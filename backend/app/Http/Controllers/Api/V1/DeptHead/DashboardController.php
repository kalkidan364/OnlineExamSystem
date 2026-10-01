<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\AcademicEvent;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Resolve the department ID for the logged-in Department Head.
     */
    private function resolveDeptId(Request $request): ?int
    {
        $user = $request->user();
        if ($user->department_id) {
            return $user->department_id;
        }

        $dept = Department::where('head_id', $user->id)->first();
        if ($dept) {
            $user->update(['department_id' => $dept->id]);
            return $dept->id;
        }

        return null;
    }

    /**
     * Return comprehensive real statistics for the Department Head dashboard.
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $deptId = $this->resolveDeptId($request);
        $department = $deptId ? Department::find($deptId) : null;

        // -------------------------------------------------------------
        // 1. Core KPIs Scoped to Department
        // -------------------------------------------------------------
        $totalStudents = $deptId
            ? User::where('department_id', $deptId)->where('role', 'student')->count()
            : 0;

        $newStudentsThisMonth = $deptId
            ? User::where('department_id', $deptId)
                ->where('role', 'student')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count()
            : 0;

        $totalInstructors = $deptId
            ? User::where('department_id', $deptId)->whereIn('role', ['instructor', 'dept_head'])->count()
            : 0;

        $newInstructorsThisMonth = $deptId
            ? User::where('department_id', $deptId)
                ->whereIn('role', ['instructor', 'dept_head'])
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count()
            : 0;

        $totalCourses = $deptId
            ? Course::where('department_id', $deptId)->count()
            : 0;

        $newCoursesThisMonth = $deptId
            ? Course::where('department_id', $deptId)
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count()
            : 0;

        // Exam query scoped to department courses or department instructors
        $courseCodes = $deptId ? Course::where('department_id', $deptId)->pluck('code') : collect();
        $examsQuery = Exam::query();
        if ($deptId) {
            $examsQuery->where(function ($q) use ($deptId, $courseCodes, $user) {
                $q->whereHas('instructor', function ($iq) use ($deptId) {
                    $iq->where('department_id', $deptId);
                })->orWhereIn('course_code', $courseCodes)
                  ->orWhere('user_id', $user->id);
            });
        } else {
            $examsQuery->whereRaw('1=0');
        }

        $allDeptExams = (clone $examsQuery)->get();
        $totalExams = $allDeptExams->count();

        $activeExams = (clone $examsQuery)->whereIn('status', ['published', 'scheduled'])->count();

        $kpis = [
            [
                'label'  => 'Total Students',
                'value'  => (string) $totalStudents,
                'change' => $newStudentsThisMonth > 0 ? "↑ {$newStudentsThisMonth} this month" : 'Enrolled in department',
                'bg'     => 'bg-indigo-50',
                'ic'     => 'text-[#5138ed]',
                'icon'   => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'color'  => 'text-emerald-500',
            ],
            [
                'label'  => 'Total Instructors',
                'value'  => (string) $totalInstructors,
                'change' => $newInstructorsThisMonth > 0 ? "↑ {$newInstructorsThisMonth} this month" : 'Active faculty',
                'bg'     => 'bg-emerald-50',
                'ic'     => 'text-emerald-500',
                'icon'   => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'color'  => 'text-emerald-500',
            ],
            [
                'label'  => 'Total Courses',
                'value'  => (string) $totalCourses,
                'change' => $newCoursesThisMonth > 0 ? "↑ {$newCoursesThisMonth} this month" : 'Active curriculum',
                'bg'     => 'bg-sky-50',
                'ic'     => 'text-sky-500',
                'icon'   => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                'color'  => 'text-emerald-500',
            ],
            [
                'label'  => 'Active Exams',
                'value'  => (string) $activeExams,
                'change' => 'Scheduled & active',
                'bg'     => 'bg-amber-50',
                'ic'     => 'text-amber-500',
                'icon'   => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                'color'  => 'text-emerald-500',
            ],
        ];

        // -------------------------------------------------------------
        // 2. Department Overview Chart (Monthly Students & Courses)
        // -------------------------------------------------------------
        $chartLabels = [];
        $studentsTrend = [];
        $coursesTrend = [];

        for ($i = 8; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $chartLabels[] = $monthDate->format('M');
            $endOfMonth = $monthDate->copy()->endOfMonth();

            $stCount = $deptId ? User::where('department_id', $deptId)
                ->where('role', 'student')
                ->where('created_at', '<=', $endOfMonth)
                ->count() : 0;

            $crCount = $deptId ? Course::where('department_id', $deptId)
                ->where('created_at', '<=', $endOfMonth)
                ->count() : 0;

            $studentsTrend[] = $stCount;
            $coursesTrend[] = $crCount;
        }

        // -------------------------------------------------------------
        // 3. Exams Breakdown (Doughnut Chart)
        // -------------------------------------------------------------
        $now = Carbon::now();
        $upcomingCount = 0;
        $ongoingCount = 0;
        $completedCount = 0;
        $draftCount = 0;

        foreach ($allDeptExams as $exam) {
            if ($exam->status === 'draft') {
                $draftCount++;
            } elseif ($exam->status === 'completed') {
                $completedCount++;
            } elseif ($exam->status === 'scheduled') {
                $upcomingCount++;
            } elseif ($exam->status === 'published') {
                $sched = $exam->scheduled_at;
                $dur = $exam->duration_minutes ?? 60;
                if ($sched) {
                    $end = $sched->copy()->addMinutes($dur);
                    if ($now->lt($sched)) {
                        $upcomingCount++;
                    } elseif ($now->between($sched, $end)) {
                        $ongoingCount++;
                    } else {
                        $completedCount++;
                    }
                } else {
                    $upcomingCount++;
                }
            } else {
                $draftCount++;
            }
        }

        $examStats = [
            ['label' => 'Upcoming', 'val' => $upcomingCount, 'color' => 'bg-[#5138ed]'],
            ['label' => 'Ongoing', 'val' => $ongoingCount, 'color' => 'bg-sky-400'],
            ['label' => 'Completed', 'val' => $completedCount, 'color' => 'bg-emerald-500'],
            ['label' => 'Cancelled', 'val' => $draftCount, 'color' => 'bg-rose-500'],
        ];

        // -------------------------------------------------------------
        // 4. Department Performance (From Exam Attempts)
        // -------------------------------------------------------------
        $examIds = $allDeptExams->pluck('id');
        $attempts = ExamAttempt::whereIn('exam_id', $examIds);
        $totalAttempts = (clone $attempts)->count();

        $passedAttempts = (clone $attempts)->where('percentage', '>=', 50)->count();
        $passRate = $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0;

        $avgScore = (clone $attempts)->avg('percentage') ?? 0;
        $gpa = round(($avgScore / 100) * 4.0, 2);

        $submittedAttempts = (clone $attempts)->whereIn('status', ['submitted', 'graded', 'published'])->count();
        $attendancePct = $totalAttempts > 0 ? round(($submittedAttempts / $totalAttempts) * 100) : 0;

        $activeCoursesCount = $deptId ? Course::where('department_id', $deptId)->where('status', 'active')->count() : 0;
        $courseCompletionPct = $totalCourses > 0 ? round(($activeCoursesCount / $totalCourses) * 100) : 0;

        $performances = [
            [
                'label' => 'Attendance Rate',
                'val'   => $totalAttempts > 0 ? "{$attendancePct}%" : '0%',
                'pct'   => $totalAttempts > 0 ? $attendancePct : 0,
                'color' => 'bg-emerald-500',
            ],
            [
                'label' => 'Pass Rate',
                'val'   => $totalAttempts > 0 ? "{$passRate}%" : '0%',
                'pct'   => $totalAttempts > 0 ? (int) round($passRate) : 0,
                'color' => 'bg-[#5138ed]',
            ],
            [
                'label' => 'Average Grade',
                'val'   => $totalAttempts > 0 ? number_format($gpa, 2) . ' / 4.00' : '0.00 / 4.00',
                'pct'   => $totalAttempts > 0 ? min(100, (int) round($avgScore)) : 0,
                'color' => 'bg-sky-400',
            ],
            [
                'label' => 'Course Completion',
                'val'   => "{$courseCompletionPct}%",
                'pct'   => $courseCompletionPct,
                'color' => 'bg-amber-500',
            ],
        ];

        // -------------------------------------------------------------
        // 5. Recent Department Activities
        // -------------------------------------------------------------
        $logsQuery = ActivityLog::query();
        if ($deptId) {
            $logsQuery->where(function ($q) use ($deptId) {
                $q->where('department_id', $deptId)
                  ->orWhereHas('user', function ($uq) use ($deptId) {
                      $uq->where('department_id', $deptId);
                  });
            });
        }

        $logs = $logsQuery->latest()->take(5)->get();

        if ($logs->isEmpty()) {
            // Fallback to recent system activity logs so the dashboard is informative
            $logs = ActivityLog::latest()->take(5)->get();
        }

        $formattedActivities = $logs->map(function ($log) {
            $icon = 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z';
            $bg = 'bg-indigo-50';
            $color = 'text-[#5138ed]';

            $mod = strtolower($log->module ?? '');
            $type = strtolower($log->type ?? '');

            if (str_contains($mod, 'exam')) {
                $icon = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4';
                $bg = 'bg-amber-50';
                $color = 'text-amber-500';
            } elseif (str_contains($mod, 'course')) {
                $icon = 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';
                $bg = 'bg-purple-50';
                $color = 'text-[#5138ed]';
            } elseif (str_contains($type, 'delete')) {
                $icon = 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16';
                $bg = 'bg-rose-50';
                $color = 'text-rose-500';
            } elseif (str_contains($type, 'create') || str_contains($type, 'store')) {
                $icon = 'M12 6v6m0 0v6m0-6h6m-6 0H6';
                $bg = 'bg-emerald-50';
                $color = 'text-emerald-500';
            }

            return [
                'title' => $log->action ?: ($log->module . ' ' . $log->type),
                'desc'  => $log->details ?: $log->action,
                'time'  => $log->created_at ? $log->created_at->diffForHumans() : 'Recently',
                'icon'  => $icon,
                'bg'    => $bg,
                'color' => $color,
            ];
        });

        // -------------------------------------------------------------
        // 6. Recent Announcements (Academic Events)
        // -------------------------------------------------------------
        $events = AcademicEvent::with('category')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'cancelled');
            })
            ->orderByDesc('start_date')
            ->take(3)
            ->get();

        $formattedAnnouncements = $events->map(function ($event) {
            $catName = strtolower($event->category?->name ?? '');
            $bg = 'bg-sky-50';
            $color = 'text-sky-500';

            if (str_contains($catName, 'exam')) {
                $bg = 'bg-amber-50';
                $color = 'text-amber-500';
            } elseif (str_contains($catName, 'holiday')) {
                $bg = 'bg-rose-50';
                $color = 'text-rose-500';
            } elseif (str_contains($catName, 'academic')) {
                $bg = 'bg-emerald-50';
                $color = 'text-emerald-500';
            }

            return [
                'title' => $event->title,
                'desc'  => $event->description ?: ($event->academic_year . ' ' . $event->semester),
                'date'  => $event->start_date ? $event->start_date->format('M d, Y') : $event->created_at->format('M d, Y'),
                'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'bg'    => $bg,
                'color' => $color,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'department' => [
                    'id'   => $department?->id,
                    'name' => $department?->name ?? 'Department',
                    'code' => $department?->code ?? 'DEPT',
                ],
                'stats' => $kpis,
                'lineChart' => [
                    'labels'   => $chartLabels,
                    'students' => $studentsTrend,
                    'courses'  => $coursesTrend,
                ],
                'doughnutChart' => [
                    'totalExams' => $totalExams,
                    'data'       => [$upcomingCount, $ongoingCount, $completedCount, $draftCount],
                    'stats'      => $examStats,
                ],
                'performances'  => $performances,
                'activities'    => $formattedActivities,
                'announcements' => $formattedAnnouncements,
            ]
        ]);
    }
}
