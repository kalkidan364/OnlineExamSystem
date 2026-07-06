<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorQuestionBankController extends Controller
{
    /**
     * Display a listing of the question banks for the instructor's assigned course.
     */
    public function index(Request $request): JsonResponse
    {
        $instructor = $request->user();

        // Scope to instructor's single assigned course
        $banks = QuestionBank::where('user_id', $instructor->id)
            ->where('course_code', $instructor->course_code)
            ->withCount('questions')
            ->latest()
            ->get();

        // Fetch questions inside banks to determine types (mcq, sa, essay)
        // Since doing this efficiently requires some group by queries, for now we will approximate or do a subquery
        $banks->each(function ($bank) {
            $questions = $bank->questions;
            $bank->mcq_count = $questions->where('type', 'multiple_choice')->count();
            $bank->sa_count = $questions->where('type', 'short_answer')->count();
            $bank->essay_count = $questions->where('type', 'essay')->count();
        });

        // Global stats across all banks
        $totalBanks = $banks->count();
        $totalQuestions = $banks->sum('questions_count');
        $totalMcq = $banks->sum('mcq_count');
        $totalSa = $banks->sum('sa_count');
        $totalEssay = $banks->sum('essay_count');

        return response()->json([
            'data' => [
                'banks' => $banks->map(fn($bank) => [
                    'id'               => $bank->id,
                    'title'            => $bank->title,
                    'description'      => $bank->description,
                    'course_code'      => $bank->course_code,
                    'course_name'      => $bank->course_name,
                    'status'           => 'Active', // Mocking status since there isn't one on the model currently
                    'total_questions'  => $bank->questions_count,
                    'types'            => [
                        'mcq'   => $bank->mcq_count,
                        'sa'    => $bank->sa_count,
                        'essay' => $bank->essay_count,
                    ],
                    'updated_at'       => $bank->updated_at->toISOString(),
                ]),
                'stats' => [
                    'total_banks'     => $totalBanks,
                    'total_questions' => $totalQuestions,
                    'mcq_questions'   => $totalMcq,
                    'sa_questions'    => $totalSa,
                    'essay_questions' => $totalEssay,
                ]
            ]
        ]);
    }

    /**
     * Display a single question bank with all its questions.
     */
    public function show(Request $request, QuestionBank $questionBank): JsonResponse
    {
        $instructor = $request->user();

        // Ensure the instructor owns this bank
        if ($questionBank->user_id !== $instructor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Load questions
        $questions = $questionBank->questions()->orderBy('id')->get();

        // Count by type
        $mcqCount = $questions->where('type', 'multiple_choice')->count();
        $saCount = $questions->where('type', 'short_answer')->count();
        $essayCount = $questions->where('type', 'essay')->count();
        $tfCount = $questions->where('type', 'true_false')->count();
        $total = $questions->count();

        // Build categories from chapters
        $categoryCounts = $questions->groupBy('chapter')->map->count()->sortDesc();

        return response()->json([
            'data' => [
                'bank' => [
                    'id'          => $questionBank->id,
                    'title'       => $questionBank->title,
                    'description' => $questionBank->description,
                    'course_code' => $questionBank->course_code,
                    'course_name' => $questionBank->course_name,
                    'status'      => 'Active',
                ],
                'stats' => [
                    'total'   => $total,
                    'mcq'     => $mcqCount,
                    'sa'      => $saCount,
                    'essay'   => $essayCount,
                    'tf'      => $tfCount,
                ],
                'questions' => $questions->map(fn($q) => [
                    'id'             => $q->id,
                    'text'           => $q->text,
                    'type'           => $q->type,
                    'difficulty'     => $q->difficulty ?? 'Medium',
                    'marks'          => $q->marks,
                    'chapter'        => $q->chapter,
                    'topic'          => $q->topic,
                    'options'        => $q->options,
                    'correct_answer' => $q->correct_answer,
                    'status'         => $q->status ? 'Active' : 'Inactive',
                    'tags'           => $q->tags,
                ]),
                'categories' => $categoryCounts->map(fn($count, $name) => [
                    'name'  => $name ?: 'Uncategorized',
                    'count' => $count,
                ])->values(),
            ]
        ]);
    }

    /**
     * Store a newly created question bank.
     */
    public function store(Request $request): JsonResponse
    {
        $instructor = $request->user();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $bank = QuestionBank::create([
            'user_id'     => $instructor->id,
            'course_code' => $instructor->course_code,
            'course_name' => $instructor->course_name,
            'title'       => $validated['title'],
            'description' => $validated['description'],
        ]);

        return response()->json([
            'message' => 'Question bank created successfully',
            'data'    => $bank
        ], 201);
    }

    /**
     * Store a new question in the specified question bank.
     */
    public function storeQuestion(Request $request, QuestionBank $questionBank): JsonResponse
    {
        // Ensure the instructor owns the question bank
        if ($questionBank->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type'           => 'required|string',
            'difficulty'     => 'nullable|string',
            'chapter'        => 'nullable|string',
            'topic'          => 'nullable|string',
            'text'           => 'required|string',
            'options'        => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'marks'          => 'required|integer|min:1',
            'negative_marks' => 'nullable|integer',
            'time_seconds'   => 'nullable|integer',
            'status'         => 'nullable|boolean',
            'tags'           => 'nullable|string',
        ]);

        $question = $questionBank->questions()->create($validated);

        // Update the question count
        $questionBank->increment('questions_count');

        return response()->json([
            'message' => 'Question created successfully',
            'data'    => $question
        ], 201);
    }

    /**
     * Update the specified question bank.
     */
    public function update(Request $request, QuestionBank $questionBank): JsonResponse
    {
        if ($questionBank->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $questionBank->update($validated);

        return response()->json([
            'message' => 'Question bank updated successfully',
            'data'    => $questionBank
        ]);
    }

    /**
     * Remove the specified question bank.
     */
    public function destroy(Request $request, QuestionBank $questionBank): JsonResponse
    {
        if ($questionBank->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $questionBank->delete();

        return response()->json([
            'message' => 'Question bank deleted successfully'
        ]);
    }

    /**
     * Update the specified question.
     */
    public function updateQuestion(Request $request, Question $question): JsonResponse
    {
        $questionBank = $question->questionBank;
        if ($questionBank->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type'           => 'required|string',
            'difficulty'     => 'nullable|string',
            'chapter'        => 'nullable|string',
            'topic'          => 'nullable|string',
            'text'           => 'required|string',
            'options'        => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'marks'          => 'required|integer|min:1',
            'negative_marks' => 'nullable|integer',
            'time_seconds'   => 'nullable|integer',
            'status'         => 'nullable|boolean',
            'tags'           => 'nullable|string',
        ]);

        $question->update($validated);

        return response()->json([
            'message' => 'Question updated successfully',
            'data'    => $question
        ]);
    }

    /**
     * Remove the specified question.
     */
    public function destroyQuestion(Request $request, Question $question): JsonResponse
    {
        $questionBank = $question->questionBank;
        if ($questionBank->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->delete();
        $questionBank->decrement('questions_count');

        return response()->json([
            'message' => 'Question deleted successfully'
        ]);
    }

    /**
     * Import questions from CSV.
     */
    public function importQuestions(Request $request, QuestionBank $questionBank): JsonResponse
    {
        if ($questionBank->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimetypes:text/csv,text/plain,application/vnd.ms-excel'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $importedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 8) continue;
            
            $type = trim($row[1]);
            $difficulty = trim($row[2]);
            $marks = (int)trim($row[3]);
            $chapter = trim($row[4]);
            $topic = trim($row[5]);
            $text = trim($row[6]);
            $correctAnswer = trim($row[7]);
            
            if (!$text || !$type) continue; // Skip invalid rows

            $options = null;
            if ($type === 'multiple_choice') {
                $options = ['Option A', 'Option B', 'Option C', 'Option D'];
            } elseif ($type === 'true_false') {
                $options = ['True', 'False'];
            }

            $questionBank->questions()->create([
                'type' => $type,
                'difficulty' => $difficulty ?: 'Medium',
                'marks' => $marks ?: 1,
                'chapter' => $chapter,
                'topic' => $topic,
                'text' => $text,
                'correct_answer' => $correctAnswer,
                'options' => $options,
                'status' => true
            ]);
            $importedCount++;
        }
        
        fclose($handle);
        
        $questionBank->update(['questions_count' => $questionBank->questions()->count()]);

        return response()->json([
            'message' => "Successfully imported $importedCount questions",
            'imported' => $importedCount
        ]);
    }
}
