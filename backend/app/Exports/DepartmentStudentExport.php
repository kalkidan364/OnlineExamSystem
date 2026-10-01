<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class DepartmentStudentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected Collection $students;
    protected int $index = 0;

    public function __construct(Collection $students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            '#',
            'Full Name',
            'Student ID',
            'Email',
            'Section',
            'Year Level',
            'Department',
            'Phone',
            'Gender',
            'Status',
            'Admission Date',
        ];
    }

    public function map($student): array
    {
        $this->index++;

        return [
            $this->index,
            $student->name ?? '',
            $student->id_no ?? (string)$student->id,
            $student->email ?? '',
            $student->section ?? '—',
            $student->year_level ?? '—',
            $student->department ? $student->department->name : '',
            $student->phone ?? '—',
            $student->gender ?? '—',
            ucfirst($student->status ?? 'active'),
            $student->created_at ? $student->created_at->format('M d, Y') : '',
        ];
    }
}
