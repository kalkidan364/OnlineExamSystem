<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class DepartmentInstructorExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected Collection $instructors;
    protected int $index = 0;

    public function __construct(Collection $instructors)
    {
        $this->instructors = $instructors;
    }

    public function collection()
    {
        return $this->instructors;
    }

    public function headings(): array
    {
        return [
            '#',
            'Full Name',
            'Employee ID',
            'Email Address',
            'Phone',
            'Gender',
            'Department',
            'Assigned Courses',
            'Total Credits',
            'Employment Type',
            'Status',
            'Academic Year',
            'Section',
            'Created By',
            'Joined Date',
        ];
    }

    public function map($instructor): array
    {
        $this->index++;

        $courses = $instructor->assignedCourses?->pluck('title')->filter()->join(', ');
        if (empty($courses)) {
            $courses = 'No Courses';
        }
        $credits = $instructor->assignedCourses?->sum('credits') ?? 0;

        $employment = ucfirst(str_replace('_', ' ', $instructor->employment_type ?? 'full_time'));
        $creatorName = $instructor->creator ? $instructor->creator->name : 'Super Admin';

        return [
            $this->index,
            $instructor->name ?? '',
            $instructor->id_no ?? (string)$instructor->id,
            $instructor->email ?? '',
            $instructor->phone ?? '—',
            $instructor->gender ?? '—',
            $instructor->department ? $instructor->department->name : '',
            $courses,
            $credits,
            $employment,
            ucfirst($instructor->status ?? 'active'),
            $instructor->year_level ?? '—',
            $instructor->section ?? '—',
            $creatorName,
            $instructor->created_at ? $instructor->created_at->format('M d, Y') : '',
        ];
    }
}
