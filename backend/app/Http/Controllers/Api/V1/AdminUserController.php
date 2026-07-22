<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * List all users (instructors, students, dept_heads).
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('department:id,name')->latest();
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        if ($request->has('academic_year') && $request->academic_year !== 'all') {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->has('year_level') && $request->year_level !== 'all') {
            $query->where('year_level', $request->year_level);
        }
        if ($request->has('semester') && $request->semester !== 'all') {
            $query->where('semester', $request->semester);
        }
        $users = $query->get();

        return response()->json(['data' => $users]);
    }

    /**
     * Create a new user (student or instructor) with a department assignment.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'username'      => 'nullable|string|max:255|unique:users,username',
            'role'          => 'required|in:instructor,student,dept_head,admin',
            'department_id' => 'nullable|exists:departments,id',
            'course_code'   => 'nullable|string|max:255',
            'course_name'   => 'nullable|string|max:255',
            'academic_year' => 'nullable|string|max:255',
            'year_level'    => 'nullable|string|max:255',
            'semester'      => 'nullable|string|max:255',
            'id_no'         => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'gender'        => 'nullable|in:Male,Female,Other',
            'password'      => 'required|string|min:8',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'username'      => $request->username,
            'role'          => $request->role,
            'department_id' => $request->department_id,
            'course_code'   => $request->course_code,
            'course_name'   => $request->course_name,
            'academic_year' => $request->academic_year,
            'year_level'    => $request->year_level,
            'semester'      => $request->semester,
            'id_no'         => $request->id_no,
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'password'      => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return response()->json([
            'data'    => $user->load('department:id,name'),
            'message' => 'User created successfully.',
        ], 201);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
