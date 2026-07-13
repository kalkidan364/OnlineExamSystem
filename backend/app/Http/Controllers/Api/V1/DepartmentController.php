<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DepartmentController extends Controller
{
    /**
     * List all departments with their head info.
     */
    public function index(): JsonResponse
    {
        $departments = Department::with('head:id,name,email')->get();

        return response()->json([
            'data' => $departments,
            'message' => 'Departments retrieved successfully.',
        ]);
    }

    /**
     * Create a new department AND its department-head user account in one transaction.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:20|unique:departments,code',
            'established'    => 'nullable|max:4',
            'head_name'      => 'required|string|max:255',
            'head_email'     => 'required|email|unique:users,email',
            'head_password'  => 'required|string|min:8',
        ]);

        $result = DB::transaction(function () use ($request) {
            // 1. Create the department head user account
            $headUser = User::create([
                'name'     => $request->head_name,
                'email'    => $request->head_email,
                'password' => $request->head_password,   // auto-hashed via User model cast
                'role'     => 'dept_head',
            ]);

            // 2. Create the department and link the head
            $department = Department::create([
                'name'        => $request->name,
                'code'        => $request->code,
                'established' => $request->established,
                'head_id'     => $headUser->id,
                'status'      => 'active',
            ]);

            // 3. Assign the department back to the head user
            $headUser->update(['department_id' => $department->id]);

            return $department->load('head:id,name,email');
        });

        return response()->json([
            'data'    => $result,
            'message' => 'Department and head account created successfully.',
        ], 201);
    }

    /**
     * Update a department.
     */
    public function update(Request $request, Department $department): JsonResponse
    {
        $request->validate([
            'name'        => 'sometimes|string|max:255',
            'code'        => 'sometimes|string|max:20|unique:departments,code,' . $department->id,
            'established' => 'nullable|max:4',
            'status'      => 'sometimes|in:active,inactive',
        ]);

        $department->update($request->only(['name', 'code', 'established', 'status']));

        return response()->json([
            'data'    => $department->load('head:id,name,email'),
            'message' => 'Department updated successfully.',
        ]);
    }

    /**
     * Delete (soft: set inactive) a department.
     */
    public function destroy(Department $department): JsonResponse
    {
        DB::transaction(function () use ($department) {
            if ($department->head_id) {
                User::where('id', $department->head_id)->delete();
            }
            $department->delete();
        });

        return response()->json([
            'message' => 'Department and associated head account deleted successfully.',
        ]);
    }
}
