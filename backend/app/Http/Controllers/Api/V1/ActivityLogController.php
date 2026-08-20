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
        $logs = ActivityLog::with('user:id,name,email,role')
            ->latest()
            ->get()
            ->map(function ($log) {
                // Use the recorded actor_role, fallback to current user role if old log
                $rawRole = $log->actor_role ?? ($log->user ? $log->user->role : '');
                $byWho = match($rawRole) {
                    'admin'     => 'Super Admin',
                    'dept_head' => 'Department Head',
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
                ];
            });

        return response()->json(['data' => $logs]);
    }
}
