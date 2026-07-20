<?php
$instructors = \App\Models\User::where('role', 'instructor')->get();
foreach ($instructors as $instructor) {
    \App\Models\QuestionBank::where('user_id', $instructor->id)->delete();
    $course_code = $instructor->course_code ?: 'SWE-301';
    \App\Models\QuestionBank::insert([
        ['user_id' => $instructor->id, 'title' => 'Database Systems Mid Questions', 'description' => 'Questions prepared for Semester I Mid Examination.', 'course_code' => $course_code, 'questions_count' => 50, 'created_at' => '2026-05-24 10:00:00', 'updated_at' => '2026-05-24 10:00:00'],
        ['user_id' => $instructor->id, 'title' => 'Operating Systems Final Questions', 'description' => 'Final examination question collection.', 'course_code' => $course_code, 'questions_count' => 60, 'created_at' => '2026-05-28 10:00:00', 'updated_at' => '2026-05-28 10:00:00'],
        ['user_id' => $instructor->id, 'title' => 'Software Engineering Questions', 'description' => 'All important questions for Software Engineering course.', 'course_code' => $course_code, 'questions_count' => 45, 'created_at' => '2026-05-26 10:00:00', 'updated_at' => '2026-05-26 10:00:00'],
        ['user_id' => $instructor->id, 'title' => 'Data Structures Quiz Questions', 'description' => 'Quiz and short questions for practice.', 'course_code' => $course_code, 'questions_count' => 30, 'created_at' => '2026-05-20 10:00:00', 'updated_at' => '2026-05-20 10:00:00'],
        ['user_id' => $instructor->id, 'title' => 'Computer Networks MCQ Bank', 'description' => 'Multiple choice questions for exams.', 'course_code' => $course_code, 'questions_count' => 100, 'created_at' => '2026-05-18 10:00:00', 'updated_at' => '2026-05-18 10:00:00']
    ]);
}
echo 'Seeded all instructors successfully.';

