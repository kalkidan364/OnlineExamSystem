<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends Controller
{
    /**
     * Get all activity logs.
     */
    public function index(Request $request): JsonResponse
    {
        $logs = ActivityLog::with(['user:id,name,email,role,department_id', 'user.department:id,name'])
            ->latest()
            ->get()
            ->map(function ($log) {
                // Use the recorded actor_role, fallback to current user role if old log
                $rawRole = $log->actor_role ?? ($log->user ? $log->user->role : '');
                $byWho = match($rawRole) {
                    'admin'     => 'Super Admin',
                    'dept_head' => $log->user && $log->user->department ? $log->user->department->name . ' Department Head' : 'Department Head',
                    'instructor' => 'Instructor',
                    'student'   => 'Student',
                    default     => 'System',
                };

                return [
                    'id'          => $log->id,
                    'time'        => $log->created_at,
                    'user'        => $log->user ? $log->user->name : 'Unknown',
                    'email'       => $log->user ? $log->user->email : '',
                    'role'        => $byWho,   // keep 'role' key for backward compat, value is now readable
                    'by_who'      => $byWho,   // explicit by_who field
                    'action'      => $log->type, // e.g. Created, Updated
                    'module'      => $log->module ?? 'System',
                    'description' => $log->details,
                    'ip_address'  => $log->ip_address ?? '127.0.0.1',
                    'status'      => $log->log_status ?? 'Success',
                    'is_read'     => (bool)$log->is_read,
                ];
            });

        return response()->json(['data' => $logs]);
    }

    /**
     * Get the count of unread activity logs.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = ActivityLog::where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Mark a specific activity log as read.
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $log = ActivityLog::findOrFail($id);
        $log->update(['is_read' => true]);
        
        return response()->json(['message' => 'Marked as read successfully.']);
    }

    /**
     * Mark all unread activity logs as read at once.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $updated = ActivityLog::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['message' => "Marked {$updated} logs as read."]);
    }
}
