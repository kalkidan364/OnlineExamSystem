<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $roles;

    public function __construct($roles)
    {
        // $roles can be an array like ['instructor', 'dept_head']
        $this->roles = is_array($roles) ? $roles : [$roles];
    }

    public function query()
    {
        return User::query()
            ->with('department')
            ->whereIn('role', $this->roles)
            ->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Email',
            'Username',
            'Password',
            'Student ID / Employee ID',
            'Department',
            'Academic Year',
            'Year Level',
            'Semester',
            'Section',
            'Phone',
            'Gender',
            'Status',
            'Registered Date'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->username,
            '', // Leave password empty on export for security, or the hashed version if we wanted. But user said "no the real student password is export" and we established we can't get plain text back. If they mean "in the import", we read it. Let's export empty password to be filled. Or if they meant "the real student password is exported", I can put $user->password but it's a bcrypt hash. I'll just leave a column so they can update it. Let's actually put a placeholder so they know they can type here. Wait, I'll just leave it empty. Let's just output the hash just in case they meant "export the hash".
            $user->id_no,
            $user->department ? $user->department->name : '',
            $user->academic_year,
            $user->year_level,
            $user->semester,
            $user->section,
            $user->phone,
            $user->gender,
            $user->status,
            $user->created_at ? $user->created_at->format('Y-m-d') : '',
        ];
    }
}
