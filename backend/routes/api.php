<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\InstructorDashboardController;
use App\Http\Controllers\Api\V1\InstructorExamController;
use App\Http\Controllers\Api\V1\InstructorQuestionBankController;
use App\Http\Controllers\Api\V1\InstructorStudentController;
use App\Http\Controllers\Api\V1\InstructorReportController;
use App\Http\Controllers\Api\V1\StudentExamController;

/*
|--------------------------------------------------------------------------
| API Routes — Wollo University Online Exam System
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api (configured in bootstrap/app.php)
| and versioned under /v1.
|
*/

// ------------------------------------------------------------------
// Public routes (no auth required)
// ------------------------------------------------------------------
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/login',  [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);

});

// ------------------------------------------------------------------
// Protected routes (Sanctum token required)
// ------------------------------------------------------------------
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Current user info
    Route::get('/user', fn(Request $request) => $request->user()->load('department'));

    // Auth — logout & change password
    Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
    Route::put('/user/change-password', [\App\Http\Controllers\Api\V1\AuthController::class, 'changePassword']);

    // Global settings (accessible by all authenticated users)
    Route::get('/settings', [\App\Http\Controllers\Api\V1\SystemSettingController::class, 'index']);

    // ------------------------------------------------------------------
    // Super Admin Routes
    // ------------------------------------------------------------------
    Route::prefix('admin')->group(function () {
        Route::apiResource('departments', \App\Http\Controllers\Api\V1\DepartmentController::class);
        Route::post('departments/{department}/assign-head', [\App\Http\Controllers\Api\V1\DepartmentController::class, 'assignHead']);
        Route::apiResource('users', \App\Http\Controllers\Api\V1\AdminUserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('users-export', [\App\Http\Controllers\Api\V1\AdminUserController::class, 'export']);
        Route::post('users-import', [\App\Http\Controllers\Api\V1\AdminUserController::class, 'import']);
        
        Route::apiResource('courses', \App\Http\Controllers\Api\V1\AdminCourseController::class);
        Route::get('courses-export', [\App\Http\Controllers\Api\V1\AdminCourseController::class, 'export']);
        Route::post('courses-import', [\App\Http\Controllers\Api\V1\AdminCourseController::class, 'import']);
        
        Route::post('settings', [\App\Http\Controllers\Api\V1\SystemSettingController::class, 'store']);

        // List all instructors (for assign-head modal), optionally filter by department_id
        Route::get('instructors', function (Request $request) {
            $query = \App\Models\User::whereIn('role', ['instructor', 'dept_head']);
            if ($request->has('department_id')) {
                $query->where('department_id', $request->department_id);
            }
            return response()->json([
                'data' => $query->select('id', 'name', 'email', 'role', 'department_id')
                    ->orderBy('name')
                    ->get(),
            ]);
        });
    });

    // ------------------------------------------------------------------
    // Department Head Routes
    // ------------------------------------------------------------------
    Route::prefix('dept-head')->group(function () {
        Route::apiResource('instructors', \App\Http\Controllers\Api\V1\DeptHead\InstructorController::class);
        Route::apiResource('courses', \App\Http\Controllers\Api\V1\DeptHead\CourseController::class);
        Route::get('students', [\App\Http\Controllers\Api\V1\DeptHead\StudentController::class, 'index']);
        Route::put('students/{id}', [\App\Http\Controllers\Api\V1\DeptHead\StudentController::class, 'update']);
        Route::get('exams', [\App\Http\Controllers\Api\V1\DeptHead\ExamController::class, 'index']);
        Route::post('exams', [\App\Http\Controllers\Api\V1\DeptHead\ExamController::class, 'store']);
    });

    // ------------------------------------------------------------------
    // Instructor Routes
    // ------------------------------------------------------------------
    Route::prefix('instructor')->group(function () {

        // Dashboard stats & lists
        Route::get('/dashboard-stats', [InstructorDashboardController::class, 'stats']);
        Route::get('/recent-exams',    [InstructorDashboardController::class, 'recentExams']);
        Route::get('/upcoming-exams',  [InstructorDashboardController::class, 'upcomingExams']);

        // Exam Management
        Route::apiResource('exams', InstructorExamController::class);

        // Question Banks Management
        Route::apiResource('question-banks', InstructorQuestionBankController::class);
        Route::post('question-banks/{question_bank}/questions', [InstructorQuestionBankController::class, 'storeQuestion']);
        Route::post('question-banks/{question_bank}/import', [InstructorQuestionBankController::class, 'importQuestions']);
        Route::put('questions/{question}', [InstructorQuestionBankController::class, 'updateQuestion']);
        Route::delete('questions/{question}', [InstructorQuestionBankController::class, 'destroyQuestion']);

        // Students Management
        Route::get('/students', [InstructorStudentController::class, 'index']);

        // Reports & Results Management
        Route::get('/reports', [InstructorReportController::class, 'index']);

        // Instructor profile — name, department, year_level, section (for header)
        Route::get('/me', function (Request $request) {
            $user = $request->user()->load('department');
            return response()->json([
                'data' => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'role'        => $user->role,
                    'department'  => $user->department?->name,
                    'year_level'  => $user->year_level,
                    'section'     => $user->section,
                    'course_name' => $user->course_name,
                    'course_code' => $user->course_code,
                ]
            ]);
        });

    });

    // ------------------------------------------------------------------
    // Student Routes
    // ------------------------------------------------------------------
    Route::prefix('student')->group(function () {

        Route::get('/dashboard', [StudentExamController::class, 'dashboard']);
        Route::get('/exams',     [StudentExamController::class, 'index']);
        Route::post('/exams/{exam}/start',  [StudentExamController::class, 'start']);
        Route::post('/exams/{exam}/submit', [StudentExamController::class, 'submit']);
        Route::get('/results',   [StudentExamController::class, 'results']);

    });

});
