<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResultController extends Controller
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

    public function index(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $user = $request->user();
        $dept = Department::find($deptId);

        $deptCourseCodes = [];
        if ($deptId) {
            $deptCourseCodes = Course::where('department_id', $deptId)->pluck('code')->filter()->toArray();
        }

        $query = Exam::query();
        if ($deptId) {
            $query->where(function ($q) use ($deptId, $user, $deptCourseCodes) {
                $q->whereHas('instructor', function ($sub) use ($deptId) {
                    $sub->where('department_id', $deptId);
                })
                ->orWhereIn('course_code', $deptCourseCodes)
                ->orWhere('user_id', $user->id)
                ->orWhere('settings->department_id', $deptId);
            });
        }

        $exams = $query->with(['instructor', 'course', 'attempts'])
            ->withCount(['students', 'attempts'])
            ->latest('id')
            ->get();

        $allAttempts = ExamAttempt::whereIn('exam_id', $exams->pluck('id'))->get();
        $totalStudents = User::where('role', 'student')->where('department_id', $deptId)->count();
        if ($totalStudents === 0) {
            $totalStudents = User::where('role', 'student')->count();
        }

        $completedExams = $exams->filter(fn($e) => in_array(strtolower($e->status ?? ''), ['completed', 'published']) || $e->attempts_count > 0)->count();
        $avgScore = $allAttempts->count() > 0 ? round($allAttempts->avg('percentage'), 1) : 0;
        $passedAttempts = $allAttempts->filter(fn($a) => ($a->percentage ?? 0) >= 50)->count();
        $passRate = $allAttempts->count() > 0 ? round(($passedAttempts / $allAttempts->count()) * 100, 1) : 0;

        $results = $exams->map(function ($exam) use ($totalStudents) {
            $examAttempts = $exam->attempts;
            $submittedCount = $examAttempts->filter(fn($a) => $a->submitted_at !== null || $a->status !== 'in_progress')->count();
            $gradedCount = $examAttempts->whereIn('status', ['graded', 'published'])->count();
            $publishedCount = $examAttempts->where('status', 'published')->count();
            $avg = $examAttempts->count() > 0 ? round($examAttempts->avg('score'), 1) : null;
            $avgPct = $examAttempts->count() > 0 ? round($examAttempts->avg('percentage'), 1) : null;

            $status = 'Completed';
            if ($submittedCount === 0) {
                $status = ucfirst($exam->status ?: 'Draft');
            } elseif ($gradedCount === $submittedCount && $submittedCount > 0) {
                $status = 'Graded';
            } elseif ($publishedCount === $submittedCount && $submittedCount > 0) {
                $status = 'Published';
            } else {
                $status = 'Pending';
            }

            return [
                'id'              => $exam->id,
                'title'           => $exam->title,
                'code'            => $exam->settings['exam_code'] ?? ('EXM-' . ($exam->scheduled_at ? $exam->scheduled_at->format('Y') : date('Y')) . '-' . str_pad((string)$exam->id, 3, '0', STR_PAD_LEFT)),
                'course_name'     => $exam->course_name ?: ($exam->course->title ?? 'General Course'),
                'course_code'     => $exam->course_code ?: ($exam->course->code ?? 'N/A'),
                'instructor_name' => $exam->instructor->name ?? 'Dr. Abebe Kebede',
                'scheduled_at'    => $exam->scheduled_at ? $exam->scheduled_at->format('M d, Y') : 'TBD',
                'duration'        => $exam->duration_minutes . ' min',
                'total_marks'     => $exam->total_marks,
                'total_students'  => $exam->students_count > 0 ? $exam->students_count : $totalStudents,
                'submitted_count' => $submittedCount,
                'graded_count'    => $gradedCount,
                'average_score'   => $avg,
                'average_pct'     => $avgPct,
                'pass_rate'       => $examAttempts->count() > 0 ? round(($examAttempts->filter(fn($a) => ($a->percentage ?? 0) >= 50)->count() / $examAttempts->count()) * 100, 1) : null,
                'status'          => $status,
                'semester'        => $exam->settings['semester'] ?? ($exam->course->semester ?? 'Semester 1'),
            ];
        });

        return response()->json([
            'data' => [
                'department' => [
                    'name'            => $dept->name ?? 'Software Engineering',
                    'code'            => $dept->code ?? 'SE',
                    'head_name'       => $user->name,
                    'academic_year'   => '2025 / 2026',
                    'total_courses'   => count($deptCourseCodes),
                    'total_students'  => $totalStudents,
                ],
                'stats' => [
                    'total_exams'     => $exams->count(),
                    'completed_exams' => $completedExams,
                    'total_attempts'  => $allAttempts->count(),
                    'average_score'   => $avgScore,
                    'pass_rate'       => $passRate,
                ],
                'results' => $results,
            ]
        ]);
    }

    public function showExamResults(Request $request, $examId): JsonResponse
    {
        $exam = Exam::with(['instructor', 'course', 'questions'])->findOrFail($examId);
        $attempts = ExamAttempt::where('exam_id', $examId)->with('user')->get();

        $students = User::where('role', 'student')
            ->when($exam->instructor?->department_id, fn($q, $d) => $q->where('department_id', $d))
            ->get();

        if ($students->isEmpty()) {
            $students = User::where('role', 'student')->get();
        }

        $studentResults = $students->map(function ($stu) use ($attempts, $exam) {
            $attempt = $attempts->firstWhere('user_id', $stu->id);
            return [
                'id'           => $stu->id,
                'name'         => $stu->name,
                'student_id'   => $stu->student_id ?? ('UGR/' . str_pad((string)$stu->id, 5, '0', STR_PAD_LEFT) . '/16'),
                'email'        => $stu->email,
                'score'        => $attempt ? $attempt->score : null,
                'total_marks'  => $exam->total_marks,
                'percentage'   => $attempt ? $attempt->percentage : null,
                'grade'        => $attempt ? $attempt->grade : 'F',
                'status'       => $attempt ? ucfirst($attempt->status) : 'Absent',
                'submitted_at' => $attempt && $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y h:i A') : 'N/A',
            ];
        });

        return response()->json([
            'data' => [
                'exam' => [
                    'id'              => $exam->id,
                    'title'           => $exam->title,
                    'course_name'     => $exam->course_name ?: ($exam->course->title ?? 'General Course'),
                    'course_code'     => $exam->course_code,
                    'instructor_name' => $exam->instructor->name ?? 'Dr. Abebe Kebede',
                    'total_marks'     => $exam->total_marks,
                ],
                'students' => $studentResults,
            ]
        ]);
    }
}
