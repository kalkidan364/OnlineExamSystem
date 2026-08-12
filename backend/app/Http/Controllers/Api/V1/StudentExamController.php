<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentExamController extends Controller
{
    /**
     * Dashboard stats for the student.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $student = $request->user();

        // Count completed attempts
        $completedAttempts = ExamAttempt::where('user_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        // Average score across all submitted attempts
        $avgScore = ExamAttempt::where('user_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->whereNotNull('percentage')
            ->avg('percentage');

        // Count upcoming published exams for student's department & year_level that haven't been attempted
        $attemptedExamIds = ExamAttempt::where('user_id', $student->id)->pluck('exam_id');

        $upcomingCount = Exam::where('status', 'published')
            ->whereHas('instructor', function ($query) use ($student) {
                $query->where('department_id', $student->department_id)
                      ->where('year_level', $student->year_level);
            })
            ->whereNotIn('id', $attemptedExamIds)
            ->count();

        return response()->json([
            'data' => [
                'completed_exams' => $completedAttempts,
                'upcoming_exams'  => $upcomingCount,
                'average_score'   => round($avgScore ?? 0, 1),
            ]
        ]);
    }

    /**
     * List all published exams for the student's course.
     * Returns ALL non-submitted exams in "upcoming_exams" with an attemptStatus field.
     * The frontend Ready Card decides what to show based on time window + attemptStatus.
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        // Get all published exams for the student's department & year_level
        $exams = Exam::where('status', 'published')
            ->whereHas('instructor', function ($query) use ($student) {
                $query->where('department_id', $student->department_id)
                      ->where('year_level', $student->year_level);
            })
            ->with('instructor:id,name')
            ->latest('scheduled_at')
            ->get();

        // Get all attempts by this student, keyed by exam_id
        $attempts = ExamAttempt::where('user_id', $student->id)
            ->get()
            ->keyBy('exam_id');

        $upcomingExams = [];

        foreach ($exams as $exam) {
            $attempt = $attempts->get($exam->id);
            $attemptStatus = $attempt ? $attempt->status : null; // null | 'in_progress' | 'submitted' | 'graded'

            // Exclude exams the student has already submitted/graded (fully completed)
            if ($attemptStatus === 'submitted' || $attemptStatus === 'graded') {
                continue;
            }

            $scheduledIso = $exam->scheduled_at ? $exam->scheduled_at->toISOString() : null;

            $upcomingExams[] = [
                'id'              => $exam->id,
                'courseCode'      => $exam->course_code,
                'courseName'      => $exam->course_name,
                'instructor'      => $exam->instructor->name ?? 'Unknown',
                'examType'        => $exam->title,
                'scheduledAt'     => $scheduledIso,
                'scheduledDate'   => $scheduledIso, // legacy alias
                'startTime'       => $exam->scheduled_at ? $exam->scheduled_at->format('h:i A') : 'TBD',
                'durationMinutes' => $exam->duration_minutes,
                'totalMarks'      => $exam->total_marks,
                'totalQuestions'  => $exam->questions()->count(),
                'status'          => 'Upcoming',
                // Attempt tracking — allows card to show Continue vs Start
                'attemptStatus'   => $attemptStatus, // null or 'in_progress'
                'attemptId'       => $attempt ? $attempt->id : null,
                'attemptStartedAt' => ($attempt && $attempt->started_at) ? $attempt->started_at->toISOString() : null,
            ];
        }

        // active_exam is now derived from upcoming_exams (in_progress) for backward compat
        $activeExamData = null;
        foreach ($upcomingExams as $e) {
            if ($e['attemptStatus'] === 'in_progress') {
                $activeExamData = [
                    'id'              => $e['id'],
                    'attempt_id'      => $e['attemptId'],
                    'courseCode'      => $e['courseCode'],
                    'courseName'      => $e['courseName'],
                    'examTitle'       => $e['examType'],
                    'instructor'      => $e['instructor'],
                    'date'            => $e['scheduledAt'] ? Carbon::parse($e['scheduledAt'])->format('M d, Y') : now()->format('M d, Y'),
                    'time'            => $e['startTime'],
                    'durationMinutes' => $e['durationMinutes'],
                    'totalMarks'      => $e['totalMarks'],
                    'started_at'      => $e['attemptStartedAt'],
                ];
                break;
            }
        }

        return response()->json([
            'data' => [
                'active_exam'    => $activeExamData,
                'upcoming_exams' => $upcomingExams,
            ]
        ]);
    }

    /**
     * Start an exam attempt. Returns questions WITHOUT correct answers.
     */
    public function start(Request $request, Exam $exam): JsonResponse
    {
        $student = $request->user();

        // Verify exam is published
        if ($exam->status !== 'published') {
            return response()->json(['message' => 'This exam is not available.'], 403);
        }

        // Enforce time window: student cannot start before scheduled_at or after the exam ends
        if ($exam->scheduled_at) {
            $now = Carbon::now();
            $examStart = $exam->scheduled_at;
            $examEnd = $exam->scheduled_at->copy()->addMinutes($exam->duration_minutes);

            if ($now->lt($examStart)) {
                return response()->json([
                    'message' => 'This exam has not started yet. It starts at ' . $examStart->format('g:i A') . '.'
                ], 403);
            }

            if ($now->gte($examEnd)) {
                return response()->json([
                    'message' => 'This exam has already ended.'
                ], 403);
            }
        }

        // Check if student already has an attempt
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $student->id)
            ->first();

        if ($existingAttempt && $existingAttempt->status === 'in_progress') {
            // Resume existing attempt — return questions again
            $questions = $exam->questions()->get()->map(fn($q) => [
                'id'          => $q->id,
                'instruction' => $q->instruction,
                'text'        => $q->text,
                'type'        => $this->mapQuestionType($q->type),
                'options'     => $q->type !== 'matching' ? ($q->options ?? []) : [],
                'pairs'       => $q->type === 'matching' ? collect($q->options ?? [])->map(fn($p) => ['left' => $p['left'] ?? '', 'right' => $p['right'] ?? ''])->values()->toArray() : null,
                'columnA'     => $q->type === 'matching' ? (($q->options[0]['columnA'] ?? null) ?: 'Column A') : null,
                'columnB'     => $q->type === 'matching' ? (($q->options[0]['columnB'] ?? null) ?: 'Column B') : null,
                'marks'       => $q->marks,
            ]);

            return response()->json([
                'data' => [
                    'attempt_id'      => $existingAttempt->id,
                    'exam_title'      => $exam->title,
                    'course_code'     => $exam->course_code,
                    'course_name'     => $exam->course_name,
                    'duration_minutes' => $exam->duration_minutes,
                    'total_marks'     => $exam->total_marks,
                    'started_at'      => $existingAttempt->started_at->toISOString(),
                    'settings'        => $exam->settings ?? [],
                    'questions'       => $questions,
                ]
            ]);
        }

        if ($existingAttempt && in_array($existingAttempt->status, ['submitted', 'graded'])) {
            return response()->json(['message' => 'You have already completed this exam.'], 409);
        }

        // Create new attempt
        $attempt = ExamAttempt::create([
            'exam_id'     => $exam->id,
            'user_id'     => $student->id,
            'total_marks' => $exam->total_marks,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);

        // Return questions WITHOUT correct_answer
        $questions = $exam->questions()->get()->map(fn($q) => [
            'id'          => $q->id,
            'instruction' => $q->instruction,
            'text'        => $q->text,
            'type'        => $this->mapQuestionType($q->type),
            'options'     => $q->type !== 'matching' ? ($q->options ?? []) : [],
            'pairs'       => $q->type === 'matching' ? collect($q->options ?? [])->map(fn($p) => ['left' => $p['left'] ?? '', 'right' => $p['right'] ?? ''])->values()->toArray() : null,
            'columnA'     => $q->type === 'matching' ? (($q->options[0]['columnA'] ?? null) ?: 'Column A') : null,
            'columnB'     => $q->type === 'matching' ? (($q->options[0]['columnB'] ?? null) ?: 'Column B') : null,
            'marks'       => $q->marks,
        ]);

        return response()->json([
            'data' => [
                'attempt_id'       => $attempt->id,
                'exam_title'       => $exam->title,
                'course_code'      => $exam->course_code,
                'course_name'      => $exam->course_name,
                'duration_minutes' => $exam->duration_minutes,
                'total_marks'      => $exam->total_marks,
                'started_at'       => $attempt->started_at->toISOString(),
                'settings'         => $exam->settings ?? [],
                'questions'        => $questions,
            ]
        ], 201);
    }

    /**
     * Submit exam answers. Auto-grades MCQ and True/False.
     */
    public function submit(Request $request, Exam $exam): JsonResponse
    {
        $student = $request->user();

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $student->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $submittedAnswers = $validated['answers'];
        $questions = $exam->questions()->get();

        // Question types that can be auto-graded
        $autoGradeTypes = ['multiple_choice', 'true_false', 'matching'];

        $autoScore   = 0;   // marks earned from auto-graded questions
        $autoTotal   = 0;   // total marks available from auto-graded questions
        $pendingTotal = 0;  // total marks from manual-graded questions (short_answer, fill_blank)

        // Per-type score tracking for the breakdown
        $typeBreakdown = [];
        $questionsReview = [];

        foreach ($questions as $q) {
            $studentAnswer = $submittedAnswers[$q->id] ?? null;
            $isAutoGrade   = in_array($q->type, $autoGradeTypes);
            $isCorrect     = false;
            $earnedMarks   = 0;

            if ($isAutoGrade) {
                $autoTotal += $q->marks;

                if ($studentAnswer !== null && $studentAnswer !== '') {
                    if ($q->type === 'matching') {
                        $isCorrect = $this->gradeMatching($studentAnswer, $q->options ?? []);
                    } else {
                        // MCQ / True-False: letter comparison (A, B, C... or A/B for T/F)
                        $isCorrect = strtolower(trim((string)$studentAnswer))
                                  === strtolower(trim((string)$q->correct_answer));
                    }
                }

                if ($isCorrect) {
                    $earnedMarks  = $q->marks;
                    $autoScore   += $q->marks;
                }

                // Build per-type totals for the breakdown
                $typeKey = $q->type;
                if (!isset($typeBreakdown[$typeKey])) {
                    $typeBreakdown[$typeKey] = ['earned' => 0, 'total' => 0];
                }
                $typeBreakdown[$typeKey]['earned'] += $earnedMarks;
                $typeBreakdown[$typeKey]['total']  += $q->marks;

                $questionsReview[] = [
                    'question_id'   => $q->id,
                    'questionText'  => $q->text,
                    'type'          => $q->type,
                    'studentAnswer' => $studentAnswer,
                    'correctAnswer' => $q->correct_answer,
                    'isCorrect'     => $isCorrect,
                    'marks'         => $q->marks,
                    'earnedMarks'   => $earnedMarks,
                    'gradingStatus' => 'graded',
                    'explanation'   => $isCorrect
                        ? 'Correct!'
                        : 'The correct answer is: ' . $q->correct_answer,
                ];
            } else {
                // Manual grading — short_answer, fill_blank, essay, etc.
                $pendingTotal += $q->marks;

                $questionsReview[] = [
                    'question_id'   => $q->id,
                    'questionText'  => $q->text,
                    'type'          => $q->type,
                    'studentAnswer' => $studentAnswer,
                    'correctAnswer' => null,
                    'isCorrect'     => null,   // null = not yet graded
                    'marks'         => $q->marks,
                    'earnedMarks'   => null,   // null = pending
                    'gradingStatus' => 'pending',
                    'explanation'   => 'This answer will be marked by your instructor.',
                ];
            }
        }

        $totalPossible    = $autoTotal + $pendingTotal;
        $hasPendingMarks  = $pendingTotal > 0;

        // Percentage is calculated on the auto-graded portion only when pending marks exist
        // so as not to artificially deflate the score before instructor grading
        $percentageBase   = $autoTotal > 0 ? $autoTotal : $totalPossible;
        $percentage       = $percentageBase > 0 ? round(($autoScore / $percentageBase) * 100, 2) : 0;
        $grade            = $hasPendingMarks ? 'Pending' : $this->calculateGrade($percentage);

        // Status: submitted if manual questions exist (instructor must grade)
        // graded immediately if everything was auto-gradeable
        $status = $hasPendingMarks ? 'submitted' : 'graded';

        $attempt->update([
            'score'        => $autoScore,
            'total_marks'  => $totalPossible,
            'percentage'   => $percentage,
            'grade'        => $grade,
            'status'       => $status,
            'answers'      => $submittedAnswers,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'data' => [
                'attempt_id'      => $attempt->id,
                // Auto-graded scores
                'auto_score'      => $autoScore,
                'auto_total'      => $autoTotal,
                // Pending manual marks
                'pending_total'   => $pendingTotal,
                'has_pending'     => $hasPendingMarks,
                // Overall
                'score'           => $autoScore,
                'total_marks'     => $totalPossible,
                'percentage'      => $hasPendingMarks ? null : $percentage,
                'grade'           => $grade,
                'status'          => $status,
                // Type-level breakdown e.g. {"multiple_choice":{"earned":10,"total":20},...}
                'type_breakdown'  => $typeBreakdown,
                // Exam meta
                'exam_title'      => $exam->title,
                'course_code'     => $exam->course_code,
                'course_name'     => $exam->course_name,
                'questionsReview' => $questionsReview,
            ]
        ]);
    }

    /**
     * Grade a matching question.
     * Student answer format: "0:rightValue,1:rightValue,2:rightValue"
     * Pairs (from DB options): [{left: "...", right: "..."}, ...]
     * Returns true only if ALL pairs are correctly matched.
     */
    private function gradeMatching(string $studentAnswer, array $pairs): bool
    {
        if (empty($pairs) || empty(trim($studentAnswer))) {
            return false;
        }

        // Parse student selections: "0:val,1:val" => [0 => 'val', 1 => 'val']
        $selections = [];
        foreach (explode(',', $studentAnswer) as $part) {
            $pieces = explode(':', $part, 2);
            if (count($pieces) === 2) {
                $selections[(int)$pieces[0]] = trim($pieces[1]);
            }
        }

        // Check every pair
        foreach ($pairs as $i => $pair) {
            $correctRight    = strtolower(trim($pair['right'] ?? ''));
            $studentSelected = strtolower($selections[$i] ?? '');
            if ($studentSelected !== $correctRight) {
                return false;
            }
        }

        return !empty($selections);
    }

    /**
     * Get all completed exam results for the student.
     */
    public function results(Request $request): JsonResponse
    {
        $student = $request->user();

        $attempts = ExamAttempt::where('user_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->with(['exam', 'exam.questions'])
            ->latest('submitted_at')
            ->get();

        $results = $attempts->map(function ($attempt) {
            $exam = $attempt->exam;

            // Build question review from stored answers
            $review = [];
            if ($exam && $exam->questions) {
                $storedAnswers = $attempt->answers ?? [];
                foreach ($exam->questions as $q) {
                    $studentAnswer = $storedAnswers[$q->id] ?? null;
                    $canAutoGrade = in_array($q->type, ['multiple_choice', 'true_false']);
                    $isCorrect = $canAutoGrade
                        ? strtolower(trim((string)$studentAnswer)) === strtolower(trim((string)$q->correct_answer))
                        : false;

                    $review[] = [
                        'questionText'  => $q->text,
                        'studentAnswer' => $studentAnswer ?? 'Not answered',
                        'correctAnswer' => $q->correct_answer ?? 'N/A',
                        'explanation'   => $isCorrect ? 'Correct!' : 'The correct answer is: ' . ($q->correct_answer ?? 'N/A'),
                        'isCorrect'     => $isCorrect,
                    ];
                }
            }

            return [
                'id'              => $attempt->id,
                'courseCode'       => $exam->course_code ?? '',
                'courseName'      => $exam->course_name ?? '',
                'examTitle'       => $exam->title ?? '',
                'score'           => $attempt->score ?? 0,
                'totalMarks'      => $attempt->total_marks,
                'percentage'      => (float) ($attempt->percentage ?? 0),
                'grade'           => $attempt->grade ?? 'N/A',
                'status'          => $attempt->percentage >= 50 ? 'Passed' : 'Failed',
                'completedDate'   => $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y') : '',
                'questionsReview' => $review,
            ];
        });

        return response()->json([
            'data' => $results,
        ]);
    }

    /**
     * Map backend question types to frontend-friendly types.
     */
    private function mapQuestionType(string $type): string
    {
        return match ($type) {
            'multiple_choice' => 'multiple-choice',
            'true_false'      => 'true_false',
            'fill_blank'      => 'fill_blank',
            'matching'        => 'matching',
            'multiple_true_false' => 'multiple_true_false',
            'short_answer'    => 'text',
            'essay'           => 'text',
            default           => $type,
        };
    }

    /**
     * Calculate letter grade from percentage.
     */
    private function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 95 => 'A+',
            $percentage >= 90 => 'A',
            $percentage >= 85 => 'A-',
            $percentage >= 80 => 'B+',
            $percentage >= 75 => 'B',
            $percentage >= 70 => 'B-',
            $percentage >= 65 => 'C+',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default           => 'F',
        };
    }
}
