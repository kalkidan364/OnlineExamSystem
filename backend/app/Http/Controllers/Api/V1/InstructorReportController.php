<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorReportController extends Controller
{
    /**
     * Display report and results statistics for the instructor's single course.
     */
    public function index(Request $request): JsonResponse
    {
        $instructor = $request->user();
        $courseCode = $instructor->course_code;

        // Get all exams for this course
        $exams = Exam::where('user_id', $instructor->id)
            ->where('course_code', $courseCode)
            ->get();
        
        $examIds = $exams->pluck('id');

        // Get all attempts for these exams
        $attempts = ExamAttempt::whereIn('exam_id', $examIds)->get();

        // Calculate stats
        $totalExams = $exams->count();
        // Since we don't have direct enrollment tracking here, we define total students as the unique number of users who have taken an exam, 
        // OR we can query the users table for students in this course. Let's query the users table.
        $totalStudents = \App\Models\User::where('role', 'student')->where('course_code', $courseCode)->count();
        
        $avgScore = $attempts->count() > 0 ? round($attempts->avg('score'), 1) : 0;
        
        // Pass rate (assume passing mark is 60 or 50, let's use 60 for now)
        $passCount = $attempts->where('score', '>=', 60)->count();
        $passRate = $attempts->count() > 0 ? round(($passCount / $attempts->count()) * 100, 1) : 0;
        
        $failCount = $attempts->count() - $passCount;
        $failRate = $attempts->count() > 0 ? round(($failCount / $attempts->count()) * 100, 1) : 0;

        $topScore = $attempts->count() > 0 ? $attempts->max('score') : 0;

        // Group attempts by exam to get exam-level stats
        $examStats = $exams->map(function ($exam) use ($attempts) {
            $examAttempts = $attempts->where('exam_id', $exam->id);
            $avg = $examAttempts->count() > 0 ? round($examAttempts->avg('score'), 1) : 0;
            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'attempts' => $examAttempts->count(),
                'average_score' => $avg,
            ];
        });

        return response()->json([
            'data' => [
                'stats' => [
                    'total_exams'    => $totalExams,
                    'total_students' => $totalStudents, // Actually participated
                    'average_score'  => $avgScore,
                    'pass_rate'      => $passRate,
                    'fail_rate'      => $failRate,
                    'top_score'      => $topScore,
                ],
                'exam_stats' => $examStats, // For charts/widgets
                'recent_attempts' => $attempts->sortByDesc('created_at')->take(10)->values()->all(), // Simplified, we might want user details here, but fine for now
            ]
        ]);
    }
}
