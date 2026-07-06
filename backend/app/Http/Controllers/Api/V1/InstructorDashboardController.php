<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InstructorDashboardController extends Controller
{
    /**
     * Return aggregate stats for the authenticated instructor's dashboard.
     * All data is scoped to their single assigned course.
     */
    public function stats(Request $request): JsonResponse
    {
        $instructor = $request->user();

        // Scope ALL queries to this instructor's one course
        $baseQuery = Exam::where('user_id', $instructor->id)
            ->where('course_code', $instructor->course_code);

        $totalExams = (clone $baseQuery)->count();

        $upcomingExams = (clone $baseQuery)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', Carbon::now())
            ->count();

        // Total distinct students enrolled in this instructor's course exams
        $examIds = (clone $baseQuery)->pluck('id');

        $totalStudents = DB::table('exam_student')
            ->whereIn('exam_id', $examIds)
            ->distinct('user_id')
            ->count('user_id');

        // Average score across all graded attempts for this course's exams
        $averageScore = ExamAttempt::whereIn('exam_id', $examIds)
            ->where('status', 'graded')
            ->avg('percentage') ?? 0;

        return response()->json([
            'data' => [
                'course_code'    => $instructor->course_code,
                'course_name'    => $instructor->course_name,
                'totalExams'     => $totalExams,
                'upcomingExams'  => $upcomingExams,
                'totalStudents'  => $totalStudents,
                'averageScore'   => round((float) $averageScore, 1),
            ]
        ]);
    }

    /**
     * Return the 5 most recently created exams for this instructor's course.
     */
    public function recentExams(Request $request): JsonResponse
    {
        $instructor = $request->user();

        $exams = Exam::where('user_id', $instructor->id)
            ->where('course_code', $instructor->course_code)
            ->withCount('students')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'data' => $exams->map(fn($exam) => [
                'id'               => $exam->id,
                'title'            => $exam->title,
                'course_code'      => $exam->course_code,
                'course_name'      => $exam->course_name,
                'status'           => $exam->status,
                'duration_minutes' => $exam->duration_minutes,
                'total_marks'      => $exam->total_marks,
                'students_count'   => $exam->students_count,
                'scheduled_at'     => $exam->scheduled_at?->toISOString(),
                'created_at'       => $exam->created_at->toISOString(),
            ])
        ]);
    }

    /**
     * Return upcoming scheduled exams for this instructor's course.
     */
    public function upcomingExams(Request $request): JsonResponse
    {
        $instructor = $request->user();

        $exams = Exam::where('user_id', $instructor->id)
            ->where('course_code', $instructor->course_code)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', Carbon::now())
            ->withCount('students')
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $exams->map(fn($exam) => [
                'id'               => $exam->id,
                'title'            => $exam->title,
                'course_code'      => $exam->course_code,
                'course_name'      => $exam->course_name,
                'status'           => $exam->status,
                'duration_minutes' => $exam->duration_minutes,
                'total_marks'      => $exam->total_marks,
                'students_count'   => $exam->students_count,
                'scheduled_at'     => $exam->scheduled_at?->toISOString(),
                'created_at'       => $exam->created_at->toISOString(),
            ])
        ]);
    }
}
