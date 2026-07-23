<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class UserImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    protected $role;
    protected $departments;

    public function __construct($role)
    {
        $this->role = $role;
        // Cache departments for fast lookup by name
        $this->departments = Department::all()->pluck('id', 'name')->mapWithKeys(function($id, $name) {
            return [strtolower(trim($name)) => $id];
        });
    }

    public function model(array $row)
    {
        // Skip duplicate emails or id_no if they exist
        if (User::where('email', $row['email'])->orWhere('id_no', $row['student_id_employee_id'] ?? $row['id_no'] ?? null)->exists()) {
            return null;
        }

        // Determine department ID from name
        $deptId = null;
        if (!empty($row['department'])) {
            $deptName = strtolower(trim($row['department']));
            $deptId = $this->departments[$deptName] ?? null;
        }

        $password = !empty($row['password']) && $row['password'] !== '********' 
            ? Hash::make($row['password']) 
            : Hash::make('Password123!');

        return new User([
            'name'          => $row['full_name'] ?? $row['name'] ?? null,
            'email'         => $row['email'],
            'username'      => $row['username'] ?? null,
            'password'      => $password,
            'role'          => $this->role,
            'id_no'         => $row['student_id_employee_id'] ?? $row['id_no'] ?? null,
            'department_id' => $deptId,
            'academic_year' => $row['academic_year'] ?? null,
            'year_level'    => $row['year_level'] ?? null,
            'semester'      => $row['semester'] ?? null,
            'section'       => $row['section'] ?? null,
            'phone'         => $row['phone'] ?? null,
            'gender'        => $row['gender'] ?? null,
            'status'        => $row['status'] ?? 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
