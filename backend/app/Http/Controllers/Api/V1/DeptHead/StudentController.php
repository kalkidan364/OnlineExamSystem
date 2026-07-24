<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
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
     * Display a listing of students in the department.
     */
    public function index(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        
        $students = User::where('department_id', $deptId)
            ->where('role', 'student')
            ->get();

        return response()->json(['data' => $students]);
    }

    /**
     * Update a student in the department.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);

        $student = User::where('id', $id)
            ->where('department_id', $deptId)
            ->where('role', 'student')
            ->firstOrFail();

        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|email|unique:users,email,' . $student->id,
            'phone'         => 'nullable|string|max:50',
            'gender'        => 'nullable|string|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'id_no'         => 'nullable|string|max:100',
            'academic_year' => 'nullable|string|max:50',
            'year_level'    => 'nullable|string|max:50',
            'section'       => 'nullable|string|max:50',
            'username'      => 'nullable|string|unique:users,username,' . $student->id,
            'password'      => 'nullable|string|min:6',
        ]);

        // Handle password separately
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'data'    => $student->fresh(),
        ]);
    }
}
