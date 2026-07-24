<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    private function resolveDeptId(Request $request): ?int
    {
        $user = $request->user();
        if ($user->department_id) return $user->department_id;
        // Fallback: look up department where this user is the head
        $dept = Department::where('head_id', $user->id)->first();
        if ($dept) {
            $user->update(['department_id' => $dept->id]);
            return $dept->id;
        }
        return null;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        
        $courses = Course::with('instructor', 'creator', 'coInstructor')
            ->where('department_id', $deptId)
            ->withCount('exams')
            ->get();

        return response()->json(['data' => $courses]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code',
            'credits' => 'required|integer',
            'semester' => 'nullable|string',
            'level' => 'nullable|string',
            'instructor_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($deptId) {
                    $instructor = User::where('id', $value)->where('department_id', $deptId)->where('role', 'instructor')->first();
                    if (!$instructor) {
                        $fail('The selected instructor is invalid or does not belong to your department.');
                    }
                },
            ],
        ]);

        $course = Course::create([
            'title' => $request->title,
            'code' => $request->code,
            'credits' => $request->credits,
            'semester' => $request->semester,
            'level' => $request->level,
            'department_id' => $deptId,
            'instructor_id' => $request->instructor_id,
            'status' => 'active',
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'data' => $course->load('instructor')
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $course = Course::where('department_id', $deptId)->findOrFail($id);

        if ($course->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized. You can only edit courses you created.'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:courses,code,' . $course->id,
            'credits' => 'sometimes|integer',
            'semester' => 'nullable|string',
            'level' => 'nullable|string',
            'section' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
            'instructor_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($deptId) {
                    $instructor = User::where('id', $value)->where('department_id', $deptId)->whereIn('role', ['instructor', 'dept_head'])->first();
                    if (!$instructor) {
                        $fail('The selected instructor is invalid or does not belong to your department.');
                    }
                },
            ],
            'co_instructor_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($deptId) {
                    $instructor = User::where('id', $value)->where('department_id', $deptId)->whereIn('role', ['instructor', 'dept_head'])->first();
                    if (!$instructor) {
                        $fail('The selected co-instructor is invalid or does not belong to your department.');
                    }
                },
            ],
        ]);

        $course->update($request->only(['title', 'code', 'credits', 'semester', 'level', 'section', 'status', 'instructor_id', 'co_instructor_id']));

        // Sync instructor's section and year_level when assigning
        if ($request->has('instructor_id') && $request->instructor_id) {
            User::where('id', $request->instructor_id)->update([
                'section'    => $course->section ?? $request->section,
                'year_level' => $course->level,
                'course_code' => $course->code,
                'course_name' => $course->title,
            ]);
        }
        if ($request->has('co_instructor_id') && $request->co_instructor_id) {
            User::where('id', $request->co_instructor_id)->update([
                'section'    => $course->section ?? $request->section,
                'year_level' => $course->level,
                'course_code' => $course->code,
                'course_name' => $course->title,
            ]);
        }

        return response()->json([
            'message' => 'Course updated successfully',
            'data' => $course->load(['instructor', 'coInstructor'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $course = Course::where('department_id', $deptId)->findOrFail($id);
        
        if ($course->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized. You can only delete courses you created.'], 403);
        }
        
        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
