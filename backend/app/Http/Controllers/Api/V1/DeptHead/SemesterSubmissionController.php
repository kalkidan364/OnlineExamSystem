<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\SemesterSubmission;
use App\Models\User;
use App\Models\Course;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class SemesterSubmissionController extends Controller
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
     * Get the year-level summary for SemesterSubmissions.vue
     */
    public function index(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $departmentName = Department::find($deptId)?->name ?? 'Unknown Department';

        $yearLabels = [
            '1st year' => '1st Year',
            '2nd year' => '2nd Year',
            '3rd year' => '3rd Year',
            '4th year' => '4th Year',
            '5th year' => '5th Year',
        ];

        $allInstructors = User::where('department_id', $deptId)
            ->whereIn('role', ['instructor', 'dept_head'])
            ->get(['id', 'year_level']);

        $allCourses = Course::where('department_id', $deptId)
            ->get(['id', 'level', 'instructor_id']);

        $instructorIds = $allInstructors->pluck('id');
        $submissions = SemesterSubmission::whereIn('instructor_id', $instructorIds)->get();

        $normalizeLevel = function ($level) {
            return strtolower(trim($level ?? ''));
        };

        $groups = [];
        foreach ($yearLabels as $key => $label) {
            $instCount = $allInstructors->filter(function ($i) use ($key, $normalizeLevel) {
                return $normalizeLevel($i->year_level) === $key;
            })->count();

            $courseCount = $allCourses->filter(function ($c) use ($key, $normalizeLevel) {
                return $normalizeLevel($c->level) === $key;
            })->count();

            $groups[$key] = [
                'id'                   => base64_encode($key),
                'academicYear'         => $label,
                'semester'             => 'Second Semester',
                'department'           => $departmentName,
                'coursesCount'         => $courseCount,
                'instructorsCount'     => $instCount,
                'submittedInstructors' => [],
                'totalCount'           => $instCount,
                'status'               => 'Not Submitted',
            ];
        }

        $instructorYearMap = [];
        foreach ($allInstructors as $inst) {
            $instructorYearMap[$inst->id] = $normalizeLevel($inst->year_level);
        }

        $academicYearFallback = [
            '2025/2026' => '1st year',
            '2024/2025' => '2nd year',
            '2023/2024' => '3rd year',
            '2022/2023' => '4th year',
            '2021/2022' => '5th year',
        ];

        foreach ($submissions as $sub) {
            if (!in_array($sub->status, ['submitted', 'approved'])) continue;

            $yearKey = $instructorYearMap[$sub->instructor_id] ?? null;
            if (!$yearKey || !isset($groups[$yearKey])) {
                $yearKey = $academicYearFallback[$sub->academic_year] ?? null;
            }

            if ($yearKey && isset($groups[$yearKey])) {
                if (!in_array($sub->instructor_id, $groups[$yearKey]['submittedInstructors'])) {
                    $groups[$yearKey]['submittedInstructors'][] = $sub->instructor_id;
                }
            }
        }

        foreach ($groups as $key => &$group) {
            $group['submittedCount'] = count($group['submittedInstructors']);
            unset($group['submittedInstructors']);

            if ($group['submittedCount'] === 0) {
                $group['status'] = 'Not Submitted';
            } elseif ($group['totalCount'] > 0 && $group['submittedCount'] >= $group['totalCount']) {
                $group['status'] = 'Submitted';
            } elseif ($group['submittedCount'] > 0) {
                $group['status'] = 'Pending';
            }
        }

        $submissionsList = array_values($groups);

        $stats = [
            'totalAcademicYears' => count($groups),
            'totalDepartments'   => Department::count(),
            'totalCourses'       => $allCourses->count(),
            'totalInstructors'   => $allInstructors->count(),
        ];

        return response()->json([
            'stats'       => $stats,
            'submissions' => $submissionsList,
        ]);
    }

    /**
     * Get detailed instructor semester submissions for SemesterSubmissionDetail.vue
     */
    public function details(Request $request, $id = null): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $userDept = Department::find($deptId);

        $academicYear = $request->query('academic_year', '2025/2026');
        $semester = $request->query('semester', 'Second Semester');
        $deptFilter = $request->query('department');
        $statusFilter = $request->query('status');
        $searchQuery = strtolower(trim($request->query('search', '')));

        // Decode year level from $id or query param
        $yearLevelFilter = $request->query('year_level');
        if (!$yearLevelFilter && $id && $id !== 'details' && $id !== 'all') {
            $decoded = base64_decode($id, true);
            $yearLevelFilter = ($decoded !== false && ctype_print($decoded)) ? $decoded : $id;
        }

        // Query instructors
        $instructorsQuery = User::whereIn('role', ['instructor', 'dept_head'])
            ->with(['department', 'assignedCourses']);

        // Department filtering
        if ($deptFilter && $deptFilter !== 'All Departments') {
            $instructorsQuery->whereHas('department', function ($q) use ($deptFilter) {
                $q->where('name', $deptFilter);
            });
        } elseif (!$deptFilter && $deptId) {
            // Default: show current department head's department + related instructors
            $instructorsQuery->where(function ($q) use ($deptId) {
                $q->where('department_id', $deptId)
                  ->orWhereNull('department_id');
            });
        }

        // Year level filtering if specified
        if ($yearLevelFilter && strtolower($yearLevelFilter) !== 'all') {
            $normalizedLevel = strtolower(trim($yearLevelFilter));
            $instructorsQuery->where(function ($q) use ($normalizedLevel) {
                $q->whereRaw('LOWER(TRIM(year_level)) = ?', [$normalizedLevel])
                  ->orWhereNull('year_level');
            });
        }

        $instructors = $instructorsQuery->get();

        $colorPalettes = [
            'bg-purple-100 text-purple-700',
            'bg-rose-100 text-rose-700',
            'bg-sky-100 text-sky-700',
            'bg-amber-100 text-amber-700',
            'bg-emerald-100 text-emerald-700',
            'bg-indigo-100 text-indigo-700',
            'bg-orange-100 text-orange-700',
            'bg-teal-100 text-teal-700',
        ];

        // Fetch or prepare submissions
        $instructorIds = $instructors->pluck('id');
        $existingSubmissions = SemesterSubmission::whereIn('instructor_id', $instructorIds)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->get()
            ->keyBy('instructor_id');

        $rows = [];
        $counts = [
            'pending' => 0,
            'approved' => 0,
            'correction_required' => 0,
            'rejected' => 0,
            'total' => 0,
        ];

        foreach ($instructors as $inst) {
            $sub = $existingSubmissions->get($inst->id);

            // Find or create initial submission state
            if (!$sub) {
                $sub = SemesterSubmission::firstOrCreate([
                    'instructor_id' => $inst->id,
                    'academic_year' => $academicYear,
                    'semester'      => $semester,
                ], [
                    'department'    => $inst->department?->name ?? 'Software Engineering',
                    'section'       => $inst->section ?? 'Section A',
                    'status'        => 'pending',
                ]);
            }

            // Normalizing status for display and badges
            $rawStatus = strtolower($sub->status ?? 'pending');
            $displayStatus = match ($rawStatus) {
                'approved' => 'Approved',
                'correction_required' => 'Correction Required',
                'rejected' => 'Rejected',
                'submitted', 'under_review' => 'Pending',
                default => 'Pending',
            };

            // Update summary counters
            $counts['total']++;
            if ($rawStatus === 'approved') {
                $counts['approved']++;
            } elseif ($rawStatus === 'correction_required') {
                $counts['correction_required']++;
            } elseif ($rawStatus === 'rejected') {
                $counts['rejected']++;
            } else {
                $counts['pending']++;
            }

            // Initials calculation
            $nameParts = preg_split('/\s+/', trim($inst->name));
            $initials = '';
            if (count($nameParts) >= 2) {
                $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
            } elseif (count($nameParts) === 1 && strlen($nameParts[0]) > 0) {
                $initials = strtoupper(substr($nameParts[0], 0, min(2, strlen($nameParts[0]))));
            } else {
                $initials = 'IN';
            }

            $color = $colorPalettes[$inst->id % count($colorPalettes)];

            // Course & Credit calculation
            $assignedCourse = $inst->assignedCourses->first();
            $courseTitle = $assignedCourse?->title ?? ($inst->course_name ?: ($inst->department?->name ?? 'Computer Science'));
            $courseCode = $assignedCourse?->code ?? ($inst->course_code ?: '');
            $credit = $assignedCourse?->credits ?? 5;
            $section = $inst->section ?: ($assignedCourse?->section ?: 'Section A');

            // Submitted date format
            $submittedStr = "May 22, 2026\n03:15 PM";
            $submittedDate = 'May 22, 2026';
            $submittedTime = '03:15 PM';

            if ($sub->submitted_at) {
                $submittedDate = $sub->submitted_at->format('M d, Y');
                $submittedTime = $sub->submitted_at->format('h:i A');
                $submittedStr = "{$submittedDate}\n{$submittedTime}";
            } elseif ($rawStatus === 'pending') {
                $submittedDate = 'May 20, 2026';
                $submittedTime = '02:00 PM';
                $submittedStr = "{$submittedDate}\n{$submittedTime}";
            }

            // Student count
            $studentsCount = User::where('role', 'student')
                ->where('department_id', $inst->department_id)
                ->count();
            if ($studentsCount === 0) {
                $studentsCount = 40;
            }

            $rowItem = [
                'id'            => $sub->id,
                'submission_id' => $sub->id,
                'instructor_id' => $inst->id,
                'name'          => $inst->name,
                'email'         => $inst->email,
                'initials'      => $initials,
                'color'         => $color,
                'department'    => $inst->department?->name ?? 'Software Engineering',
                'course'        => $courseTitle,
                'course_code'   => $courseCode,
                'section'       => $section,
                'courses'       => $credit, // Column CREDIT renders this value
                'credit'        => $credit,
                'students'      => $studentsCount,
                'submitted'     => $submittedStr,
                'submitted_date'=> $submittedDate,
                'submitted_time'=> $submittedTime,
                'status'        => $displayStatus,
                'raw_status'    => $rawStatus,
                'remarks'       => $sub->remarks ?? '',
                'year_level'    => $inst->year_level ?? '1st Year',
                'academic_year' => $sub->academic_year,
                'semester'      => $sub->semester,
            ];

            // Apply filter queries in memory
            $matchesStatus = true;
            if ($statusFilter && $statusFilter !== 'All Statuses') {
                $matchesStatus = (strtolower($displayStatus) === strtolower($statusFilter));
            }

            $matchesSearch = true;
            if ($searchQuery) {
                $matchesSearch = (
                    str_contains(strtolower($inst->name), $searchQuery) ||
                    str_contains(strtolower($inst->email), $searchQuery) ||
                    str_contains(strtolower($rowItem['department']), $searchQuery) ||
                    str_contains(strtolower($courseTitle), $searchQuery) ||
                    str_contains(strtolower($section), $searchQuery)
                );
            }

            if ($matchesStatus && $matchesSearch) {
                $rows[] = $rowItem;
            }
        }

        $allDepartments = Department::orderBy('name')->pluck('name')->unique()->values();

        return response()->json([
            'semester_info' => [
                'academicYear'       => $academicYear,
                'semester'           => $semester,
                'department'         => $userDept?->name ?? 'Software Engineering',
                'pendingReview'      => $counts['pending'],
                'approved'           => $counts['approved'],
                'correctionRequired' => $counts['correction_required'],
                'rejected'           => $counts['rejected'],
                'total'              => $counts['total'],
            ],
            'instructors'   => $rows,
            'departments'   => $allDepartments,
            'semesters'     => [
                '2025/2026 — Second Semester',
                '2025/2026 — First Semester',
                '2024/2025 — Second Semester',
                '2024/2025 — First Semester',
            ],
        ]);
    }

    /**
     * Show detail for a specific year-level / id
     */
    public function show(Request $request, $id): JsonResponse
    {
        return $this->details($request, $id);
    }

    /**
     * Update status of an instructor semester submission
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status'  => 'required|string',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $normalizedStatus = strtolower(str_replace(' ', '_', trim($request->status)));

        // Valid statuses
        $allowed = ['pending', 'submitted', 'under_review', 'approved', 'rejected', 'correction_required'];
        if (!in_array($normalizedStatus, $allowed)) {
            $normalizedStatus = 'pending';
        }

        // Find submission by ID
        $submission = SemesterSubmission::with('instructor')->find($id);

        if (!$submission) {
            // Find by instructor_id
            $submission = SemesterSubmission::where('instructor_id', $id)->first();
        }

        if (!$submission) {
            $inst = User::find($id);
            if ($inst) {
                $submission = SemesterSubmission::create([
                    'instructor_id' => $inst->id,
                    'academic_year' => $request->input('academic_year', '2025/2026'),
                    'semester'      => $request->input('semester', 'Second Semester'),
                    'department'    => $inst->department?->name ?? 'Software Engineering',
                    'section'       => $inst->section ?? 'Section A',
                    'status'        => $normalizedStatus,
                    'remarks'       => $request->remarks,
                    'submitted_at'  => now(),
                ]);
            }
        }

        if (!$submission) {
            return response()->json(['message' => 'Submission record not found.'], 404);
        }

        $submission->status = $normalizedStatus;

        if ($normalizedStatus === 'approved') {
            $submission->approved_at = now();
        } elseif ($normalizedStatus === 'pending' || $normalizedStatus === 'correction_required') {
            $submission->approved_at = null;
        }

        if ($request->has('remarks')) {
            $submission->remarks = $request->remarks;
        }

        $submission->save();

        $displayStatus = match ($normalizedStatus) {
            'approved' => 'Approved',
            'correction_required' => 'Correction Required',
            'rejected' => 'Rejected',
            default => 'Pending',
        };

        // Record Activity Log
        $instName = $submission->instructor?->name ?? 'Instructor';
        LogActivity::record(
            'Updated',
            'Semester Submissions',
            "{$displayStatus} semester submission for {$instName}" . ($submission->remarks ? ": {$submission->remarks}" : '')
        );

        return response()->json([
            'message'    => "Semester submission updated to {$displayStatus} successfully.",
            'submission' => [
                'id'            => $submission->id,
                'status'        => $displayStatus,
                'raw_status'    => $submission->status,
                'remarks'       => $submission->remarks,
                'approved_at'   => $submission->approved_at?->format('M d, Y h:i A'),
            ]
        ]);
    }
}
