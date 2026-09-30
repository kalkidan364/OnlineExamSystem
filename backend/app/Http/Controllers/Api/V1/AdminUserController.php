<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Helpers\LogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Smalot\PdfParser\Parser;

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
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('avatars', 'public');
        }

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
            'profile_picture' => $profilePicturePath,
        ]);

        $roleName = ucfirst(str_replace('_', ' ', $user->role));
        $module = match($user->role) {
            'student'   => 'Students',
            'instructor' => 'Instructors',
            'dept_head' => 'Instructors',
            default     => 'Users',
        };
        LogActivity::record(
            'Created',
            $module,
            "Created a new $roleName \"{$user->name}\""
        );

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
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name', 'email', 'username', 'role', 'department_id',
            'academic_year', 'year_level', 'semester', 'section', 'id_no',
            'phone', 'gender', 'status'
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $roleName = ucfirst(str_replace('_', ' ', $user->role));
        $module = match($user->role) {
            'student'   => 'Students',
            'instructor' => 'Instructors',
            'dept_head' => 'Instructors',
            default     => 'Users',
        };
        LogActivity::record(
            'Updated',
            $module,
            "Updated $roleName \"{$user->name}\""
        );

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
        $roleName = ucfirst(str_replace('_', ' ', $user->role));
        $userName = $user->name;
        $module = match($user->role) {
            'student'   => 'Students',
            'instructor' => 'Instructors',
            'dept_head' => 'Instructors',
            default     => 'Users',
        };
        
        $user->delete();

        LogActivity::record(
            'Deleted',
            $module,
            "Deleted $roleName \"$userName\""
        );

        return response()->json(['message' => 'User deleted successfully.']);
    }

    /**
     * Export users as CSV (Excel-compatible) or PDF.
     * Pure PHP — no ZipArchive needed.
     */
    public function export(Request $request)
    {
        $role = $request->query('role', 'student');
        $format = $request->query('format', 'csv');
        $roles = explode(',', $role);

        $query = User::whereIn('role', $roles);

        // Apply filters
        if ($request->filled('department') && $request->department !== 'all') {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('name', $request->department);
            });
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('section') && $request->section !== 'all') {
            $query->where('section', $request->section);
        }
        if ($request->filled('year') && $request->year !== 'all') {
            $query->where('year_level', $request->year);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_no', 'like', "%{$search}%");
            });
        }

        $users = $query->with('department')->get();
        $fileName = "export_{$role}_" . date('Y-m-d');

        if ($format === 'pdf') {
            return $this->exportUsersPdf($users, $fileName);
        }

        return $this->exportUsersCsv($users, $fileName);
    }

    private function exportUsersCsv($users, string $fileName)
    {
        ob_start();
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
        $csvContent = ob_get_clean();

        return response()->json([
            'file' => base64_encode($csvContent),
            'filename' => $fileName . '.csv'
        ]);
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

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        return response()->json([
            'file' => base64_encode($pdfContent),
            'filename' => $fileName . '.pdf'
        ]);
    }

    /**
     * Import users from a CSV or PDF file.
     * Full validation against form format with detailed error feedback.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file',
            'role' => 'required|string',
        ]);

        $role = $request->role;
        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['csv', 'txt', 'pdf'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file format. Please upload a CSV (.csv) or PDF (.pdf) file.',
                'errors' => ['Only .csv and .pdf file formats are supported.']
            ], 422);
        }

        // Parse rows from file
        try {
            if ($extension === 'pdf') {
                $rows = $this->parsePdfRows($file->getPathname());
            } else {
                $rows = $this->parseCsvRows($file->getPathname());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse file: ' . $e->getMessage(),
                'errors' => ['File parsing error: ' . $e->getMessage()]
            ], 422);
        }

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'The uploaded file is empty or contains no readable instructor table data.',
                'errors' => ['No data rows could be extracted from the uploaded file. Please ensure the file has a valid table with headers.']
            ], 422);
        }

        // Cache departments for smart matching
        $departments = Department::all(['id', 'name', 'code'])->toArray();
        $availableDeptNames = array_map(fn($d) => $d['name'] . ($d['code'] ? " ({$d['code']})" : ''), $departments);

        // Normalize rows and check headers
        $normalizedRows = [];
        foreach ($rows as $row) {
            $norm = $this->normalizeRowKeys($row);
            $normalizedRows[] = $norm;
        }

        // For instructor role: Verify required Add Instructor form columns exist
        if ($role === 'instructor') {
            $hasName = false;
            $hasEmail = false;
            $hasPhone = false;
            $hasDept = false;

            foreach ($normalizedRows as $r) {
                if (!empty($r['name'])) $hasName = true;
                if (!empty($r['email'])) $hasEmail = true;
                if (!empty($r['phone'])) $hasPhone = true;
                if (!empty($r['department'])) $hasDept = true;
            }

            $missingHeaders = [];
            if (!$hasName) $missingHeaders[] = 'Full Name';
            if (!$hasEmail) $missingHeaders[] = 'Email Address';
            if (!$hasPhone) $missingHeaders[] = 'Phone Number';
            if (!$hasDept) $missingHeaders[] = 'Department';

            if (!empty($missingHeaders)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The file format does not fulfill the Add Instructor form requirements. Missing column(s): ' . implode(', ', $missingHeaders),
                    'errors' => [
                        'Missing required column(s): ' . implode(', ', $missingHeaders) . '.',
                        'The instructor file MUST fulfill the Add Instructor form format with these columns:',
                        '• Full Name (required)',
                        '• Email Address (required)',
                        '• Phone Number (required)',
                        '• Department (required, e.g. "Software Engineering", "Computer Science")',
                        'Optional columns: Gender, Employee ID, Academic Year Level, Semester, Section, Password'
                    ],
                    'format_guide' => [
                        'required_columns' => ['Full Name', 'Email Address', 'Phone Number', 'Department'],
                        'optional_columns' => ['Gender', 'Employee ID', 'Academic Year Level', 'Semester', 'Section', 'Password'],
                        'available_departments' => $availableDeptNames,
                    ]
                ], 422);
            }
        }

        // Validate each row
        $errors = [];
        $validRecords = [];
        $seenEmails = [];
        $seenIdNos = [];

        foreach ($normalizedRows as $idx => $r) {
            $rowNum = $idx + 1; // 1-based data row
            $name = trim($r['name'] ?? '');
            $email = strtolower(trim($r['email'] ?? ''));
            $phone = trim($r['phone'] ?? '');
            $deptVal = trim($r['department'] ?? '');
            $idNo = trim($r['id_no'] ?? '');
            $gender = trim($r['gender'] ?? '');
            $yearLevel = trim($r['year_level'] ?? '');
            $semester = trim($r['semester'] ?? '');
            $section = trim($r['section'] ?? '');
            $username = trim($r['username'] ?? '');
            $password = trim($r['password'] ?? '');

            // Skip completely empty rows
            if (empty($name) && empty($email) && empty($phone) && empty($deptVal)) {
                continue;
            }

            // 1. Name validation
            if (empty($name)) {
                $errors[] = "Row {$rowNum}: Full Name is required.";
            }

            // 2. Email validation
            if (empty($email)) {
                $errors[] = "Row {$rowNum}: Email Address is required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNum}: '{$email}' is not a valid email address.";
            } elseif (isset($seenEmails[$email])) {
                $errors[] = "Row {$rowNum}: Email '{$email}' is duplicated within the uploaded file.";
            } elseif (User::where('email', $email)->exists()) {
                $errors[] = "Row {$rowNum}: Email '{$email}' is already registered in the system.";
            } else {
                $seenEmails[$email] = true;
            }

            // 3. Phone validation
            if ($role === 'instructor' && empty($phone)) {
                $errors[] = "Row {$rowNum}: Phone Number is required.";
            }

            // 4. Department validation
            $deptId = null;
            if (empty($deptVal)) {
                if ($role === 'instructor') {
                    $errors[] = "Row {$rowNum}: Department is required.";
                }
            } else {
                $deptId = $this->findDepartmentId($deptVal, $departments);
                if (!$deptId) {
                    $errors[] = "Row {$rowNum}: Department '{$deptVal}' does not exist in the system. Available: " . implode(', ', $availableDeptNames);
                }
            }

            // 5. Gender normalization
            if ($gender) {
                $normGender = strtolower($gender);
                if (in_array($normGender, ['m', 'male'])) {
                    $gender = 'Male';
                } elseif (in_array($normGender, ['f', 'female'])) {
                    $gender = 'Female';
                } elseif (in_array($normGender, ['other'])) {
                    $gender = 'Other';
                } else {
                    $errors[] = "Row {$rowNum}: Invalid gender '{$gender}'. Must be 'Male' or 'Female'.";
                }
            }

            // 6. Employee ID uniqueness
            if ($idNo) {
                if (isset($seenIdNos[$idNo])) {
                    $errors[] = "Row {$rowNum}: Employee ID '{$idNo}' is duplicated in the uploaded file.";
                } elseif (User::where('id_no', $idNo)->exists()) {
                    $errors[] = "Row {$rowNum}: Employee ID '{$idNo}' already belongs to another user in the system.";
                } else {
                    $seenIdNos[$idNo] = true;
                }
            }

            $validRecords[] = [
                'name'          => $name,
                'email'         => $email,
                'phone'         => $phone ?: null,
                'department_id' => $deptId,
                'gender'        => $gender ?: null,
                'id_no'         => $idNo ?: null,
                'year_level'    => $yearLevel ?: null,
                'semester'      => $semester ?: null,
                'section'       => $section ?: null,
                'username'      => $username ?: null,
                'password'      => $password ?: 'Password123!',
                'role'          => $role,
                'status'        => 'active',
            ];
        }

        // If any format errors were found, reject import and report all issues
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'The uploaded file does not fulfill the Add Instructor format. Found ' . count($errors) . ' issue(s).',
                'errors'  => $errors,
                'format_guide' => [
                    'required_columns' => ['Full Name', 'Email Address', 'Phone Number', 'Department'],
                    'optional_columns' => ['Gender', 'Employee ID', 'Academic Year Level', 'Semester', 'Section', 'Password'],
                    'available_departments' => $availableDeptNames,
                ]
            ], 422);
        }

        if (empty($validRecords)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid instructor records found in the file to import.',
                'errors'  => ['The file contains no instructor data rows.']
            ], 422);
        }

        // Insert inside transaction
        $createdUsers = [];
        DB::transaction(function () use ($validRecords, &$createdUsers) {
            foreach ($validRecords as $r) {
                $username = $r['username'];
                if (!$username) {
                    $base = strtolower(explode('@', $r['email'])[0]);
                    $clean = preg_replace('/[^a-z0-9_.]/', '', $base) ?: 'instructor';
                    $candidate = $clean;
                    $counter = 1;
                    while (User::where('username', $candidate)->exists()) {
                        $candidate = $clean . $counter++;
                    }
                    $username = $candidate;
                }

                $user = User::create([
                    'name'          => $r['name'],
                    'email'         => $r['email'],
                    'username'      => $username,
                    'password'      => Hash::make($r['password']),
                    'role'          => $r['role'],
                    'department_id' => $r['department_id'],
                    'id_no'         => $r['id_no'],
                    'phone'         => $r['phone'],
                    'gender'        => $r['gender'],
                    'year_level'    => $r['year_level'],
                    'semester'      => $r['semester'],
                    'section'       => $r['section'],
                    'status'        => 'active',
                ]);

                $createdUsers[] = [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'department'     => $user->department?->name ?? 'N/A',
                    'employeeId'     => $user->id_no,
                    'phone'          => $user->phone,
                ];

                LogActivity::record(
                    'Created',
                    'Instructors',
                    "Imported Instructor \"{$user->name}\""
                );
            }
        });

        $count = count($createdUsers);
        return response()->json([
            'success'     => true,
            'message'     => "Successfully imported {$count} instructor(s) into the system.",
            'imported'    => $count,
            'instructors' => $createdUsers,
        ]);
    }

    /**
     * Parse rows from CSV / TXT file.
     */
    private function parseCsvRows(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Cannot open CSV file.');
        }

        // Strip UTF-8 BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Detect delimiter
        $firstLine = fgets($handle);
        if (!$firstLine) {
            fclose($handle);
            return [];
        }
        $delimiters = [',', ';', "\t"];
        $bestDelim = ',';
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $count = substr_count($firstLine, $d);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelim = $d;
            }
        }
        rewind($handle);
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headings = fgetcsv($handle, 0, $bestDelim);
        if (!$headings) {
            fclose($handle);
            return [];
        }

        $headings = array_map(fn($h) => strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', (string)$h), '_')), $headings);

        $rows = [];
        while (($data = fgetcsv($handle, 0, $bestDelim)) !== false) {
            if (empty(array_filter($data, fn($v) => trim((string)$v) !== ''))) continue;
            $row = [];
            foreach ($headings as $i => $h) {
                if ($h === '') continue;
                $row[$h] = isset($data[$i]) ? trim((string)$data[$i]) : '';
            }
            $rows[] = $row;
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse table rows from PDF file.
     */
    private function parsePdfRows(string $filePath): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $allRows = [];

        foreach ($pdf->getPages() as $page) {
            $textArray = array_values(array_filter(array_map('trim', $page->getTextArray()), fn($v) => $v !== ''));
            if (empty($textArray)) continue;

            // Strategy 1: Table text array matching
            $headerStartIndex = null;
            foreach ($textArray as $idx => $item) {
                $lower = strtolower($item);
                if ($lower === '#' || $lower === 'full name' || $lower === 'name' || $lower === 'email') {
                    $headerStartIndex = $idx;
                    break;
                }
            }

            if ($headerStartIndex !== null) {
                $headers = [];
                $dataStartIndex = null;
                for ($i = $headerStartIndex; $i < count($textArray); $i++) {
                    $item = $textArray[$i];
                    if (!empty($headers) && ($item === '1' || filter_var($item, FILTER_VALIDATE_EMAIL))) {
                        $dataStartIndex = $i;
                        break;
                    }
                    $normHeader = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $item), '_'));
                    if ($normHeader !== '') {
                        $headers[] = $normHeader;
                    }
                }

                $numCols = count($headers);
                if ($dataStartIndex !== null && $numCols >= 3) {
                    $dataSlice = array_slice($textArray, $dataStartIndex);
                    $chunks = array_chunk($dataSlice, $numCols);
                    $pageRows = [];
                    foreach ($chunks as $chunk) {
                        if (count($chunk) === $numCols) {
                            $row = array_combine($headers, $chunk);
                            if (strtolower($row['email'] ?? '') !== 'email') {
                                $pageRows[] = $row;
                            }
                        }
                    }
                    if (!empty($pageRows)) {
                        $allRows = array_merge($allRows, $pageRows);
                        continue;
                    }
                }
            }

            // Strategy 2: Y-coordinate text grouping
            $tm = $page->getDataTm();
            if (!empty($tm)) {
                $rowsByY = [];
                foreach ($tm as $item) {
                    $x = $item[0][4];
                    $y = $item[0][5];
                    $text = trim($item[1]);
                    if ($text === '') continue;

                    $foundY = null;
                    foreach (array_keys($rowsByY) as $existingY) {
                        if (abs($existingY - $y) <= 3.5) {
                            $foundY = $existingY;
                            break;
                        }
                    }
                    if ($foundY === null) {
                        $foundY = $y;
                        $rowsByY[$foundY] = [];
                    }
                    $rowsByY[$foundY][] = ['x' => $x, 'text' => $text];
                }
                krsort($rowsByY);

                $headerRow = null;
                $dataRows = [];

                foreach ($rowsByY as $y => $cells) {
                    usort($cells, fn($a, $b) => $a['x'] <=> $b['x']);
                    $rowText = strtolower(implode(' ', array_map(fn($c) => $c['text'], $cells)));

                    if (!$headerRow && (str_contains($rowText, 'name') || str_contains($rowText, 'email'))) {
                        $headerRow = $cells;
                        continue;
                    }
                    if ($headerRow) {
                        if (str_contains($rowText, 'name') && str_contains($rowText, 'email')) {
                            continue; // repeated header on multi-page
                        }
                        $dataRows[] = $cells;
                    }
                }

                if ($headerRow && !empty($dataRows)) {
                    $colsCount = count($headerRow);
                    $colDefs = [];
                    for ($i = 0; $i < $colsCount; $i++) {
                        $currentX = $headerRow[$i]['x'];
                        $prevMid = ($i === 0) ? -9999 : ($headerRow[$i - 1]['x'] + $currentX) / 2;
                        $nextMid = ($i === $colsCount - 1) ? 9999 : ($currentX + $headerRow[$i + 1]['x']) / 2;
                        $rawName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $headerRow[$i]['text']), '_'));
                        $colDefs[] = [
                            'name'  => $rawName,
                            'min_x' => $prevMid,
                            'max_x' => $nextMid,
                        ];
                    }

                    $coordRows = [];
                    foreach ($dataRows as $cells) {
                        $record = array_fill_keys(array_column($colDefs, 'name'), '');
                        foreach ($cells as $cell) {
                            foreach ($colDefs as $col) {
                                if ($cell['x'] >= $col['min_x'] && $cell['x'] < $col['max_x']) {
                                    $record[$col['name']] = trim($record[$col['name']] . ' ' . $cell['text']);
                                    break;
                                }
                            }
                        }
                        if (!empty(array_filter($record))) {
                            $coordRows[] = $record;
                        }
                    }

                    if (!empty($coordRows)) {
                        $allRows = array_merge($allRows, $coordRows);
                        continue;
                    }
                }
            }

            // Strategy 3: Line-by-line fallback regex
            $lines = explode("\n", $page->getText());
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $line, $m)) {
                    $email = $m[1];
                    $parts = explode($email, $line);
                    $name = preg_replace('/^[0-9#\s\.-]+/', '', trim($parts[0]));
                    $phone = '';
                    if (preg_match('/(\+?[0-9]{9,15})/', $line, $pm)) {
                        $phone = $pm[1];
                    }
                    $allRows[] = [
                        'full_name'  => trim($name),
                        'email'      => $email,
                        'phone'      => $phone,
                        'department' => '',
                    ];
                }
            }
        }

        return $allRows;
    }

    /**
     * Normalize messy column keys to standard fields.
     */
    private function normalizeRowKeys(array $row): array
    {
        $norm = [
            'name'       => '',
            'email'      => '',
            'phone'      => '',
            'department' => '',
            'gender'     => '',
            'id_no'      => '',
            'year_level' => '',
            'semester'   => '',
            'section'    => '',
            'username'   => '',
            'password'   => '',
        ];

        foreach ($row as $key => $val) {
            $val = trim((string)$val);
            if ($val === '') continue;

            $k = strtolower(trim(preg_replace('/[^a-z0-9]+/', '_', (string)$key), '_'));

            // Name
            if (in_array($k, ['full_name', 'name', 'instructor_name', 'instructor', 'teacher'])) {
                if (empty($norm['name'])) $norm['name'] = $val;
            }
            // Email
            elseif (in_array($k, ['email', 'email_address', 'e_mail'])) {
                if (empty($norm['email'])) $norm['email'] = $val;
            }
            // Phone
            elseif (in_array($k, ['phone', 'phone_number', 'mobile', 'tel', 'telephone', 'contact'])) {
                if (empty($norm['phone'])) $norm['phone'] = $val;
            }
            // Department
            elseif (in_array($k, ['department', 'department_name', 'dept', 'dept_name', 'faculty', 'college'])) {
                if (empty($norm['department'])) $norm['department'] = $val;
            }
            // Gender
            elseif (in_array($k, ['gender', 'sex'])) {
                if (empty($norm['gender'])) $norm['gender'] = $val;
            }
            // Employee ID / ID No
            elseif (in_array($k, ['id_no', 'employee_id', 'employee_id_no', 'student_id_employee_id', 'id_employee_id', 'emp_id', 'id'])) {
                if ($k === 'id' && is_numeric($val) && strlen($val) < 4) {
                    // row number index
                } else {
                    if (empty($norm['id_no'])) $norm['id_no'] = $val;
                }
            }
            // Year level
            elseif (in_array($k, ['year_level', 'academic_year_level', 'year', 'level'])) {
                if (empty($norm['year_level'])) $norm['year_level'] = $val;
            }
            // Semester
            elseif (in_array($k, ['semester', 'sem'])) {
                if (empty($norm['semester'])) $norm['semester'] = $val;
            }
            // Section
            elseif (in_array($k, ['section', 'sec'])) {
                if (empty($norm['section'])) $norm['section'] = $val;
            }
            // Username
            elseif (in_array($k, ['username', 'user_name'])) {
                if (empty($norm['username'])) $norm['username'] = $val;
            }
            // Password
            elseif (in_array($k, ['password', 'pass'])) {
                if (empty($norm['password'])) $norm['password'] = $val;
            }
        }

        return $norm;
    }

    /**
     * Smart department matching by name, code, or fuzzy string.
     */
    private function findDepartmentId($val, array $departments): ?int
    {
        $clean = strtolower(trim((string)$val));
        if ($clean === '') return null;

        // 1. Exact match on name or code
        foreach ($departments as $d) {
            if (strtolower($d['name']) === $clean || (isset($d['code']) && strtolower($d['code']) === $clean)) {
                return (int)$d['id'];
            }
        }

        // 2. Alphanumeric match (ignoring spaces & punctuation)
        $cleanAlpha = preg_replace('/[^a-z0-9]/', '', $clean);
        foreach ($departments as $d) {
            $nameAlpha = preg_replace('/[^a-z0-9]/', '', strtolower($d['name']));
            $codeAlpha = isset($d['code']) ? preg_replace('/[^a-z0-9]/', '', strtolower($d['code'])) : '';
            if ($nameAlpha === $cleanAlpha || ($codeAlpha !== '' && $codeAlpha === $cleanAlpha)) {
                return (int)$d['id'];
            }
        }

        // 3. Substring or Levenshtein tolerance
        foreach ($departments as $d) {
            $dName = strtolower($d['name']);
            if (str_contains($clean, $dName) || str_contains($dName, $clean)) {
                return (int)$d['id'];
            }
            if (levenshtein($clean, $dName) <= 4) {
                return (int)$d['id'];
            }
        }

        return null;
    }
}
