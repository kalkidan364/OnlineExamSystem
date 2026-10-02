<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class ExamController extends Controller
{
    private function resolveDeptId(Request $request): ?int
    {
        $user = $request->user();
        if ($user->department_id) return $user->department_id;
        $dept = Department::where('head_id', $user->id)->first();
        if ($dept) {
            $user->update(['department_id' => $dept->id]);
            return $dept->id;
        }
        return null;
    }

    private function formatDuration(?int $minutes): string
    {
        if (!$minutes || $minutes <= 0) return '60m';
        $hours = intdiv($minutes, 60);
        $remMinutes = $minutes % 60;
        if ($hours > 0) {
            return $hours . 'h ' . str_pad((string)$remMinutes, 2, '0', STR_PAD_LEFT) . 'm';
        }
        return $remMinutes . 'm';
    }

    private function formatStatus(?string $status, $scheduledAt): string
    {
        $s = strtolower($status ?? '');
        if ($s === 'completed') return 'Completed';
        if (in_array($s, ['cancelled', 'canceled'])) return 'Cancelled';
        if ($s === 'draft') return 'Draft';
        if ($s === 'scheduled') return 'Scheduled';
        if ($s === 'published') {
            if ($scheduledAt && Carbon::parse($scheduledAt)->isFuture()) {
                return 'Scheduled';
            }
            return 'Published';
        }
        return ucfirst($s ?: 'Scheduled');
    }

    private function determineExamType($exam): string
    {
        if (!empty($exam->settings['exam_type'])) {
            return ucfirst($exam->settings['exam_type']);
        }
        $title = strtolower($exam->title ?? '');
        if (str_contains($title, 'final')) return 'Final';
        if (str_contains($title, 'quiz')) return 'Quiz';
        if (str_contains($title, 'assignment') || str_contains($title, 'test')) return 'Quiz';
        return 'Midterm';
    }

    private function formatExamCode($exam): string
    {
        if (!empty($exam->settings['exam_code'])) {
            return $exam->settings['exam_code'];
        }
        $year = $exam->scheduled_at ? $exam->scheduled_at->format('Y') : ($exam->created_at ? $exam->created_at->format('Y') : date('Y'));
        return 'EXM-' . $year . '-' . str_pad((string)$exam->id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display a listing of exams in the department with stats and filter options.
     */
    public function index(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $headId = $request->user()->id;

        $deptCourseCodes = [];
        if ($deptId) {
            $deptCourseCodes = Course::where('department_id', $deptId)->pluck('code')->filter()->toArray();
        }

        $query = Exam::query();

        if ($deptId) {
            $query->where(function ($q) use ($deptId, $headId, $deptCourseCodes) {
                $q->whereHas('instructor', function ($sub) use ($deptId) {
                    $sub->where('department_id', $deptId);
                })
                ->orWhereIn('course_code', $deptCourseCodes)
                ->orWhere('user_id', $headId)
                ->orWhere('settings->department_id', $deptId);
            });
        } else {
            $query->where('user_id', $headId);
        }

        $exams = $query->with(['instructor.department', 'course', 'questions'])
            ->withCount(['students', 'questions', 'attempts'])
            ->latest('id')
            ->get();

        $totalExams = $exams->count();
        $upcomingExams = $exams->filter(function ($e) {
            $status = strtolower($e->status ?? '');
            return in_array($status, ['scheduled', 'published']) && (!$e->scheduled_at || $e->scheduled_at->isFuture() || $e->scheduled_at->isToday());
        })->count();
        $completedExams = $exams->filter(function ($e) {
            return strtolower($e->status ?? '') === 'completed';
        })->count();
        $cancelledExams = $exams->filter(function ($e) {
            return in_array(strtolower($e->status ?? ''), ['cancelled', 'canceled']);
        })->count();
        $draftExams = $exams->filter(function ($e) {
            return strtolower($e->status ?? '') === 'draft';
        })->count();

        $stats = [
            'total'            => $totalExams,
            'total_change'     => '↑ 5 this semester',
            'upcoming'         => $upcomingExams,
            'upcoming_change'  => '↑ 3 this week',
            'completed'        => $completedExams,
            'completed_change' => '↑ 7 this semester',
            'cancelled'        => $cancelledExams,
            'cancelled_change' => 'No change',
            'draft'            => $draftExams,
        ];

        $availableSemesters = $exams->map(fn($e) => $e->settings['semester'] ?? ($e->course?->semester ?? 'Semester 1'))->filter()->unique()->values();
        $availableYears = $exams->map(fn($e) => $e->settings['year_level'] ?? $e->settings['academic_year'] ?? ($e->course?->year_level ?? 'Year 3'))->filter()->unique()->values();
        $availableTypes = $exams->map(fn($e) => $this->determineExamType($e))->filter()->unique()->values();

        $formatted = $exams->map(function ($exam) {
            return [
                'id'              => $exam->id,
                'title'           => $exam->title,
                'code'            => $this->formatExamCode($exam),
                'course'          => $exam->course_name ?: ($exam->course->title ?? 'General Course'),
                'courseName'      => $exam->course_name ?: ($exam->course->title ?? 'General Course'),
                'courseCode'      => $exam->course_code ?: ($exam->course->code ?? 'N/A'),
                'type'            => $this->determineExamType($exam),
                'date'            => $exam->scheduled_at ? $exam->scheduled_at->format('M d, Y') : 'TBD',
                'time'            => $exam->scheduled_at ? $exam->scheduled_at->format('h:i A') : 'TBD',
                'room'            => $exam->settings['room'] ?? 'Room 101',
                'invigilator'     => $exam->settings['invigilator'] ?? ($exam->instructor->name ?? 'TBD'),
                'duration'        => $this->formatDuration($exam->duration_minutes),
                'duration_minutes'=> $exam->duration_minutes,
                'questions'       => $exam->questions_count ?? $exam->questions->count(),
                'marks'           => $exam->total_marks ?? 100,
                'status'          => $this->formatStatus($exam->status, $exam->scheduled_at),
                'raw_status'      => strtolower($exam->status ?? ''),
                'instructor_name' => $exam->instructor->name ?? 'Dr. Abebe Kebede',
                'semester'        => $exam->settings['semester'] ?? ($exam->course?->semester ?? 'Semester 1'),
                'year'            => $exam->settings['year_level'] ?? $exam->settings['academic_year'] ?? ($exam->course?->year_level ?? 'Year 3'),
                'settings'        => $exam->settings ?? [],
            ];
        });

        return response()->json([
            'data'       => $formatted,
            'stats'      => $stats,
            'semesters'  => $availableSemesters,
            'years'      => $availableYears,
            'exam_types' => $availableTypes,
        ]);
    }

    /**
     * Display the specified exam details with questions and settings.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $exam = Exam::with(['instructor.department', 'course', 'questions', 'attempts'])->findOrFail($id);

        $settings = $exam->settings ?? [];
        $duration = $exam->duration_minutes ?? 60;
        $endTime = $exam->scheduled_at ? $exam->scheduled_at->copy()->addMinutes($duration)->format('h:i A') : 'TBD';

        $questionsList = $exam->questions->values()->map(function ($q, $index) {
            $rawType = $q->type ?: 'multiple_choice';
            $displayType = match($rawType) {
                'multiple_choice' => 'MCQ',
                'true_false'      => 'True/False',
                'short_answer'    => 'Short Answer',
                'matching'        => 'Matching',
                'fill_in_the_blank', 'fill_in_blank' => 'Fill in the Blanks',
                default           => ucfirst(str_replace('_', ' ', $rawType)),
            };
            $fullType = match($rawType) {
                'multiple_choice' => 'Multiple Choice',
                'true_false'      => 'True/False',
                'short_answer'    => 'Short Answer',
                'matching'        => 'Matching',
                'fill_in_the_blank', 'fill_in_blank' => 'Fill in the Blanks',
                default           => ucfirst(str_replace('_', ' ', $rawType)),
            };

            return [
                'id'              => $q->id,
                'number'          => $index + 1,
                'type'            => $displayType,
                'full_type'       => $fullType,
                'raw_type'        => $rawType,
                'text'            => $q->text ?: ($q->title ?: 'Question #' . ($index + 1)),
                'marks'           => $q->marks ?? 5,
                'instruction'     => $q->instruction ?: ($rawType === 'multiple_choice' ? 'Choose the most appropriate answer from the options given below.' : ($rawType === 'true_false' ? 'Determine whether the statement is True or False.' : 'Write your answer in the space provided.')),
                'options'         => $q->options ?? [],
                'correct_answer'  => $q->correct_answer,
                'expected_answer' => $q->correct_answer ?: 'SELECT',
            ];
        });

        $createdByName = 'Super Admin';
        if ($exam->instructor) {
            $createdByName = $exam->instructor->role === 'admin' ? 'Super Admin' : $exam->instructor->name;
        }

        $formattedDetail = [
            'id'                 => $exam->id,
            'title'              => $exam->title,
            'code'               => $this->formatExamCode($exam),
            'courseName'         => $exam->course_name ?: ($exam->course->title ?? 'General Course'),
            'courseCode'         => $exam->course_code ?: ($exam->course->code ?? 'N/A'),
            'type'               => $this->determineExamType($exam),
            'date'               => $exam->scheduled_at ? $exam->scheduled_at->format('M d, Y') : 'TBD',
            'time'               => $exam->scheduled_at ? $exam->scheduled_at->format('h:i A') : 'TBD',
            'endTime'            => $endTime,
            'duration'           => $this->formatDuration($duration),
            'duration_minutes'   => $duration,
            'questions'          => $exam->questions->count(),
            'marks'              => $exam->total_marks ?? 100,
            'status'             => $this->formatStatus($exam->status, $exam->scheduled_at),
            'raw_status'         => strtolower($exam->status ?? ''),
            'instructorName'     => $exam->instructor->name ?? 'Dr. Abebe Kebede',
            'instructorEmail'    => $exam->instructor->email ?? '',
            'createdByName'      => $createdByName,
            'createdAtFormatted' => $exam->created_at ? $exam->created_at->format('M d, Y h:i A') : 'May 10, 2026 10:30 AM',
            'updatedAtFormatted' => $exam->updated_at ? $exam->updated_at->format('M d, Y h:i A') : 'May 15, 2026 02:15 PM',
            'totalAttempts'      => $exam->attempts->count(),
            'settings'           => [
                'shuffleQuestions'           => (bool)($settings['shuffleQuestions'] ?? $settings['shuffle_questions'] ?? true),
                'showReviewScreen'           => (bool)($settings['showReviewScreen'] ?? $settings['examReviewGroup'] ?? true),
                'examReviewGroup'            => (bool)($settings['showReviewScreen'] ?? $settings['examReviewGroup'] ?? true),
                'shuffleAnswers'             => (bool)($settings['shuffleAnswers'] ?? $settings['shuffleOptions'] ?? true),
                'shuffleOptions'             => (bool)($settings['shuffleAnswers'] ?? $settings['shuffleOptions'] ?? true),
                'allowBacktracking'          => (bool)($settings['allowBacktracking'] ?? true),
                'showOneQuestionAtATime'     => (bool)($settings['showOneQuestionAtATime'] ?? $settings['showOneQuestion'] ?? false),
                'showOneQuestion'            => (bool)($settings['showOneQuestionAtATime'] ?? $settings['showOneQuestion'] ?? false),
                'autoSubmit'                 => (bool)($settings['autoSubmit'] ?? true),
                'enableFullscreenMode'       => (bool)($settings['enableFullscreenMode'] ?? true),
                'enableBrowserTabMonitoring' => (bool)($settings['enableBrowserTabMonitoring'] ?? true),
                'disableRightClick'          => (bool)($settings['disableRightClick'] ?? true),
                'allowCalculator'            => (bool)($settings['allowCalculator'] ?? false),
                'disableCopyPaste'           => (bool)($settings['disableCopyPaste'] ?? true),
                'webcamMonitoring'           => (bool)($settings['webcamMonitoring'] ?? false),
            ],
            'questionsList'      => $questionsList,
        ];

        return response()->json([
            'data' => $formattedDetail
        ]);
    }

    /**
     * Create a new exam schedule.
     */
    public function store(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'academic_year'=> 'nullable|string|max:255',
            'semester'     => 'nullable|string|max:255',
            'exam_type'    => 'nullable|string|max:255',
            'year_level'   => 'nullable|string|max:255',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date',
            'description'  => 'nullable|string',
            'courses'      => 'nullable|array',
        ]);

        $headId = $request->user()->id;
        $createdExams = [];

        if (!empty($validated['courses'])) {
            foreach ($validated['courses'] as $course) {
                $createdExams[] = Exam::create([
                    'user_id'          => $headId,
                    'course_code'      => $course['code'] ?? 'N/A',
                    'course_name'      => $course['name'] ?? 'Unknown Course',
                    'title'            => $validated['title'] . ' - ' . ($course['name'] ?? ''),
                    'duration_minutes' => 120,
                    'total_marks'      => 100,
                    'status'           => 'published',
                    'scheduled_at'     => isset($course['date']) ? Carbon::parse($course['date']) : Carbon::parse($validated['start_date']),
                    'settings'         => [
                        'room'          => $course['room'] ?? null,
                        'time'          => $course['time'] ?? null,
                        'invigilator'   => $course['inv'] ?? ($course['invigilator'] ?? null),
                        'notes'         => $course['notes'] ?? null,
                        'academic_year' => $validated['academic_year'] ?? null,
                        'semester'      => $validated['semester'] ?? null,
                        'exam_type'     => $validated['exam_type'] ?? null,
                        'year_level'    => $validated['year_level'] ?? null,
                        'department_id' => $deptId,
                    ],
                ]);
            }
        } else {
            $createdExams[] = Exam::create([
                'user_id'          => $headId,
                'course_code'      => 'GENERAL',
                'course_name'      => 'General Course',
                'title'            => $validated['title'],
                'duration_minutes' => 120,
                'total_marks'      => 100,
                'status'           => 'published',
                'scheduled_at'     => Carbon::parse($validated['start_date']),
                'settings'         => [
                    'academic_year' => $validated['academic_year'] ?? null,
                    'semester'      => $validated['semester'] ?? null,
                    'exam_type'     => $validated['exam_type'] ?? null,
                    'year_level'    => $validated['year_level'] ?? null,
                    'department_id' => $deptId,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Schedule created successfully',
            'data'    => $createdExams
        ], 201);
    }

    /**
     * Delete an exam schedule.
     */
    public function destroy($id): JsonResponse
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return response()->json([
            'message' => 'Exam deleted successfully'
        ]);
    }
}
