<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalUsers = User::count();
        $instructors = User::where('role', 'instructor')->count();
        $students = User::where('role', 'student')->count();
        $deptHeads = User::where('role', 'dept_head')->count();
        $courses = Course::count();
        $exams = Exam::count();

        // System Overview Stats
        $attempts = ExamAttempt::count();
        $examsConducted = ExamAttempt::distinct('exam_id')->count();
        $averageScore = ExamAttempt::avg('percentage') ?? 0;
        
        // Pass Rate (assuming >= 50% is passing for stats purpose)
        $passedAttempts = ExamAttempt::where('percentage', '>=', 50)->count();
        $passRate = $attempts > 0 ? ($passedAttempts / $attempts) * 100 : 0;

        // Recent Exams
        $recentExams = Exam::latest()->take(5)->get()->map(function ($exam) {
            return [
                'title' => $exam->title,
                'course' => $exam->course_name ? $exam->course_name . ' (' . $exam->course_code . ')' : 'No Course',
                'date' => $exam->created_at->format('M d, Y'),
                'status' => $exam->status ?? 'Active',
            ];
        });

        // Chart Data (Attempts per day for last 7 days)
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('M d');
            $count = ExamAttempt::whereDate('created_at', $date->toDateString())->count();
            $chartData[] = $count;
        }

        return response()->json([
            'stats' => [
                'totalUsers' => $totalUsers,
                'instructors' => $instructors,
                'students' => $students,
                'deptHeads' => $deptHeads,
                'courses' => $courses,
                'exams' => $exams,
            ],
            'overview' => [
                'examsConducted' => $examsConducted,
                'totalAttempts' => $attempts,
                'passRate' => round($passRate, 1),
                'averageScore' => round($averageScore, 1),
            ],
            'recentExams' => $recentExams,
            'chart' => [
                'labels' => $chartLabels,
                'data' => $chartData,
            ],
            'systemStatus' => [
                'server' => 'Operational',
                'database' => 'Operational',
                'storage' => 'Operational',
                'email' => 'Operational',
            ]
        ]);
    }
}
