<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminExamController extends Controller
{
    /**
     * Display a listing of all exams for super admin.
     */
    public function index(Request $request): JsonResponse
    {
        $exams = Exam::with(['instructor' => function($q) {
            $q->select('id', 'name', 'email', 'department_id', 'year_level');
        }, 'instructor.department:id,name'])
        ->latest()
        ->get()
        ->map(function ($exam) {
            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'course' => $exam->title,
                'courseCode' => $exam->course_code,
                'department' => $exam->instructor->department->name ?? 'Unknown',
                'year' => $exam->instructor->year_level ?? 'Unknown',
                'semester' => $exam->instructor->semester ?? 'Unknown',
                'section' => $exam->section ?? 'Both',
                'instructor' => $exam->instructor->name ?? 'Unknown',
                'instructorEmail' => $exam->instructor->email ?? '',
                'type' => $exam->course_name, 
                'totalMarks' => $exam->total_marks,
                'duration' => $exam->duration_minutes . 'm', // formatting as minutes
                'status' => $exam->status,
                'examDate' => $exam->scheduled_at ? $exam->scheduled_at->format('M d, Y') : '',
                'examTime' => $exam->scheduled_at ? $exam->scheduled_at->format('h:i A') : '',
                'createdAt' => $exam->created_at->toISOString(),
            ];
        });

        // Stats for Admin Exam Cards
        $stats = [
            'total' => $exams->count(),
            'published' => $exams->where('status', 'published')->count(),
            'draft' => $exams->where('status', 'draft')->count(),
            'scheduled' => $exams->where('status', 'scheduled')->count(),
            'completed' => $exams->where('status', 'completed')->count(),
        ];

        return response()->json([
            'data' => [
                'exams' => $exams->values(),
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return response()->json(['message' => 'Exam deleted successfully']);
    }

    /**
     * Display the specified exam with its questions and settings.
     */
    public function show(string $id): JsonResponse
    {
        $exam = Exam::with(['questions', 'instructor' => function($q) {
            $q->select('id', 'name', 'email', 'department_id', 'year_level');
        }, 'instructor.department:id,name'])->findOrFail($id);

        // Map course_name to type for consistency with index
        $exam->type = $exam->course_name;
        // The frontend expects courseCode in index, so we should map that too
        $exam->courseCode = $exam->course_code;
        $exam->course = $exam->title;
        $exam->totalMarks = $exam->total_marks;

        $settings = $exam->settings ?? [];
        $exam->shuffle_questions = $settings['shuffleQuestions'] ?? false;
        $exam->show_review_screen = $settings['showReviewScreen'] ?? false;
        $exam->shuffle_options = $settings['shuffleAnswers'] ?? false;
        $exam->allow_backtracking = $settings['allowBacktracking'] ?? false;
        $exam->show_one_question = $settings['showOneQuestionAtATime'] ?? false;
        $exam->fullscreen_mode = $settings['enableFullscreenMode'] ?? false;
        $exam->tab_monitoring = $settings['enableBrowserTabMonitoring'] ?? false;
        $exam->disable_right_click = $settings['disableRightClick'] ?? false;
        $exam->allow_calculator = $settings['allowCalculator'] ?? false;
        $exam->disable_copy_paste = $settings['disableCopyPaste'] ?? false;

        return response()->json([
            'data' => $exam
        ]);
    }
}
