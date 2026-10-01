<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

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
     * Display a listing of the instructors in the department.
     */
    public function index(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $currentUserId = $request->user()->id;

        $instructors = User::where('department_id', $deptId)
            ->whereIn('role', ['instructor', 'dept_head'])
            ->with(['assignedCourses', 'coInstructorCourses', 'creator'])
            ->get();

        $totalInstructors = $instructors->count();
        $fullTimeCount = $instructors->filter(function ($i) {
            $et = strtolower($i->employment_type ?? 'full_time');
            $st = strtolower($i->status ?? 'active');
            return ($et === 'full_time' || $et === 'full time') && $st !== 'on_leave';
        })->count();

        $partTimeCount = $instructors->filter(function ($i) {
            $et = strtolower($i->employment_type ?? '');
            $st = strtolower($i->status ?? '');
            return $et === 'part_time' || $et === 'part time' || $st === 'part time';
        })->count();

        $onLeaveCount = $instructors->filter(function ($i) {
            $st = strtolower($i->status ?? '');
            return $st === 'on_leave' || $st === 'on leave' || $st === 'leave';
        })->count();

        $newThisSemester = $instructors->filter(function ($i) {
            return $i->created_at && $i->created_at >= Carbon::now()->subMonths(6);
        })->count();

        $data = $instructors->map(function ($instructor) use ($currentUserId) {
            // Instructor is created by department head if created_by matches current user
            // Otherwise, it was created by super admin
            $isCreatedByDeptHead = ($instructor->created_by === $currentUserId);

            return [
                'id'                 => $instructor->id,
                'name'               => $instructor->name,
                'email'              => $instructor->email,
                'username'           => $instructor->username,
                'phone'              => $instructor->phone,
                'gender'             => $instructor->gender,
                'id_no'              => $instructor->id_no,
                'role'               => $instructor->role,
                'department_id'      => $instructor->department_id,
                'course_code'        => $instructor->course_code,
                'course_name'        => $instructor->course_name,
                'year_level'         => $instructor->year_level,
                'semester'           => $instructor->semester,
                'section'            => $instructor->section,
                'status'             => $instructor->status ?? 'active',
                'employment_type'    => $instructor->employment_type ?? 'full_time',
                'created_by'         => $instructor->created_by,
                'creator_name'       => $instructor->creator?->name ?? 'Super Admin',
                'can_edit'           => $isCreatedByDeptHead,
                'can_delete'         => $isCreatedByDeptHead,
                'is_admin_created'   => !$isCreatedByDeptHead,
                'profile_picture'    => $instructor->profile_picture,
                'profile_picture_url'=> $instructor->profile_picture_url,
                'created_at'         => $instructor->created_at,
                'assigned_courses'   => $instructor->assignedCourses,
                'co_instructor_courses' => $instructor->coInstructorCourses,
            ];
        });

        return response()->json([
            'data' => $data,
            'stats' => [
                'total'             => $totalInstructors,
                'full_time'         => $fullTimeCount,
                'part_time'         => $partTimeCount,
                'on_leave'          => $onLeaveCount,
                'new_this_semester' => $newThisSemester,
            ]
        ]);
    }

    /**
     * Store a newly created instructor in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'required|string|max:255',
            'gender'          => 'required|string|max:255',
            'id_no'           => 'required|string|max:255',
            'year_level'      => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'username'        => 'required|string|unique:users,username',
            'password'        => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?`~]/',
            ],
            'employment_type' => 'nullable|string|max:50',
            'status'          => 'nullable|string|max:50',
        ], [
            'password.min'   => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $deptId = $this->resolveDeptId($request);

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('avatars', 'public');
        }

        $instructor = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'gender'          => $request->gender,
            'id_no'           => $request->id_no,
            'year_level'      => $request->year_level,
            'username'        => $request->username,
            'password'        => Hash::make($request->password),
            'role'            => 'instructor',
            'department_id'   => $deptId,
            'status'          => $request->status ?? 'active',
            'employment_type' => $request->employment_type ?? 'full_time',
            'created_by'      => $request->user()->id,
            'profile_picture' => $profilePicturePath,
        ]);

        LogActivity::record(
            'Created',
            'Instructors',
            "Created a new Instructor \"{$instructor->name}\""
        );

        return response()->json([
            'message' => 'Instructor created successfully',
            'data'    => $instructor
        ], 201);
    }

    /**
     * Update the specified instructor in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $instructor = User::where('department_id', $deptId)
            ->whereIn('role', ['instructor', 'dept_head'])
            ->findOrFail($id);

        // Security / Permission Check:
        // Instructors created by Super Admin can only be edited by Super Admin.
        if ($instructor->created_by !== $request->user()->id) {
            return response()->json([
                'message' => 'You do not have permission to edit this instructor. Instructors created by Super Admin can only be edited by Super Admin.'
            ], 403);
        }

        $request->validate([
            'name'            => 'sometimes|string|max:255',
            'email'           => 'sometimes|email|unique:users,email,' . $instructor->id,
            'phone'           => 'nullable|string|max:255',
            'gender'          => 'nullable|string|max:255',
            'id_no'           => 'nullable|string|max:255',
            'year_level'      => 'nullable|string|max:255',
            'semester'        => 'nullable|string|max:255',
            'status'          => 'nullable|string|max:50',
            'employment_type' => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'password'        => [
                'nullable',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?`~]/',
            ],
        ], [
            'password.min'   => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $updateData = $request->only([
            'name',
            'email',
            'phone',
            'gender',
            'id_no',
            'year_level',
            'semester',
            'status',
            'employment_type',
        ]);

        if ($request->hasFile('profile_picture')) {
            $updateData['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $instructor->update($updateData);

        LogActivity::record(
            'Updated',
            'Instructors',
            "Updated Instructor \"{$instructor->name}\""
        );

        return response()->json([
            'message' => 'Instructor updated successfully',
            'data'    => $instructor
        ]);
    }

    /**
     * Remove the specified instructor from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $instructor = User::where('department_id', $deptId)
            ->whereIn('role', ['instructor', 'dept_head'])
            ->findOrFail($id);

        // Security / Permission Check:
        // Instructors created by Super Admin can only be deleted by Super Admin.
        if ($instructor->created_by !== $request->user()->id) {
            return response()->json([
                'message' => 'You do not have permission to delete this instructor. Instructors created by Super Admin can only be deleted by Super Admin.'
            ], 403);
        }

        $instructorName = $instructor->name;
        $instructor->delete();

        LogActivity::record(
            'Deleted',
            'Instructors',
            "Deleted Instructor \"$instructorName\""
        );

        return response()->json(['message' => 'Instructor deleted successfully']);
    }
}
