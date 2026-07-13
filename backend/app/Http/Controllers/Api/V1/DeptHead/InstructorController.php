<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InstructorController extends Controller
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
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        
        $instructors = User::where('department_id', $deptId)
            ->where('role', 'instructor')
            ->get();

        return response()->json(['data' => $instructors]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'semester' => 'nullable|string',
        ]);

        $deptId = $this->resolveDeptId($request);

        $instructor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // automatically hashed
            'role' => 'instructor',
            'department_id' => $deptId,
            'semester' => $request->semester,
        ]);

        return response()->json([
            'message' => 'Instructor created successfully',
            'data' => $instructor
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $instructor = User::where('department_id', $deptId)->where('role', 'instructor')->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'semester' => 'nullable|string',
            // Allow status if needed later
        ]);

        $instructor->update($request->only(['name', 'semester']));

        return response()->json([
            'message' => 'Instructor updated successfully',
            'data' => $instructor
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $instructor = User::where('department_id', $deptId)->where('role', 'instructor')->findOrFail($id);
        
        $instructor->delete();

        return response()->json(['message' => 'Instructor deleted successfully']);
    }
}
