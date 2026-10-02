<?php

namespace App\Http\Controllers\Api\V1\DeptHead;

use App\Exports\DepartmentInstructorExport;
use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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

    /**
     * Export department instructors as PDF, Excel (.xlsx), or CSV.
     */
    public function export(Request $request): JsonResponse
    {
        $deptId = $this->resolveDeptId($request);
        $format = strtolower($request->query('format', 'excel'));

        $dept = Department::find($deptId);
        $deptName = $dept ? $dept->name : 'Department';

        $query = User::where('department_id', $deptId)
            ->whereIn('role', ['instructor', 'dept_head'])
            ->with(['assignedCourses', 'coInstructorCourses', 'creator', 'department']);

        if ($request->filled('status') && $request->status !== 'all') {
            $status = strtolower($request->status);
            if ($status === 'active') {
                $query->where(function ($q) {
                    $q->where('status', 'active')->orWhereNull('status');
                });
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('year') && $request->year !== 'all') {
            $query->where('year_level', $request->year);
        }

        if ($request->filled('section') && $request->section !== 'all') {
            $query->where('section', $request->section);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_no', 'like', "%{$search}%");
            });
        }

        $instructors = $query->orderBy('name')->get();
        $dateStr = now()->format('Y-m-d');
        $sanitizedDept = preg_replace('/[^A-Za-z0-9_\-]/', '_', $deptName);
        $baseFileName = "{$sanitizedDept}_Instructors_{$dateStr}";

        if ($format === 'pdf') {
            return $this->exportInstructorsPdf($instructors, $deptName, $baseFileName);
        }

        if ($format === 'csv') {
            return $this->exportInstructorsCsv($instructors, $deptName, $baseFileName);
        }

        return $this->exportInstructorsExcel($instructors, $deptName, $baseFileName);
    }

    /**
     * Export instructors as PDF using Dompdf.
     */
    private function exportInstructorsPdf($instructors, string $deptName, string $baseFileName): JsonResponse
    {
        $generatedAt = now()->format('F j, Y  H:i');
        $totalRecords = $instructors->count();

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<title>' . htmlspecialchars($deptName) . ' — Department Instructors</title>';
        $html .= '<style>
            @page { margin: 25px 25px 35px 25px; }
            body { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 8px; color: #1e293b; }
            .header-table { width: 100%; border-bottom: 2px solid #5138ed; padding-bottom: 10px; margin-bottom: 12px; }
            .university-title { font-size: 15px; font-weight: bold; color: #1e1b4b; text-transform: uppercase; letter-spacing: 0.5px; }
            .dept-title { font-size: 11px; font-weight: bold; color: #5138ed; margin-top: 2px; }
            .meta { font-size: 8px; color: #64748b; text-align: right; line-height: 1.4; }
            table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
            table.data-table th { background: #5138ed; color: #ffffff; padding: 6px 4px; text-align: left; font-size: 7.5px; font-weight: bold; text-transform: uppercase; }
            table.data-table td { padding: 5px 4px; border-bottom: 1px solid #e2e8f0; font-size: 7px; color: #334155; }
            table.data-table tr:nth-child(even) td { background: #f8fafc; }
            .badge-active { display: inline-block; padding: 2px 5px; border-radius: 4px; font-weight: bold; color: #15803d; background: #dcfce7; font-size: 6.5px; }
            .badge-leave { display: inline-block; padding: 2px 5px; border-radius: 4px; font-weight: bold; color: #b45309; background: #fef3c7; font-size: 6.5px; }
            .badge-inactive { display: inline-block; padding: 2px 5px; border-radius: 4px; font-weight: bold; color: #be123c; background: #ffe4e6; font-size: 6.5px; }
            .badge-full { display: inline-block; padding: 2px 5px; border-radius: 4px; font-weight: bold; color: #4338ca; background: #e0e7ff; font-size: 6.5px; }
            .badge-part { display: inline-block; padding: 2px 5px; border-radius: 4px; font-weight: bold; color: #0284c7; background: #e0f2fe; font-size: 6.5px; }
            .footer { position: fixed; bottom: 10px; left: 25px; right: 25px; font-size: 7.5px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 4px; }
        </style></head><body>';

        $html .= '<table class="header-table"><tr>';
        $html .= '<td><div class="university-title">Wollo University</div>';
        $html .= '<div class="dept-title">Department of ' . htmlspecialchars($deptName) . ' — Instructors & Academic Staff</div></td>';
        $html .= '<td class="meta"><strong>Date Generated:</strong> ' . $generatedAt . '<br><strong>Total Instructors:</strong> ' . $totalRecords . '</td>';
        $html .= '</tr></table>';

        $html .= '<table class="data-table"><thead><tr>';
        $html .= '<th style="width: 25px;">#</th>';
        $html .= '<th>Instructor Name</th>';
        $html .= '<th>Employee ID</th>';
        $html .= '<th>Email Address</th>';
        $html .= '<th>Phone</th>';
        $html .= '<th>Assigned Courses</th>';
        $html .= '<th>Credits</th>';
        $html .= '<th>Type</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Joined Date</th>';
        $html .= '</tr></thead><tbody>';

        if ($instructors->isEmpty()) {
            $html .= '<tr><td colspan="10" style="text-align: center; padding: 15px; color: #94a3b8;">No instructor records found in this department.</td></tr>';
        } else {
            foreach ($instructors as $i => $inst) {
                $status = strtolower($inst->status ?? 'active');
                if ($status === 'on_leave' || $status === 'on leave') {
                    $statusBadge = '<span class="badge-leave">On Leave</span>';
                } elseif ($status === 'inactive') {
                    $statusBadge = '<span class="badge-inactive">Inactive</span>';
                } else {
                    $statusBadge = '<span class="badge-active">Active</span>';
                }

                $et = strtolower($inst->employment_type ?? 'full_time');
                $typeBadge = ($et === 'part_time' || $et === 'part time')
                    ? '<span class="badge-part">Part-Time</span>'
                    : '<span class="badge-full">Full-Time</span>';

                $courses = $inst->assignedCourses?->pluck('title')->filter()->join(', ');
                if (empty($courses)) {
                    $courses = 'No Courses';
                }
                $credits = $inst->assignedCourses?->sum('credits') ?? 0;
                $joinedDate = $inst->created_at ? $inst->created_at->format('M d, Y') : '—';

                $html .= '<tr>';
                $html .= '<td>' . ($i + 1) . '</td>';
                $html .= '<td><strong>' . htmlspecialchars($inst->name ?? '') . '</strong></td>';
                $html .= '<td>' . htmlspecialchars($inst->id_no ?? (string)$inst->id) . '</td>';
                $html .= '<td>' . htmlspecialchars($inst->email ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($inst->phone ?? '—') . '</td>';
                $html .= '<td>' . htmlspecialchars($courses) . '</td>';
                $html .= '<td>' . $credits . '</td>';
                $html .= '<td>' . $typeBadge . '</td>';
                $html .= '<td>' . $statusBadge . '</td>';
                $html .= '<td>' . $joinedDate . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table>';
        $html .= '<div class="footer">Wollo University Online Examination System &bull; Confidential Department Document &bull; Generated by Department Head</div>';
        $html .= '</body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $pdfOutput = $dompdf->output();

        LogActivity::record('Exported', 'Instructors', "Exported {$totalRecords} instructors list as PDF ({$deptName})");

        return response()->json([
            'file'     => base64_encode($pdfOutput),
            'filename' => $baseFileName . '.pdf',
            'format'   => 'pdf',
        ]);
    }

    /**
     * Export instructors as Excel (.xlsx).
     */
    private function exportInstructorsExcel($instructors, string $deptName, string $baseFileName): JsonResponse
    {
        $export = new DepartmentInstructorExport($instructors);
        $xlsxBytes = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);

        LogActivity::record('Exported', 'Instructors', "Exported {$instructors->count()} instructors list as Excel ({$deptName})");

        return response()->json([
            'file'     => base64_encode($xlsxBytes),
            'filename' => $baseFileName . '.xlsx',
            'format'   => 'xlsx',
        ]);
    }

    /**
     * Export instructors as CSV (Excel-compatible UTF-8 BOM).
     */
    private function exportInstructorsCsv($instructors, string $deptName, string $baseFileName): JsonResponse
    {
        ob_start();
        $handle = fopen('php://output', 'w');
        // UTF-8 BOM for Microsoft Excel auto-detect
        fputs($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            '#', 'Full Name', 'Employee ID', 'Email Address', 'Phone', 'Gender',
            'Department', 'Assigned Courses', 'Total Credits', 'Employment Type',
            'Status', 'Academic Year', 'Section', 'Created By', 'Joined Date'
        ]);

        foreach ($instructors as $i => $inst) {
            $courses = $inst->assignedCourses?->pluck('title')->filter()->join(', ');
            if (empty($courses)) {
                $courses = 'No Courses';
            }
            $credits = $inst->assignedCourses?->sum('credits') ?? 0;
            $employment = ucfirst(str_replace('_', ' ', $inst->employment_type ?? 'full_time'));
            $creatorName = $inst->creator ? $inst->creator->name : 'Super Admin';

            fputcsv($handle, [
                $i + 1,
                $inst->name ?? '',
                $inst->id_no ?? (string)$inst->id,
                $inst->email ?? '',
                $inst->phone ?? '—',
                $inst->gender ?? '—',
                $inst->department ? $inst->department->name : $deptName,
                $courses,
                $credits,
                $employment,
                ucfirst($inst->status ?? 'active'),
                $inst->year_level ?? '—',
                $inst->section ?? '—',
                $creatorName,
                $inst->created_at ? $inst->created_at->format('M d, Y') : '',
            ]);
        }
        fclose($handle);
        $csvContent = ob_get_clean();

        LogActivity::record('Exported', 'Instructors', "Exported {$instructors->count()} instructors list as CSV ({$deptName})");

        return response()->json([
            'file'     => base64_encode($csvContent),
            'filename' => $baseFileName . '.csv',
            'format'   => 'csv',
        ]);
    }
}
