<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminUserController extends Controller
{
    /**
     * List all users (instructors, students, dept_heads).
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['department:id,name', 'assignedCourses:id,title,instructor_id', 'coInstructorCourses:id,title,co_instructor_id'])->latest();
        if ($request->has('role')) {
            $roles = explode(',', $request->role);
            $query->whereIn('role', $roles);
        }
        if ($request->has('academic_year') && $request->academic_year !== 'all') {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->has('year_level') && $request->year_level !== 'all') {
            $query->where('year_level', $request->year_level);
        }
        if ($request->has('semester') && $request->semester !== 'all') {
            $query->where('semester', $request->semester);
        }
        $users = $query->get();

        return response()->json(['data' => $users]);
    }

    /**
     * Create a new user (student or instructor) with a department assignment.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'username'      => 'nullable|string|max:255|unique:users,username',
            'role'          => 'required|in:instructor,student,dept_head,admin',
            'department_id' => 'nullable|exists:departments,id',
            'course_code'   => 'nullable|string|max:255',
            'course_name'   => 'nullable|string|max:255',
            'academic_year' => 'nullable|string|max:255',
            'year_level'    => 'nullable|string|max:255',
            'semester'      => 'nullable|string|max:255',
            'section'       => 'nullable|string|max:255',
            'id_no'         => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'gender'        => 'nullable|in:Male,Female,Other',
            'password'      => 'required|string|min:8',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'username'      => $request->username,
            'role'          => $request->role,
            'department_id' => $request->department_id,
            'course_code'   => $request->course_code,
            'course_name'   => $request->course_name,
            'academic_year' => $request->academic_year,
            'year_level'    => $request->year_level,
            'semester'      => $request->semester,
            'section'       => $request->section,
            'id_no'         => $request->id_no,
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'password'      => Hash::make($request->password),
        ]);

        return response()->json([
            'data'    => $user->load('department:id,name'),
            'message' => 'User created successfully.',
        ], 201);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|email|unique:users,email,' . $user->id,
            'username'      => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'role'          => 'sometimes|in:instructor,student,dept_head,admin',
            'department_id' => 'nullable|exists:departments,id',
            'academic_year' => 'nullable|string|max:255',
            'year_level'    => 'nullable|string|max:255',
            'semester'      => 'nullable|string|max:255',
            'section'       => 'nullable|string|max:255',
            'id_no'         => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'gender'        => 'nullable|in:Male,Female,Other',
            'password'      => 'nullable|string|min:8',
        ]);

        $data = $request->only([
            'name', 'email', 'username', 'role', 'department_id',
            'academic_year', 'year_level', 'semester', 'section', 'id_no',
            'phone', 'gender', 'status'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'data'    => $user->load('department:id,name'),
            'message' => 'User updated successfully.',
        ]);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    /**
     * Export users as CSV (Excel-compatible) or PDF.
     * Pure PHP — no ZipArchive needed.
     */
    public function export(Request $request)
    {
        $role   = $request->role ?? 'student';
        $format = $request->format ?? 'excel';
        $roles  = explode(',', $role);

        $users = User::with('department')->whereIn('role', $roles)->latest()->get();

        $fileName = 'export_' . str_replace(',', '_', $role) . '_' . date('Y-m-d');

        if ($format === 'pdf') {
            return $this->exportUsersPdf($users, $fileName);
        }

        // Default: CSV (Excel opens .csv natively — no ZipArchive needed)
        return $this->exportUsersCsv($users, $fileName);
    }

    private function exportUsersCsv($users, string $fileName)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");
            // Headings
            fputcsv($handle, [
                'ID', 'Full Name', 'Email', 'Username', 'Password',
                'Student ID / Employee ID', 'Department', 'Academic Year',
                'Year Level', 'Semester', 'Section', 'Phone', 'Gender',
                'Status', 'Registered Date'
            ]);
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->username ?? '',
                    '', // Password is never exported as plain text
                    $user->id_no ?? '',
                    $user->department ? $user->department->name : '',
                    $user->academic_year ?? '',
                    $user->year_level ?? '',
                    $user->semester ?? '',
                    $user->section ?? '',
                    $user->phone ?? '',
                    $user->gender ?? '',
                    $user->status ?? 'active',
                    $user->created_at ? $user->created_at->format('Y-m-d') : '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportUsersPdf($users, string $fileName)
    {
        $roleLabel = $users->first()?->role ?? 'Users';
        $generatedAt = now()->format('F j, Y  H:i');

        $html = '<html><head><style>
            body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8px; color: #1e293b; }
            h2 { color: #4338ca; margin-bottom: 4px; font-size: 14px; }
            .meta { color: #64748b; font-size: 8px; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; }
            th { background: #4338ca; color: #fff; padding: 5px 4px; text-align: left; font-size: 7.5px; }
            td { padding: 4px; border-bottom: 1px solid #e2e8f0; font-size: 7px; }
            tr:nth-child(even) td { background: #f8fafc; }
            .badge-active { color: #10b981; font-weight: bold; }
            .badge-inactive { color: #ef4444; font-weight: bold; }
        </style></head><body>';
        $html .= '<h2>User Export — ' . ucfirst($roleLabel) . 's</h2>';
        $html .= '<div class="meta">Generated: ' . $generatedAt . ' &nbsp;|&nbsp; Total records: ' . $users->count() . '</div>';
        $html .= '<table><thead><tr>
            <th>#</th><th>Full Name</th><th>Email</th><th>Username</th>
            <th>ID / Employee ID</th><th>Department</th>
            <th>Year Level</th><th>Semester</th><th>Section</th>
            <th>Phone</th><th>Gender</th><th>Status</th><th>Registered</th>
        </tr></thead><tbody>';
        foreach ($users as $i => $user) {
            $statusClass = ($user->status === 'active') ? 'badge-active' : 'badge-inactive';
            $html .= '<tr>
                <td>' . ($i + 1) . '</td>
                <td>' . htmlspecialchars($user->name ?? '') . '</td>
                <td>' . htmlspecialchars($user->email ?? '') . '</td>
                <td>' . htmlspecialchars($user->username ?? '') . '</td>
                <td>' . htmlspecialchars($user->id_no ?? '') . '</td>
                <td>' . htmlspecialchars($user->department ? $user->department->name : '') . '</td>
                <td>' . htmlspecialchars($user->year_level ?? '') . '</td>
                <td>' . htmlspecialchars($user->semester ?? '') . '</td>
                <td>' . htmlspecialchars($user->section ?? '') . '</td>
                <td>' . htmlspecialchars($user->phone ?? '') . '</td>
                <td>' . htmlspecialchars($user->gender ?? '') . '</td>
                <td class="' . $statusClass . '">' . ucfirst($user->status ?? 'active') . '</td>
                <td>' . ($user->created_at ? $user->created_at->format('Y-m-d') : '') . '</td>
            </tr>';
        }
        $html .= '</tbody></table></body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '.pdf"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        ]);
    }

    /**
     * Import users from a CSV file.
     * Pure PHP fgetcsv — no ZipArchive needed.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'role' => 'required|string',
        ]);

        $role = $request->role;
        $file = $request->file('file');

        // Cache departments for fast lookup
        $departments = Department::all()->pluck('id', 'name')->mapWithKeys(function ($id, $name) {
            return [strtolower(trim($name)) => $id];
        });

        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            return response()->json(['message' => 'Cannot open file.'], 400);
        }

        // Strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read heading row
        $headings = fgetcsv($handle);
        if (!$headings) {
            fclose($handle);
            return response()->json(['message' => 'File is empty or has no headings.'], 400);
        }

        // Normalize headings: lowercase, trim, replace spaces with underscores
        $headings = array_map(fn($h) => strtolower(trim(preg_replace('/\s+/', '_', $h))), $headings);

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;

            // Map row by heading keys
            $data = array_combine(array_slice($headings, 0, count($row)), $row);

            $email = trim($data['email'] ?? '');
            $idNo  = trim($data['student_id_/_employee_id'] ?? $data['id_/_employee_id'] ?? $data['student_id'] ?? $data['employee_id'] ?? $data['id_no'] ?? '');

            if (empty($email)) { $skipped++; continue; }

            // Skip duplicates
            if (User::where('email', $email)->orWhere(function ($q) use ($idNo) {
                if ($idNo) $q->where('id_no', $idNo);
            })->exists()) {
                $skipped++;
                continue;
            }

            $deptId = null;
            $deptName = strtolower(trim($data['department'] ?? ''));
            if ($deptName) {
                $deptId = $departments[$deptName] ?? null;
            }

            $password = trim($data['password'] ?? '');
            if (empty($password) || $password === '********') {
                $password = 'Password123!';
            }

            User::create([
                'name'          => trim($data['full_name'] ?? $data['name'] ?? ''),
                'email'         => $email,
                'username'      => trim($data['username'] ?? '') ?: null,
                'password'      => Hash::make($password),
                'role'          => $role,
                'id_no'         => $idNo ?: null,
                'department_id' => $deptId,
                'academic_year' => trim($data['academic_year'] ?? '') ?: null,
                'year_level'    => trim($data['year_level'] ?? '') ?: null,
                'semester'      => trim($data['semester'] ?? '') ?: null,
                'section'       => trim($data['section'] ?? '') ?: null,
                'phone'         => trim($data['phone'] ?? '') ?: null,
                'gender'        => trim($data['gender'] ?? '') ?: null,
                'status'        => trim($data['status'] ?? 'active'),
            ]);

            $imported++;
        }

        fclose($handle);

        return response()->json([
            'message'  => "Import complete. {$imported} record(s) imported, {$skipped} skipped (duplicates or empty rows).",
            'imported' => $imported,
            'skipped'  => $skipped,
        ]);
    }
}
