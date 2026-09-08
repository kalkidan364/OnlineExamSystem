<?php
$file = 'app/Http/Controllers/Api/V1/AdminCourseController.php';
$content = file_get_contents($file);

$newMethods = <<<PHP
    /**
     * Export courses as CSV (Excel-compatible) or PDF.
     * Pure PHP — no ZipArchive needed.
     */
    public function export(Request $request)
    {
        \$format = \$request->query('format', 'csv');
        \$query = Course::with('department')->latest();

        // Apply filters
        if (\$request->filled('department') && \$request->department !== 'all') {
            \$query->whereHas('department', function (\$q) use (\$request) {
                \$q->where('name', \$request->department);
            });
        }
        if (\$request->filled('status') && \$request->status !== 'all') {
            \$query->where('status', \$request->status);
        }
        if (\$request->filled('level') && \$request->level !== 'all') {
            \$query->where('level', \$request->level);
        }
        if (\$request->filled('semester') && \$request->semester !== 'all') {
            \$query->where('semester', \$request->semester);
        }
        if (\$request->filled('search')) {
            \$search = \$request->search;
            \$query->where(function (\$q) use (\$search) {
                \$q->where('title', 'LIKE', "%\$search%")
                  ->orWhere('code', 'LIKE', "%\$search%");
            });
        }

        \$courses = \$query->get();
        \$fileName = 'courses_export_' . date('Y-m-d');

        if (\$format === 'pdf') {
            return \$this->exportCoursesPdf(\$courses, \$fileName);
        }

        return \$this->exportCoursesCsv(\$courses, \$fileName);
    }

    private function exportCoursesCsv(\$courses, string \$fileName)
    {
        ob_start();
        \$handle = fopen('php://output', 'w');
        fputs(\$handle, "\\xEF\\xBB\\xBF"); // UTF-8 BOM for Excel
        fputcsv(\$handle, [
            'ID', 'Course Name', 'Course Code', 'Description',
            'Department', 'Credits', 'Level', 'Semester',
            'Start Date', 'End Date', 'Status', 'Created Date'
        ]);
        foreach (\$courses as \$course) {
            fputcsv(\$handle, [
                \$course->id,
                \$course->title ?? \$course->name ?? '',
                \$course->code ?? '',
                \$course->description ?? '',
                \$course->department ? \$course->department->name : '',
                \$course->credits ?? '',
                \$course->level ?? '',
                \$course->semester ?? '',
                \$course->start_date ?? '',
                \$course->end_date ?? '',
                \$course->status ?? 'active',
                \$course->created_at ? \$course->created_at->format('Y-m-d') : '',
            ]);
        }
        fclose(\$handle);
        \$csvContent = ob_get_clean();

        return response()->json([
            'file' => base64_encode(\$csvContent),
            'filename' => \$fileName . '.csv'
        ]);
    }

    private function exportCoursesPdf(\$courses, string \$fileName)
    {
        \$generatedAt = now()->format('F j, Y  H:i');

        \$html = '<html><head><style>
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
        \$html .= '<h2>Course Export</h2>';
        \$html .= '<div class="meta">Generated: ' . \$generatedAt . ' &nbsp;|&nbsp; Total records: ' . \$courses->count() . '</div>';
        \$html .= '<table><thead><tr>
            <th>#</th><th>Course Name</th><th>Code</th><th>Department</th>
            <th>Credits</th><th>Level</th><th>Semester</th>
            <th>Start</th><th>End</th><th>Status</th>
        </tr></thead><tbody>';
        foreach (\$courses as \$i => \$course) {
            \$statusClass = (\$course->status === 'active') ? 'badge-active' : 'badge-inactive';
            \$html .= '<tr>
                <td>' . (\$i + 1) . '</td>
                <td>' . htmlspecialchars(\$course->title ?? \$course->name ?? '') . '</td>
                <td>' . htmlspecialchars(\$course->code ?? '') . '</td>
                <td>' . htmlspecialchars(\$course->department ? \$course->department->name : '') . '</td>
                <td>' . htmlspecialchars(\$course->credits ?? '') . '</td>
                <td>' . htmlspecialchars(\$course->level ?? '') . '</td>
                <td>' . htmlspecialchars(\$course->semester ?? '') . '</td>
                <td>' . htmlspecialchars(\$course->start_date ?? '') . '</td>
                <td>' . htmlspecialchars(\$course->end_date ?? '') . '</td>
                <td class="' . \$statusClass . '">' . ucfirst(\$course->status ?? 'active') . '</td>
            </tr>';
        }
        \$html .= '</tbody></table></body></html>';

        \$options = new \Dompdf\Options();
        \$options->set('isHtml5ParserEnabled', true);
        \$options->set('isPhpEnabled', false);
        \$options->set('defaultFont', 'DejaVu Sans');

        \$dompdf = new \Dompdf\Dompdf(\$options);
        \$dompdf->loadHtml(\$html);
        \$dompdf->setPaper('A4', 'landscape');
        \$dompdf->render();

        \$pdfContent = \$dompdf->output();

        return response()->json([
            'file' => base64_encode(\$pdfContent),
            'filename' => \$fileName . '.pdf'
        ]);
    }
PHP;

$pattern = '/\/\*\*\s*\*\s*Export courses as CSV.*?public function export.*?return response\(\$pdfContent, 200, \[.*?\]\);\s*\}/s';
$newContent = preg_replace($pattern, $newMethods, $content);
file_put_contents($file, $newContent);
echo "Controller updated successfully.";
