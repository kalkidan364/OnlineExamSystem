<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Log in a user and issue a Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
            'role'     => 'required|string|in:student,instructor,staff',
        ]);

        $loginField = $request->login;
        $requestedRole = $request->role;

        // Try to find user by email first, then by username
        $user = User::where('email', $loginField)->first()
              ?? User::where('username', $loginField)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $effectiveRole = $user->role;

        // Role Validation Logic
        if ($requestedRole === 'student') {
            if ($user->role !== 'student') {
                throw ValidationException::withMessages(['login' => ['Invalid credentials or role mismatch.']]);
            }
        } elseif ($requestedRole === 'instructor') {
            if (!in_array($user->role, ['instructor', 'dept_head'])) {
                throw ValidationException::withMessages(['login' => ['Invalid credentials or role mismatch.']]);
            }
            $effectiveRole = 'instructor';
        } elseif ($requestedRole === 'staff') {
            if (!in_array($user->role, ['admin', 'dept_head'])) {
                throw ValidationException::withMessages(['login' => ['Invalid credentials or role mismatch.']]);
            }
            $effectiveRole = $user->role; // either admin or dept_head
        }

        // Revoke old tokens (single-session policy)
        $user->tokens()->delete();

        if ($effectiveRole === 'instructor') {
            $hasCourses = \App\Models\Course::where('instructor_id', $user->id)->exists();
            if (!$hasCourses) {
                throw ValidationException::withMessages([
                    'login' => ['Access denied. You have not been assigned to any courses yet. Please wait for an administrator to assign you to a course.'],
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Load department so the frontend has it immediately upon login
        $user->load('department');

        return response()->json([
            'data' => [
                'user'  => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'username'   => $user->username,
                    'role'       => $effectiveRole,
                    'department_id' => $user->department_id,
                    'department' => $user->department,
                ],
                'token' => $token,
            ],
            'message' => 'Login successful.',
        ]);
    }

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'sometimes|in:instructor,student',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $request->role ?? 'student',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'token' => $token,
            ],
            'message' => 'Registration successful.',
        ], 201);
    }

    /**
     * Log out by revoking all tokens.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }
}
