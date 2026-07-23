<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class CourseImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    protected $departments;

    public function __construct()
    {
        $this->departments = Department::all()->pluck('id', 'name')->mapWithKeys(function($id, $name) {
            return [strtolower(trim($name)) => $id];
        });
    }

    public function model(array $row)
    {
        if (empty($row['course_code'])) {
            return null;
        }

        // Skip existing course codes
        if (Course::where('code', $row['course_code'])->exists()) {
            return null;
        }

        $deptId = null;
        if (!empty($row['department'])) {
            $deptName = strtolower(trim($row['department']));
            $deptId = $this->departments[$deptName] ?? null;
        }

        return new Course([
            'name'         => $row['course_name'] ?? $row['name'] ?? null,
            'code'         => $row['course_code'],
            'description'  => $row['description'] ?? null,
            'department_id'=> $deptId,
            'credit_hours' => $row['credit_hours'] ?? 3,
            'status'       => $row['status'] ?? 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'course_code' => 'required',
        ];
    }
}
