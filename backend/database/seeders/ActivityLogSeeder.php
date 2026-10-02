<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $csDept = Department::where('code', 'CS')->orWhere('name', 'like', '%computer%')->first();
        $deptId = $csDept ? $csDept->id : 14;

        // Ensure users exist
        $deptHead = User::where('role', 'dept_head')->first();
        if ($deptHead && !$deptHead->department_id) {
            $deptHead->update(['department_id' => $deptId]);
        }

        $admin = User::where('role', 'admin')->first();

        // Additional instructors / students for CS
        $instructorsData = [
            ['name' => 'Dr. Abebe Kebede', 'email' => 'abebe.kebede@wollo.edu.et', 'role' => 'instructor'],
            ['name' => 'Selamawit Getachew', 'email' => 'selamawit.g@wollo.edu.et', 'role' => 'instructor'],
            ['name' => 'Yonas Alemu', 'email' => 'yonas.a@wollo.edu.et', 'role' => 'instructor'],
            ['name' => 'Hanna Mengesha', 'email' => 'hanna.m@wollo.edu.et', 'role' => 'instructor'],
        ];

        $createdInstructors = [];
        foreach ($instructorsData as $data) {
            $createdInstructors[] = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'          => $data['name'],
                    'password'      => Hash::make('password123'),
                    'role'          => 'instructor',
                    'department_id' => $deptId,
                    'status'        => 'active',
                ]
            );
        }

        $studentsData = [
            ['name' => 'Medhanit Alemu', 'email' => 'medhanit.a@student.wollo.edu.et', 'id_no' => 'WUR/1001/14'],
            ['name' => 'Daniel Kassa', 'email' => 'daniel.k@student.wollo.edu.et', 'id_no' => 'WUR/1002/14'],
            ['name' => 'Rahab Solomon', 'email' => 'rahab.s@student.wollo.edu.et', 'id_no' => 'WUR/1003/14'],
            ['name' => 'Kalkidan Hailu', 'email' => 'kalkidan.h@student.wollo.edu.et', 'id_no' => 'WUR/1004/14'],
            ['name' => 'Temesgen Alemu', 'email' => 'temesgen.a@student.wollo.edu.et', 'id_no' => 'WUR/1005/14'],
        ];

        $createdStudents = [];
        foreach ($studentsData as $s) {
            $createdStudents[] = User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'name'          => $s['name'],
                    'password'      => Hash::make('password123'),
                    'role'          => 'student',
                    'department_id' => $deptId,
                    'id_no'         => $s['id_no'],
                    'status'        => 'active',
                ]
            );
        }

        // Define realistic log entries
        $logs = [
            [
                'user'        => $deptHead,
                'role'        => 'dept_head',
                'action'      => 'User logged in to the system',
                'type'        => 'Login',
                'module'      => 'Authentication',
                'details'     => 'Department Head successfully logged in to department dashboard',
                'ip'          => '192.168.1.15',
                'status'      => 'Success',
                'hours_ago'   => 1,
            ],
            [
                'user'        => $createdInstructors[1], // Selamawit
                'role'        => 'instructor',
                'action'      => 'Created a new exam "Database Midterm"',
                'type'        => 'Created',
                'module'      => 'Exams',
                'details'     => 'Created a new exam "Database Systems Midterm Examination" (CS-302)',
                'ip'          => '192.168.1.23',
                'status'      => 'Success',
                'hours_ago'   => 3,
            ],
            [
                'user'        => $createdInstructors[0], // Dr. Abebe
                'role'        => 'instructor',
                'action'      => 'Updated question in Question Bank',
                'type'        => 'Updated',
                'module'      => 'Questions',
                'details'     => 'Updated 5 questions in "Data Structures Question Bank"',
                'ip'          => '192.168.1.23',
                'status'      => 'Success',
                'hours_ago'   => 5,
            ],
            [
                'user'        => $createdStudents[0], // Medhanit
                'role'        => 'student',
                'action'      => 'Submitted exam "Database Quiz 2"',
                'type'        => 'Submitted',
                'module'      => 'Exams',
                'details'     => 'Submitted exam attempt for "Database Quiz 2" with score 88%',
                'ip'          => '192.168.1.45',
                'status'      => 'Success',
                'hours_ago'   => 8,
            ],
            [
                'user'        => $admin,
                'role'        => 'admin',
                'action'      => 'Created new instructor account',
                'type'        => 'Created',
                'module'      => 'Users',
                'details'     => 'Created new instructor account for Computer Science department',
                'ip'          => '192.168.1.10',
                'status'      => 'Success',
                'hours_ago'   => 12,
            ],
            [
                'user'        => $createdInstructors[2], // Yonas
                'role'        => 'instructor',
                'action'      => 'Deleted exam "Old Practice Test"',
                'type'        => 'Deleted',
                'module'      => 'Exams',
                'details'     => 'Deleted archived exam draft "Old Practice Test 2024"',
                'ip'          => '192.168.1.23',
                'status'      => 'Success',
                'hours_ago'   => 16,
            ],
            [
                'user'        => $createdStudents[2], // Rahab
                'role'        => 'student',
                'action'      => 'Failed login attempt',
                'type'        => 'Login Failed',
                'module'      => 'Authentication',
                'details'     => 'Failed login attempt: Invalid credentials provided for rahab.s@student.wollo.edu.et',
                'ip'          => '192.168.1.45',
                'status'      => 'Failed',
                'hours_ago'   => 20,
            ],
            [
                'user'        => null,
                'role'        => 'system',
                'action'      => 'System backup completed',
                'type'        => 'Backup',
                'module'      => 'System',
                'details'     => 'Automated daily database snapshot backup completed successfully',
                'ip'          => '127.0.0.1',
                'status'      => 'Success',
                'hours_ago'   => 24,
            ],
            [
                'user'        => $createdInstructors[3], // Hanna
                'role'        => 'instructor',
                'action'      => 'Exported exam results report',
                'type'        => 'Exported',
                'module'      => 'Reports',
                'details'     => 'Exported exam results report as PDF for CS-201',
                'ip'          => '192.168.1.23',
                'status'      => 'Success',
                'hours_ago'   => 28,
            ],
            [
                'user'        => null,
                'role'        => 'system',
                'action'      => 'System auto cleanup completed',
                'type'        => 'System Event',
                'module'      => 'System',
                'details'     => 'System auto cache cleanup and session purge executed',
                'ip'          => '127.0.0.1',
                'status'      => 'Success',
                'hours_ago'   => 32,
            ],
            [
                'user'        => $deptHead,
                'role'        => 'dept_head',
                'action'      => 'Approved semester exam submission',
                'type'        => 'Updated',
                'module'      => 'Semester Submissions',
                'details'     => 'Approved semester final exam submission submitted by Dr. Abebe Kebede',
                'ip'          => '192.168.1.15',
                'status'      => 'Success',
                'hours_ago'   => 36,
            ],
            [
                'user'        => $deptHead,
                'role'        => 'dept_head',
                'action'      => 'Created new course "Advanced Algorithms"',
                'type'        => 'Created',
                'module'      => 'Courses',
                'details'     => 'Added new course CS-401 "Advanced Algorithms" to curriculum',
                'ip'          => '192.168.1.15',
                'status'      => 'Success',
                'hours_ago'   => 42,
            ],
            [
                'user'        => $createdStudents[1], // Daniel
                'role'        => 'student',
                'action'      => 'User logged in to student portal',
                'type'        => 'Login',
                'module'      => 'Authentication',
                'details'     => 'Student Daniel Kassa successfully logged in',
                'ip'          => '192.168.1.78',
                'status'      => 'Success',
                'hours_ago'   => 48,
            ],
            [
                'user'        => $createdStudents[3], // Kalkidan H
                'role'        => 'student',
                'action'      => 'Submitted exam "Algorithms Quiz 1"',
                'type'        => 'Submitted',
                'module'      => 'Exams',
                'details'     => 'Submitted exam "Algorithms Quiz 1" with score 94%',
                'ip'          => '192.168.1.88',
                'status'      => 'Success',
                'hours_ago'   => 54,
            ],
            [
                'user'        => $deptHead,
                'role'        => 'dept_head',
                'action'      => 'Exported department analytics report',
                'type'        => 'Exported',
                'module'      => 'Reports',
                'details'     => 'Exported Computer Science Department Analytics Report as PDF',
                'ip'          => '192.168.1.15',
                'status'      => 'Success',
                'hours_ago'   => 60,
            ],
            [
                'user'        => $createdInstructors[0], // Abebe
                'role'        => 'instructor',
                'action'      => 'Submitted semester exam draft',
                'type'        => 'Submitted',
                'module'      => 'Semester Submissions',
                'details'     => 'Submitted semester exam draft for Course SWE-301 to Department Head',
                'ip'          => '192.168.1.23',
                'status'      => 'Success',
                'hours_ago'   => 72,
            ],
            [
                'user'        => $createdStudents[4], // Temesgen
                'role'        => 'student',
                'action'      => 'Failed exam attempt submission',
                'type'        => 'Submitted',
                'module'      => 'Exams',
                'details'     => 'Failed exam attempt submission: Network timeout during final answer upload',
                'ip'          => '192.168.1.92',
                'status'      => 'Failed',
                'hours_ago'   => 84,
            ],
            [
                'user'        => null,
                'role'        => 'system',
                'action'      => 'Scheduled academic calendar reminder',
                'type'        => 'System Event',
                'module'      => 'System',
                'details'     => 'Broadcast upcoming Midterm Examination event notification to all students',
                'ip'          => '127.0.0.1',
                'status'      => 'Success',
                'hours_ago'   => 96,
            ],
            [
                'user'        => $deptHead,
                'role'        => 'dept_head',
                'action'      => 'Assigned instructor to course',
                'type'        => 'Updated',
                'module'      => 'Courses',
                'details'     => 'Assigned instructor Dr. Abebe Kebede to course "Operating Systems"',
                'ip'          => '192.168.1.15',
                'status'      => 'Success',
                'hours_ago'   => 110,
            ],
            [
                'user'        => $createdInstructors[1], // Selamawit
                'role'        => 'instructor',
                'action'      => 'User logged in',
                'type'        => 'Login',
                'module'      => 'Authentication',
                'details'     => 'Instructor Selamawit Getachew logged in successfully',
                'ip'          => '192.168.1.23',
                'status'      => 'Success',
                'hours_ago'   => 120,
            ],
        ];

        // Clear existing to prevent duplicate test runs
        ActivityLog::truncate();

        foreach ($logs as $log) {
            $timestamp = Carbon::now()->subHours($log['hours_ago']);
            ActivityLog::create([
                'user_id'       => $log['user'] ? $log['user']->id : null,
                'department_id' => $deptId,
                'actor_role'    => $log['role'],
                'action'        => $log['action'],
                'type'          => $log['type'],
                'module'        => $log['module'],
                'details'       => $log['details'],
                'ip_address'    => $log['ip'],
                'log_status'    => $log['status'],
                'is_read'       => false,
                'created_at'    => $timestamp,
                'updated_at'    => $timestamp,
            ]);
        }
    }
}
