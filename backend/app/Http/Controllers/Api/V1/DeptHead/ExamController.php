<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

    /**
     * Display a listing of exams in the department.
     */
    public function index(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        
        $exams = Exam::whereHas('instructor', function ($query) use ($deptId) {
            $query->where('department_id', $deptId);
        })
        ->withCount('students')
        ->with('instructor')
        ->latest()
        ->get();

        return response()->json([
            'data' => $exams->map(fn($exam) => [
                'id'               => $exam->id,
                'title'            => $exam->title,
                'code'             => $exam->course_code,
                'course'           => $exam->course_name,
                'instructor_name'  => $exam->instructor->name ?? 'Unknown',
                'status'           => $exam->status === 'published' ? 'Scheduled' : ucfirst($exam->status),
                'duration'         => $exam->duration_minutes . ' min',
                'students'         => $exam->students_count,
                'date'             => $exam->scheduled_at ? $exam->scheduled_at->format('M d, Y') : 'TBD',
                'time'             => $exam->scheduled_at ? $exam->scheduled_at->format('h:i A') . ' - ' . $exam->scheduled_at->addMinutes($exam->duration_minutes)->format('h:i A') : 'TBD',
                'room'             => $exam->settings['room'] ?? 'TBD',
            ])
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
                    'scheduled_at'     => isset($course['date']) ? \Carbon\Carbon::parse($course['date']) : \Carbon\Carbon::parse($validated['start_date']),
                    'settings'         => [
                        'room'          => $course['room'] ?? null,
                        'time'          => $course['time'] ?? null,
                        'academic_year' => $validated['academic_year'] ?? null,
                        'semester'      => $validated['semester'] ?? null,
                        'exam_type'     => $validated['exam_type'] ?? null,
                        'year_level'    => $validated['year_level'] ?? null,
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
                'scheduled_at'     => \Carbon\Carbon::parse($validated['start_date']),
                'settings'         => [
                    'academic_year' => $validated['academic_year'] ?? null,
                    'semester'      => $validated['semester'] ?? null,
                    'exam_type'     => $validated['exam_type'] ?? null,
                    'year_level'    => $validated['year_level'] ?? null,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Schedule created successfully',
            'data'    => $createdExams
        ], 201);
    }
}
