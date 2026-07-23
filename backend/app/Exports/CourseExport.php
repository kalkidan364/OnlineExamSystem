<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CourseExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Course::query()
            ->with('department')
            ->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Course Name',
            'Course Code',
            'Description',
            'Department',
            'Credit Hours',
            'Status',
            'Created Date'
        ];
    }

    public function map($course): array
    {
        return [
            $course->id,
            $course->name,
            $course->code,
            $course->description,
            $course->department ? $course->department->name : '',
            $course->credit_hours,
            $course->status,
            $course->created_at ? $course->created_at->format('Y-m-d') : '',
        ];
    }
}
