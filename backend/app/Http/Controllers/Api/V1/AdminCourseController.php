<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminCourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): JsonResponse
    {
        $courses = Course::with(['department', 'instructor', 'creator'])->get();
        return response()->json(['data' => $courses]);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code',
            'credits' => 'required|integer',
            'semester' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'instructor_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request) {
                    $instructor = User::where('id', $value)->where('department_id', $request->department_id)->where('role', 'instructor')->first();
                    if (!$instructor) {
                        $fail('The selected instructor is invalid or does not belong to the selected department.');
                    }
                },
            ],
            'level' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'code' => $request->code,
            'credits' => $request->credits,
            'semester' => $request->semester,
            'department_id' => $request->department_id,
            'instructor_id' => $request->instructor_id,
            'level' => $request->level,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'data' => $course->load(['department', 'instructor'])
        ], 201);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:courses,code,' . $course->id,
            'credits' => 'sometimes|integer',
            'semester' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
            'department_id' => 'sometimes|exists:departments,id',
            'section' => 'nullable|string',
            'instructor_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $course) {
                    $deptId = $request->input('department_id', $course->department_id);
                    $instructor = User::where('id', $value)->where('department_id', $deptId)->whereIn('role', ['instructor', 'dept_head'])->first();
                    if (!$instructor) {
                        $fail('The selected instructor is invalid or does not belong to the department.');
                    }
                },
            ],
            'co_instructor_id' => 'nullable|exists:users,id',
            'level' => 'sometimes|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $course->update($request->only(['title', 'code', 'credits', 'semester', 'level', 'section', 'start_date', 'end_date', 'status', 'department_id', 'instructor_id', 'co_instructor_id']));

        // Sync instructor's section and year_level when assigning
        if ($request->has('instructor_id') && $request->instructor_id) {
            User::where('id', $request->instructor_id)->update([
                'section'    => $course->section ?? $request->section,
                'year_level' => $course->level,
                'course_code' => $course->code,
                'course_name' => $course->title,
            ]);
        }
        if ($request->has('co_instructor_id') && $request->co_instructor_id) {
            User::where('id', $request->co_instructor_id)->update([
                'section'    => $course->section ?? $request->section,
                'year_level' => $course->level,
                'course_code' => $course->code,
                'course_name' => $course->title,
            ]);
        }

        return response()->json([
            'message' => 'Course updated successfully',
            'data' => $course->load(['department', 'instructor'])
        ]);
    }

    /**
     * Delete a course.
     */
    public function destroy(Course $course): JsonResponse
    {
        $course->delete();
        return response()->json(['message' => 'Course deleted successfully.']);
    }

    /**
     * Export courses as CSV (Excel-compatible) or PDF.
     * Pure PHP — no ZipArchive needed.
     */
    public function export(Request $request)
    {
        $format   = $request->format ?? 'excel';
        $courses  = Course::with('department')->latest()->get();
        $fileName = 'courses_export_' . date('Y-m-d');

        if ($format === 'pdf') {
            return $this->exportCoursesPdf($courses, $fileName);
        }

        return $this->exportCoursesCsv($courses, $fileName);
    }

    private function exportCoursesCsv($courses, string $fileName)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($courses) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            fputcsv($handle, [
                'ID', 'Course Name', 'Course Code', 'Description',
                'Department', 'Credits', 'Level', 'Semester',
                'Start Date', 'End Date', 'Status', 'Created Date'
            ]);
            foreach ($courses as $course) {
                fputcsv($handle, [
                    $course->id,
                    $course->title ?? $course->name ?? '',
                    $course->code ?? '',
                    $course->description ?? '',
                    $course->department ? $course->department->name : '',
                    $course->credits ?? '',
                    $course->level ?? '',
                    $course->semester ?? '',
                    $course->start_date ?? '',
                    $course->end_date ?? '',
                    $course->status ?? 'active',
                    $course->created_at ? $course->created_at->format('Y-m-d') : '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCoursesPdf($courses, string $fileName)
    {
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
        $html .= '<h2>Course Export</h2>';
        $html .= '<div class="meta">Generated: ' . $generatedAt . ' &nbsp;|&nbsp; Total records: ' . $courses->count() . '</div>';
        $html .= '<table><thead><tr>
            <th>#</th><th>Course Name</th><th>Code</th><th>Department</th>
            <th>Credits</th><th>Level</th><th>Semester</th>
            <th>Start</th><th>End</th><th>Status</th>
        </tr></thead><tbody>';
        foreach ($courses as $i => $course) {
            $statusClass = ($course->status === 'active') ? 'badge-active' : 'badge-inactive';
            $html .= '<tr>
                <td>' . ($i + 1) . '</td>
                <td>' . htmlspecialchars($course->title ?? $course->name ?? '') . '</td>
                <td>' . htmlspecialchars($course->code ?? '') . '</td>
                <td>' . htmlspecialchars($course->department ? $course->department->name : '') . '</td>
                <td>' . htmlspecialchars($course->credits ?? '') . '</td>
                <td>' . htmlspecialchars($course->level ?? '') . '</td>
                <td>' . htmlspecialchars($course->semester ?? '') . '</td>
                <td>' . htmlspecialchars($course->start_date ?? '') . '</td>
                <td>' . htmlspecialchars($course->end_date ?? '') . '</td>
                <td class="' . $statusClass . '">' . ucfirst($course->status ?? 'active') . '</td>
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
     * Import courses from a CSV file.
     * Pure PHP fgetcsv — no ZipArchive needed.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        // Cache departments for fast lookup
        $departments = Department::all()->pluck('id', 'name')->mapWithKeys(function ($id, $name) {
            return [strtolower(trim($name)) => $id];
        });

        $handle = fopen($request->file('file')->getPathname(), 'r');
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
        $headings = array_map(fn($h) => strtolower(trim(preg_replace('/\s+/', '_', $h))), $headings);

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;

            $data = array_combine(array_slice($headings, 0, count($row)), $row);

            $code = trim($data['course_code'] ?? $data['code'] ?? '');
            if (empty($code)) { $skipped++; continue; }

            // Skip duplicate course codes
            if (Course::where('code', $code)->exists()) {
                $skipped++;
                continue;
            }

            $title = trim($data['course_name'] ?? $data['title'] ?? $data['name'] ?? '');
            if (empty($title)) { $skipped++; continue; }

            $deptId = null;
            $deptName = strtolower(trim($data['department'] ?? ''));
            if ($deptName) {
                $deptId = $departments[$deptName] ?? null;
            }

            Course::create([
                'title'       => $title,
                'code'        => $code,
                'description' => trim($data['description'] ?? '') ?: null,
                'credits'     => intval($data['credits'] ?? $data['credit_hours'] ?? 3),
                'level'       => trim($data['level'] ?? '') ?: null,
                'semester'    => trim($data['semester'] ?? '') ?: null,
                'start_date'  => trim($data['start_date'] ?? '') ?: null,
                'end_date'    => trim($data['end_date'] ?? '') ?: null,
                'status'      => trim($data['status'] ?? 'active'),
                'department_id' => $deptId,
                'created_by'  => auth()->id(),
            ]);

            $imported++;
        }

        fclose($handle);

        return response()->json([
            'message'  => "Import complete. {$imported} course(s) imported, {$skipped} skipped (duplicates or missing data).",
            'imported' => $imported,
            'skipped'  => $skipped,
        ]);
    }
}
