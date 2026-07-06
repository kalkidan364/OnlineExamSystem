<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InstructorExamController extends Controller
{
    /**
     * Display a listing of the exams for the instructor's assigned course.
     */
    public function index(Request $request): JsonResponse
    {
        $instructor = $request->user();

        // Scope to instructor's single assigned course
        $exams = Exam::where('user_id', $instructor->id)
            ->withCount('students')
            ->latest()
            ->get();

        // Stats for ExamStatCards
        $totalExams = $exams->count();
        $publishedExams = $exams->where('status', 'published')->count();
        $draftExams = $exams->where('status', 'draft')->count();
        $completedExams = $exams->where('status', 'completed')->count();

        return response()->json([
            'data' => [
                'exams' => $exams->map(fn($exam) => [
                    'id'               => $exam->id,
                    'title'            => $exam->title,
                    'course_code'      => $exam->course_code,
                    'course_name'      => $exam->course_name,
                    'status'           => $exam->status,
                    'duration_minutes' => $exam->duration_minutes,
                    'total_marks'      => $exam->total_marks,
                    'students_count'   => $exam->students_count,
                    'scheduled_at'     => $exam->scheduled_at?->toISOString(),
                    'settings'         => $exam->settings,
                    'created_at'       => $exam->created_at->toISOString(),
                ]),
                'stats' => [
                    'total'     => $totalExams,
                    'published' => $publishedExams,
                    'draft'     => $draftExams,
                    'completed' => $completedExams,
                ]
            ]
        ]);
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $instructor = $request->user();

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'course_code'      => 'nullable|string|max:50',
            'course_name'      => 'nullable|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks'      => 'required|integer|min:1',
            'status'           => 'required|in:draft,published,scheduled,completed',
            'scheduled_at'     => 'nullable|date',
            'settings'         => 'nullable|array',
            'questions'        => 'nullable|array',
        ]);

        $exam = Exam::create([
            'user_id'          => $instructor->id,
            'course_code'      => $validated['course_code'] ?? $instructor->course_code ?? 'GENERAL',
            'course_name'      => $validated['course_name'] ?? $instructor->course_name ?? 'General Course',
            'title'            => $validated['title'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks'      => $validated['total_marks'],
            'status'           => $validated['status'],
            'scheduled_at'     => $validated['scheduled_at'] ? Carbon::parse($validated['scheduled_at']) : null,
            'settings'         => $validated['settings'] ?? null,
        ]);

        \Log::info('Exam Creation Payload: ', $request->all());

        if ($request->has('questions') && is_array($request->input('questions'))) {
            \Log::info('Questions array found: ', $request->input('questions'));
            foreach ($request->input('questions') as $q) {
                $exam->questions()->create([
                    'type'           => $q['type'] ?? 'multiple_choice',
                    'text'           => $q['text'] ?? 'Untitled Question',
                    'options'        => collect($q['options'] ?? [])->map(function ($opt) { return is_string($opt) ? ['text' => $opt] : $opt; })->toArray(),
                    'correct_answer' => $q['correct_answer'] ?? null,
                    'marks'          => $q['marks'] ?? 5,
                    'difficulty'     => 'Medium',
                    'status'         => 1,
                ]);
            }
        } else {
            \Log::warning('No questions array found in request.');
        }

        return response()->json([
            'message' => 'Exam created successfully',
            'data'    => $exam
        ], 201);
    }

    /**
     * Display the specified exam.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $instructor = $request->user();
        
        $exam = Exam::where('user_id', $instructor->id)
            ->where('course_code', $instructor->course_code)
            ->where('id', $id)
            ->withCount('students')
            ->firstOrFail();

        return response()->json([
            'data' => $exam
        ]);
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $instructor = $request->user();

        $exam = Exam::where('user_id', $instructor->id)
            ->where('course_code', $instructor->course_code)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'duration_minutes' => 'sometimes|integer|min:1',
            'total_marks'      => 'sometimes|integer|min:1',
            'status'           => 'sometimes|in:draft,published,scheduled,completed',
            'scheduled_at'     => 'nullable|date',
            'settings'         => 'sometimes|array',
        ]);

        if (isset($validated['scheduled_at'])) {
            $validated['scheduled_at'] = Carbon::parse($validated['scheduled_at']);
        }

        $exam->update($validated);

        return response()->json([
            'message' => 'Exam updated successfully',
            'data'    => $exam
        ]);
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $instructor = $request->user();

        $exam = Exam::where('user_id', $instructor->id)
            ->where('course_code', $instructor->course_code)
            ->where('id', $id)
            ->firstOrFail();

        $exam->delete();

        return response()->json([
            'message' => 'Exam deleted successfully'
        ]);
    }
}
