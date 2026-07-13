<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminCourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): JsonResponse
    {
        $courses = Course::with(['department', 'instructor', 'creator'])->get();
        return response()->json(['data' => $courses]);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code',
            'credits' => 'required|integer',
            'semester' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'instructor_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request) {
                    $instructor = User::where('id', $value)->where('department_id', $request->department_id)->where('role', 'instructor')->first();
                    if (!$instructor) {
                        $fail('The selected instructor is invalid or does not belong to the selected department.');
                    }
                },
            ],
        ]);

        $course = Course::create([
            'title' => $request->title,
            'code' => $request->code,
            'credits' => $request->credits,
            'semester' => $request->semester,
            'department_id' => $request->department_id,
            'instructor_id' => $request->instructor_id,
            'status' => 'active',
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'data' => $course->load(['department', 'instructor'])
        ], 201);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:courses,code,' . $course->id,
            'credits' => 'sometimes|integer',
            'semester' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
            'department_id' => 'sometimes|exists:departments,id',
            'instructor_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $course) {
                    $deptId = $request->input('department_id', $course->department_id);
                    $instructor = User::where('id', $value)->where('department_id', $deptId)->where('role', 'instructor')->first();
                    if (!$instructor) {
                        $fail('The selected instructor is invalid or does not belong to the department.');
                    }
                },
            ],
        ]);

        $course->update($request->only(['title', 'code', 'credits', 'semester', 'status', 'department_id', 'instructor_id']));

        return response()->json([
            'message' => 'Course updated successfully',
            'data' => $course->load(['department', 'instructor'])
        ]);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
