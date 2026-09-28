<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\SemesterSubmission;

class InstructorSemesterSubmissionController extends Controller
{
    /**
     * Get the status of the semester submission for the authenticated instructor.
     */
    public function status(Request $request): JsonResponse
    {
        $instructor = $request->user()->load('department');
        
        // 1. Get Academic Info
        $academicYear = $instructor->academic_year ?? '2025/2026';
        $semester = $instructor->semester ?? 'Second Semester';
        $department = $instructor->department?->name ?? 'N/A';
        $section = $instructor->section ?? 'N/A';

        // 2. Compute Checklist Statuses
        
        // Academic Schedule Check (from Admin Academic Calendar "Class Start" event)
        // Fetch the most recently created "Class Start" event to avoid mismatches 
        // with instructor's semester/academic_year field
        $classStartEvent = \App\Models\AcademicEvent::where('title', 'Class Start')
            ->orderBy('created_at', 'desc')
            ->first();
            
        $academicScheduleCompleted = false;
        $academicScheduleDate = '-';
        if ($classStartEvent && $classStartEvent->end_date) {
            $endDate = \Carbon\Carbon::parse($classStartEvent->end_date);
            // Completed if current date is >= end_date
            $academicScheduleCompleted = now()->startOfDay()->gte($endDate->startOfDay());
            $academicScheduleDate = $endDate->format('M d, Y');
        }

        // Check if instructor has created at least one exam
        $exams = Exam::where('user_id', $instructor->id)->get();
        $hasExams = $exams->count() > 0;
        
        // Check if exams have been scheduled
        $hasScheduledExams = $exams->whereNotNull('scheduled_at')->count() > 0;
        
        // Count students
        $query = User::where('role', 'student');
        if ($instructor->department_id) {
            $query->where('department_id', $instructor->department_id);
        }
        if ($instructor->year_level) {
            $query->where('year_level', $instructor->year_level);
        }
        if ($instructor->section) {
            $raw   = $instructor->section;
            $clean = trim(preg_replace('/^[Ss]ection\s+/i', '', $raw));
            $query->where(function ($q) use ($raw, $clean) {
                $q->where('section', $raw)
                  ->orWhere('section', $clean)
                  ->orWhere('section', 'Section ' . $clean);
            });
        }
        $studentsCount = $query->count();
        if ($studentsCount === 0 && $instructor->department_id && $instructor->year_level) {
            $studentsCount = User::where('role', 'student')
                ->where('department_id', $instructor->department_id)
                ->where('year_level', $instructor->year_level)
                ->count();
        }
        if ($studentsCount === 0 && $instructor->department_id) {
            $studentsCount = User::where('role', 'student')
                ->where('department_id', $instructor->department_id)
                ->count();
        }

        $hasStudents = $studentsCount > 0;

        // Exams Check (all created exams must be finished)
        $examsCompleted = false;
        $examsDate = '-';
        $examsTime = '-';
        if ($exams->isNotEmpty()) {
            $allFinished = true;
            $latestEnd = null;
            foreach ($exams as $exam) {
                if (!$exam->scheduled_at) {
                    $allFinished = false; // Draft exam
                    break;
                }
                $endTime = \Carbon\Carbon::parse($exam->scheduled_at)->addMinutes($exam->duration_minutes ?? 0);
                if (now()->lt($endTime) && $exam->status !== 'completed') {
                    $allFinished = false; // Not finished yet
                    break;
                }
                if (!$latestEnd || $endTime->gt($latestEnd)) {
                    $latestEnd = $endTime;
                }
            }
            $examsCompleted = $allFinished;
            if ($latestEnd) {
                $examsDate = $latestEnd->format('M d, Y');
                $examsTime = $latestEnd->format('h:i A');
            }
        }

        // Results Check (all students must be graded AND result published for all exams)
        $resultsCompleted = false;
        $resultsDate = '-';
        $resultsTime = '-';
        if ($exams->isNotEmpty() && $studentsCount > 0) {
            $allResultsPublished = true;
            foreach ($exams as $exam) {
                $submittedCount = ExamAttempt::where('exam_id', $exam->id)
                    ->where(function($q) {
                        $q->whereNotNull('submitted_at')->orWhere('status', '!=', 'in_progress');
                    })
                    ->count();
                
                $publishedCount = ExamAttempt::where('exam_id', $exam->id)
                    ->where('status', 'published')
                    ->count();
                
                // If there are submitted attempts but they are not all published
                if ($submittedCount > 0 && $publishedCount < $submittedCount) {
                    $allResultsPublished = false;
                    break;
                }
                
                // If nobody has submitted yet, it's only complete if the exam window has fully passed
                if ($submittedCount === 0) {
                    $examEnd = $exam->scheduled_at ? $exam->scheduled_at->copy()->addMinutes((int)($exam->duration_minutes ?? 0)) : null;
                    if (!$examEnd || now()->lt($examEnd)) {
                        $allResultsPublished = false;
                        break;
                    }
                }
            }
            $resultsCompleted = $allResultsPublished;
            
            if ($resultsCompleted) {
                $latestAttempt = ExamAttempt::whereIn('exam_id', $exams->pluck('id'))
                    ->where('status', 'published')
                    ->latest('updated_at')
                    ->first();
                if ($latestAttempt) {
                    $resultsDate = $latestAttempt->updated_at->format('M d, Y');
                    $resultsTime = $latestAttempt->updated_at->format('h:i A');
                }
            }
        }

        $checklist = [
            'academic_schedule' => [
                'completed' => $academicScheduleCompleted, 
                'date' => $academicScheduleDate, 
                'time' => $classStartEvent && $classStartEvent->end_time ? \Carbon\Carbon::parse($classStartEvent->end_time)->format('h:i A') : '11:59 PM'
            ],
            'exams' => [
                'completed' => $examsCompleted, 
                'date' => $examsDate, 
                'time' => $examsTime
            ],
            'student_info' => [
                'completed' => $hasStudents, 
                'date' => now()->format('M d, Y'), 
                'time' => now()->format('h:i A')
            ],
            'results' => [
                'completed' => $resultsCompleted, 
                'date' => $resultsDate, 
                'time' => $resultsTime
            ],
        ];
        
        $allChecklistComplete = collect($checklist)->every(fn($item) => $item['completed']);

        // 3. Compute Summary Stats
        $coursesAssigned = 1; // Assuming 1 for now based on $instructor->course_name or relation
        if ($instructor->course_code) {
            $coursesAssigned = 1;
        }

        $resultsSubmitted = ExamAttempt::whereIn('exam_id', $exams->pluck('id'))->where('status', 'graded')->distinct('user_id')->count('user_id');

        // 4. Fetch Submission Status
        $submission = SemesterSubmission::firstOrCreate([
            'instructor_id' => $instructor->id,
            'academic_year' => $academicYear,
            'semester' => $semester,
        ], [
            'department' => $department,
            'section' => $section,
            'status' => 'pending',
        ]);

        return response()->json([
            'data' => [
                'academic_info' => [
                    'academic_year' => $academicYear,
                    'semester' => $semester,
                    'department' => $department,
                    'section' => $section,
                ],
                'checklist' => $checklist,
                'is_all_completed' => $allChecklistComplete,
                'summary_stats' => [
                    'total_students' => $studentsCount,
                    'courses' => $coursesAssigned,
                    'exams_conducted' => $exams->count(),
                    'results_submitted' => $resultsSubmitted,
                ],
                'submission' => [
                    'status' => $submission->status, // pending, submitted, approved, rejected
                    'submitted_at' => $submission->submitted_at?->format('M d, Y • h:i A'),
                    'approved_at' => $submission->approved_at?->format('M d, Y • h:i A'),
                    'remarks' => $submission->remarks,
                ]
            ]
        ]);
    }

    /**
     * Submit the semester records.
     */
    public function submit(Request $request): JsonResponse
    {
        $instructor = $request->user();
        
        $academicYear = $instructor->academic_year ?? '2025/2026';
        $semester = $instructor->semester ?? 'Second Semester';

        $submission = SemesterSubmission::where([
            'instructor_id' => $instructor->id,
            'academic_year' => $academicYear,
            'semester' => $semester,
        ])->first();

        if (!$submission) {
            return response()->json(['message' => 'Submission record not found.'], 404);
        }

        if (in_array($submission->status, ['submitted', 'approved'])) {
            return response()->json(['message' => 'Records already submitted.'], 400);
        }

        $submission->status = 'submitted';
        $submission->submitted_at = now();
        $submission->save();

        return response()->json([
            'message' => 'Semester records submitted successfully.',
            'data' => [
                'status' => 'submitted',
                'submitted_at' => $submission->submitted_at->format('M d, Y • h:i A')
            ]
        ]);
    }
}
