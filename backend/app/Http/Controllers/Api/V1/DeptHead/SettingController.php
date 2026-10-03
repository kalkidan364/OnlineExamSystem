<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Helpers\LogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    /**
     * Get the logged-in Department Head's account settings and preferences.
     */
    public function getSettings(Request $request): JsonResponse
    {
        $user = $request->user()->load('department');

        $defaultNotifications = [
            'emailOnInstructorJoin' => true,
            'emailOnCourseCreate'   => true,
            'emailOnExamPublish'    => false,
            'weeklyReport'          => true,
        ];

        $notifications = is_array($user->notification_preferences)
            ? array_merge($defaultNotifications, $user->notification_preferences)
            : $defaultNotifications;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'profile' => [
                    'id'          => $user->id,
                    'fullName'    => $user->name,
                    'email'       => $user->email,
                    'phone'       => $user->phone ?? '+251 91 123 4567',
                    'office'      => $user->office ?? 'Block A, Room 204',
                    'role'        => $user->role,
                    'department'  => $user->department?->name ?? 'Computer Science',
                ],
                'notifications' => $notifications,
            ]
        ]);
    }

    /**
     * Update account settings (profile information, security/password, notification preferences).
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $user = $request->user();

        $rules = [
            'fullName'        => 'required|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'office'          => 'nullable|string|max:100',
            'notifications'   => 'nullable|array',
            'currentPassword' => 'nullable|string',
            'newPassword'     => 'nullable|string|min:6',
            'confirmPassword' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        // Handle Password Change if requested
        $passwordChanged = false;
        if (!empty($request->newPassword)) {
            if (empty($request->currentPassword)) {
                throw ValidationException::withMessages([
                    'currentPassword' => ['Current password is required to set a new password.'],
                ]);
            }

            if (!Hash::check($request->currentPassword, $user->password)) {
                throw ValidationException::withMessages([
                    'currentPassword' => ['The provided current password does not match our records.'],
                ]);
            }

            if ($request->newPassword !== $request->confirmPassword) {
                throw ValidationException::withMessages([
                    'confirmPassword' => ['Password confirmation does not match.'],
                ]);
            }

            $user->password = Hash::make($request->newPassword);
            $passwordChanged = true;
        }

        // Update Profile
        $user->name = $request->fullName;
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('office')) {
            $user->office = $request->office;
        }

        // Update Notification Preferences
        if ($request->has('notifications') && is_array($request->notifications)) {
            $user->notification_preferences = [
                'emailOnInstructorJoin' => (bool)($request->notifications['emailOnInstructorJoin'] ?? false),
                'emailOnCourseCreate'   => (bool)($request->notifications['emailOnCourseCreate'] ?? false),
                'emailOnExamPublish'    => (bool)($request->notifications['emailOnExamPublish'] ?? false),
                'weeklyReport'          => (bool)($request->notifications['weeklyReport'] ?? false),
            ];
        }

        $user->save();

        // Log the activity
        $logMessage = "Department Head updated profile information & preferences";
        if ($passwordChanged) {
            $logMessage .= " and changed security password";
        }
        LogActivity::record('Updated', 'Settings', $logMessage, 'Success', $user->department_id);

        return response()->json([
            'status'  => 'success',
            'message' => $passwordChanged
                ? 'Account settings and password updated successfully.'
                : 'Account settings saved successfully.',
            'data'    => [
                'profile' => [
                    'id'          => $user->id,
                    'fullName'    => $user->name,
                    'email'       => $user->email,
                    'phone'       => $user->phone,
                    'office'      => $user->office,
                    'role'        => $user->role,
                    'department'  => $user->department?->name,
                ],
                'notifications' => $user->notification_preferences,
            ]
        ]);
    }
}
