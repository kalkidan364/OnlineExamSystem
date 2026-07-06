<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ExamAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorStudentController extends Controller
{
    /**
     * Display a listing of students enrolled in the instructor's single course.
     * Students are identified by having role='student' and matching course_code.
     */
    public function index(Request $request): JsonResponse
    {
        $instructor = $request->user();
        $courseCode = $instructor->course_code;

        $students = User::where('role', 'student')
            ->where('course_code', $courseCode)
            ->get();

        // Enrich each student with exam attempt stats
        $studentData = $students->map(function ($student) {
            $attempts = ExamAttempt::where('user_id', $student->id)->get();
            $examsTaken = $attempts->count();
            $averageScore = $examsTaken > 0 ? round($attempts->avg('score'), 1) : 0;

            return [
                'id'           => $student->id,
                'name'         => $student->name,
                'email'        => $student->email,
                'id_number'    => $student->id_number ?? 'N/A',
                'course_code'  => $student->course_code,
                'course_name'  => $student->course_name,
                'status'       => 'Active',
                'exams_taken'  => $examsTaken,
                'average_score' => $averageScore,
                'created_at'   => $student->created_at->toISOString(),
            ];
        });

        // Compute stats
        $totalStudents = $students->count();
        $activeStudents = $totalStudents; // All enrolled = active for now
        $avgScore = $studentData->count() > 0 ? round($studentData->avg('average_score'), 1) : 0;
        $topPerformers = $studentData->where('average_score', '>=', 85)->count();

        return response()->json([
            'data' => [
                'students' => $studentData,
                'stats' => [
                    'total_students'  => $totalStudents,
                    'active_students' => $activeStudents,
                    'average_score'   => $avgScore,
                    'top_performers'  => $topPerformers,
                ]
            ]
        ]);
    }
}
