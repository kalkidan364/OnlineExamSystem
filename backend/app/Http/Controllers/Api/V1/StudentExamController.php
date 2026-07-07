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

        // Count upcoming published exams for student's course that haven't been attempted
        $attemptedExamIds = ExamAttempt::where('user_id', $student->id)->pluck('exam_id');

        $upcomingCount = Exam::where('status', 'published')
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
     * Separates into "active" (in-progress attempt) and "upcoming" (not yet attempted).
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        // Get all published exams
        $exams = Exam::where('status', 'published')
            ->with('instructor:id,name')
            ->latest('scheduled_at')
            ->get();

        // Get all attempts by this student
        $attempts = ExamAttempt::where('user_id', $student->id)
            ->get()
            ->keyBy('exam_id');

        $activeExam = null;
        $upcomingExams = [];
        $completedExamIds = [];

        foreach ($exams as $exam) {
            $attempt = $attempts->get($exam->id);

            if ($attempt && $attempt->status === 'in_progress') {
                // Student has an active in-progress attempt
                $activeExam = [
                    'id'              => $exam->id,
                    'attempt_id'      => $attempt->id,
                    'courseCode'       => $exam->course_code,
                    'courseName'      => $exam->course_name,
                    'examTitle'       => $exam->title,
                    'instructor'      => $exam->instructor->name ?? 'Unknown',
                    'date'            => $exam->scheduled_at ? $exam->scheduled_at->format('M d, Y') : now()->format('M d, Y'),
                    'time'            => $exam->scheduled_at ? $exam->scheduled_at->format('h:i A') : '',
                    'durationMinutes' => $exam->duration_minutes,
                    'totalMarks'      => $exam->total_marks,
                    'started_at'      => $attempt->started_at->toISOString(),
                ];
            } elseif ($attempt && in_array($attempt->status, ['submitted', 'graded'])) {
                // Already completed — skip from upcoming
                $completedExamIds[] = $exam->id;
            } else {
                // Not yet attempted — show as upcoming
                $upcomingExams[] = [
                    'id'              => $exam->id,
                    'courseCode'       => $exam->course_code,
                    'courseName'      => $exam->course_name,
                    'instructor'      => $exam->instructor->name ?? 'Unknown',
                    'examType'        => $exam->title,
                    'scheduledDate'   => $exam->scheduled_at ? $exam->scheduled_at->format('M d, Y') : 'TBD',
                    'startTime'       => $exam->scheduled_at ? $exam->scheduled_at->format('h:i A') : 'TBD',
                    'durationMinutes' => $exam->duration_minutes,
                    'totalMarks'      => $exam->total_marks,
                    'totalQuestions'  => $exam->questions()->count(),
                    'status'          => 'Ready',
                ];
            }
        }

        return response()->json([
            'data' => [
                'active_exam'    => $activeExam,
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

        $submittedAnswers = $validated['answers']; // { questionId: selectedAnswer }
        $questions = $exam->questions()->get();

        $totalScore = 0;
        $totalPossible = 0;
        $questionsReview = [];

        foreach ($questions as $q) {
            $studentAnswer = $submittedAnswers[$q->id] ?? null;
            $isCorrect = false;
            $canAutoGrade = in_array($q->type, ['multiple_choice', 'true_false']);

            if ($canAutoGrade && $studentAnswer !== null) {
                // For MCQ: compare selected answer with correct_answer
                $isCorrect = strtolower(trim((string)$studentAnswer)) === strtolower(trim((string)$q->correct_answer));
                if ($isCorrect) {
                    $totalScore += $q->marks;
                }
            }

            $totalPossible += $q->marks;

            $questionsReview[] = [
                'question_id'   => $q->id,
                'questionText'  => $q->text,
                'type'          => $q->type,
                'studentAnswer' => $studentAnswer,
                'correctAnswer' => $q->correct_answer,
                'isCorrect'     => $isCorrect,
                'marks'         => $q->marks,
                'explanation'   => $canAutoGrade
                    ? ($isCorrect ? 'Correct!' : 'The correct answer is: ' . $q->correct_answer)
                    : 'This question requires manual grading.',
            ];
        }

        // Calculate percentage and grade
        $percentage = $totalPossible > 0 ? round(($totalScore / $totalPossible) * 100, 2) : 0;
        $grade = $this->calculateGrade($percentage);

        // Update the attempt
        $attempt->update([
            'score'        => $totalScore,
            'total_marks'  => $totalPossible,
            'percentage'   => $percentage,
            'grade'        => $grade,
            'status'       => 'submitted',
            'answers'      => $submittedAnswers,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'data' => [
                'attempt_id'      => $attempt->id,
                'score'           => $totalScore,
                'total_marks'     => $totalPossible,
                'percentage'      => $percentage,
                'grade'           => $grade,
                'exam_title'      => $exam->title,
                'course_code'     => $exam->course_code,
                'course_name'     => $exam->course_name,
                'questionsReview' => $questionsReview,
            ]
        ]);
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
