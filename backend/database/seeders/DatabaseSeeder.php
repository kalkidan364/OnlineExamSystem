<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Business Rule: Each instructor teaches exactly ONE course.
     * All exams created by the instructor belong to that one course.
     */
    public function run(): void
    {
        // ── 1. Instructor Account (ONE course only) ──────────────────────────
        $instructor = User::updateOrCreate(
            ['email' => 'instructor@wollo.edu.et'],
            [
                'name'        => 'Dr. Abebe Kebede',
                'password'    => Hash::make('password123'),
                'role'        => 'instructor',
                'course_code' => 'SWE-301',            // ← single assigned course
                'course_name' => 'Software Engineering',
            ]
        );

        // ── 2. Student Accounts ──────────────────────────────────────────────
        $studentData = [
            ['name' => 'Kalkidan Hailu',   'email' => 'kalkidan@student.wollo.edu.et'],
            ['name' => 'Temesgen Alemu',   'email' => 'temesgen@student.wollo.edu.et'],
            ['name' => 'Yohannes Girma',   'email' => 'yohannes@student.wollo.edu.et'],
            ['name' => 'Selamawit Bekele', 'email' => 'selamawit@student.wollo.edu.et'],
            ['name' => 'Mihret Tadesse',   'email' => 'mihret@student.wollo.edu.et'],
        ];

        $students = [];
        foreach ($studentData as $data) {
            $students[] = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password123'),
                    'role'     => 'student',
                ]
            );
        }

        // ── 3. Question Bank for SWE-301 ─────────────────────────────────────
        $bank = QuestionBank::updateOrCreate(
            ['user_id' => $instructor->id, 'course_code' => 'SWE-301'],
            [
                'title'           => 'Software Engineering Core Concepts',
                'description'     => 'Questions covering design patterns, testing, and architecture.',
                'questions_count' => 5,
            ]
        );

        // ── 4. Exams — ALL belong to SWE-301 ────────────────────────────────
        $examDefinitions = [
            [
                'title'        => 'Chapter 1–3 Quiz — Software Engineering',
                'status'       => 'completed',
                'duration'     => 60,
                'marks'        => 50,
                'scheduled_at' => Carbon::now()->subDays(20),
                'published_at' => Carbon::now()->subDays(25),
            ],
            [
                'title'        => 'Midterm Examination — Software Engineering',
                'status'       => 'completed',
                'duration'     => 120,
                'marks'        => 100,
                'scheduled_at' => Carbon::now()->subDays(10),
                'published_at' => Carbon::now()->subDays(15),
            ],
            [
                'title'        => 'Design Patterns Assignment Test — Software Engineering',
                'status'       => 'scheduled',
                'duration'     => 90,
                'marks'        => 75,
                'scheduled_at' => Carbon::now()->addDays(3),
                'published_at' => Carbon::now()->subDay(),
            ],
            [
                'title'        => 'Final Examination — Software Engineering',
                'status'       => 'scheduled',
                'duration'     => 180,
                'marks'        => 100,
                'scheduled_at' => Carbon::now()->addDays(14),
                'published_at' => Carbon::now(),
            ],
            [
                'title'        => 'Makeup Exam Draft — Software Engineering',
                'status'       => 'draft',
                'duration'     => 90,
                'marks'        => 100,
                'scheduled_at' => null,
                'published_at' => null,
            ],
        ];

        $createdExams = [];
        foreach ($examDefinitions as $def) {
            $exam = Exam::updateOrCreate(
                ['user_id' => $instructor->id, 'title' => $def['title']],
                [
                    'user_id'          => $instructor->id,
                    'course_code'      => $instructor->course_code,  // always SWE-301
                    'course_name'      => $instructor->course_name,  // always Software Engineering
                    'duration_minutes' => $def['duration'],
                    'total_marks'      => $def['marks'],
                    'status'           => $def['status'],
                    'scheduled_at'     => $def['scheduled_at'],
                    'published_at'     => $def['published_at'],
                ]
            );

            // Enroll all students in each exam
            $exam->students()->syncWithoutDetaching(collect($students)->pluck('id'));
            $createdExams[] = $exam;
        }

        // ── 5. Sample Questions ───────────────────────────────────────────────
        $questions = [
            'What is the primary purpose of the Repository Pattern?',
            'Which principle does SRP stand for in SOLID?',
            'What does TDD stand for in Software Engineering?',
            'Which diagram shows the sequence of object interactions?',
            'What is the difference between a class and an object?',
        ];

        foreach ($questions as $text) {
            Question::firstOrCreate(
                ['question_bank_id' => $bank->id, 'text' => $text],
                [
                    'type'           => 'multiple_choice',
                    'options'        => ['A', 'B', 'C', 'D'],
                    'correct_answer' => 'A',
                    'marks'          => 10,
                    'exam_id'        => $createdExams[0]->id,
                ]
            );
        }

        // ── 6. Graded Attempts for the completed exams ───────────────────────
        $completedExams = array_filter($createdExams, fn($e) => $e->status === 'completed');
        $scores = [95, 88, 82, 91, 78];

        foreach ($completedExams as $exam) {
            foreach ($students as $i => $student) {
                $pct = $scores[$i];
                $grade = match(true) {
                    $pct >= 95 => 'A+',
                    $pct >= 90 => 'A',
                    $pct >= 85 => 'A-',
                    $pct >= 80 => 'B+',
                    default    => 'B',
                };

                ExamAttempt::updateOrCreate(
                    ['exam_id' => $exam->id, 'user_id' => $student->id],
                    [
                        'score'        => (int)($pct * $exam->total_marks / 100),
                        'total_marks'  => $exam->total_marks,
                        'percentage'   => $pct,
                        'grade'        => $grade,
                        'status'       => 'graded',
                        'answers'      => ['q1' => 'A', 'q2' => 'B', 'q3' => 'A'],
                        'started_at'   => $exam->scheduled_at,
                        'submitted_at' => $exam->scheduled_at->copy()->addMinutes($exam->duration_minutes - 5),
                    ]
                );
            }
        }

        $this->command->info('');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('────────────────────────────────────────────');
        $this->command->info('  Instructor : instructor@wollo.edu.et');
        $this->command->info('  Course     : SWE-301 — Software Engineering');
        $this->command->info('  Password   : password123');
        $this->command->info('────────────────────────────────────────────');
    }
}
