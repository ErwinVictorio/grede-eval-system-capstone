<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\EvalutionCommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RecitationController;
use App\Http\Controllers\StudentReportController;
use App\Http\Controllers\TeacherSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Admin.Dashboard');
});

// Authentication Routes
// Show login form at /login (avoid duplicate GET '/' route)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login')->middleware('prevent-back-after-login');

Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// route for admin dashboard
Route::middleware(['role:admin'])->prefix('Admin')->group(function () {
    Route::get('/Dashboard', [AdminController::class, 'index'])->name('Dashboard.admin');
    Route::get('/create-teacher', [AuthController::class, 'createTeacherForm'])->name('create-teacher');
    Route::post('/create-teacher', [TeacherController::class, 'store'])->name('create-teacher.store');
    // Edit/update/delete teacher
    Route::get('/teacher/{id}/edit', [AdminController::class, 'edit'])->name('admin.teacher.edit');
    Route::put('/teacher/{id}', [AdminController::class, 'update'])->name('admin.teacher.update');
    Route::delete('/teacher/{id}', [AdminController::class, 'destroy'])->name('admin.teacher.destroy');
});


// route for teacher dashboard
Route::middleware(['role:teacher'])->prefix('Teacher')->group(function () {
    Route::get('/Dashboard', [TeacherController::class, 'index'])->name('Dashboard.teacher');

    // Delete Student route
    Route::delete('/student/{id}', [TeacherController::class, 'destroy'])->name('student.destroy');


    // Student Report route
    Route::get('/student/{student}/report', [StudentReportController::class, 'show'])->name('student.report');

    Route::get('/flag-student/{id}', [StudentReportController::class, 'ShowFlagFormPage'])->name('flag.view');
    Route::post('/submit-flag', [StudentReportController::class, 'storeFlag'])->name('flag.submit');



    Route::get('/add-student', [TeacherController::class, 'addStudentForm'])->name('add-student');
    Route::post('/add-student', [TeacherController::class, 'storeStudent'])->name('add-student.store');
    // Attendance routes
    Route::get('/attendance', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    // Route::get('/attendance/report/{student}', [AttendanceController::class, 'report'])->name('attendance.report');

    // Quiz routes
    Route::get('/quiz', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz', [QuizController::class, 'store'])->name('quiz.store');
    // Route::get('/quiz/report/{student}', [QuizController::class, 'report'])->name('quiz.report');
    Route::get('/quiz/edit/{id}', [QuizController::class, 'edit'])->name('quiz.edit');
    Route::put('/quiz/{id}', [QuizController::class, 'update'])->name('quiz.update');
    Route::delete('/quiz/{id}', [QuizController::class, 'destroy'])->name('quiz.destroy');

    // Exam routes
    Route::get('/exam', [ExamController::class, 'show'])->name('exam.show');
    Route::post('/exam', [ExamController::class, 'store'])->name('exam.store');
    // Route::get('/exam/report/{student}', [ExamController::class, 'report'])->name('exam.report');
    Route::get('/exam/edit/{id}', [ExamController::class, 'edit'])->name('exam.edit');
    Route::put('/exam/{id}', [ExamController::class, 'update'])->name('exam.update');
    Route::delete('/exam/{id}', [ExamController::class, 'destroy'])->name('exam.destroy');

    // Activity routes
    Route::get('/activity', [ActivityController::class, 'show'])->name('activity.show');
    Route::post('/activity', [ActivityController::class, 'store'])->name('activity.store');
    // Route::get('/activity/report/{student}', [ActivityController::class, 'report'])->name('activity.report');
    Route::get('/activity/edit/{id}', [ActivityController::class, 'edit'])->name('activity.edit');
    Route::put('/activity/{id}', [ActivityController::class, 'update'])->name('activity.update');
    Route::delete('/activity/{id}', [ActivityController::class, 'destroy'])->name('activity.destroy');

    // Project routes
    Route::get('/project', [ProjectController::class, 'show'])->name('project.show');
    Route::post('/project', [ProjectController::class, 'store'])->name('project.store');
    // Route::get('/project/report/{student}', [ProjectController::class, 'report'])->name('project.report');
    Route::get('/project/edit/{id}', [ProjectController::class, 'edit'])->name('project.edit');
    Route::put('/project/{id}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/project/{id}', [ProjectController::class, 'destroy'])->name('project.destroy');

    // Recitation routes
    Route::get('/recitation', [RecitationController::class, 'show'])->name('recitation.show');
    Route::post('/recitation', [RecitationController::class, 'store'])->name('recitation.store');
    // Route::get('/recitation/report/{student}', [RecitationController::class, 'report'])->name('recitation.report');
    Route::get('/recitation/edit/{id}', [RecitationController::class, 'edit'])->name('recitation.edit');
    Route::put('/recitation/{id}', [RecitationController::class, 'update'])->name('recitation.update');
    Route::delete('/recitation/{id}', [RecitationController::class, 'destroy'])->name('recitation.destroy');

    // Teacher settings for weight allocation
    Route::get('/settings', [TeacherSettingsController::class, 'edit'])->name('teacher.settings');
    Route::post('/settings', [TeacherSettingsController::class, 'update'])->name('teacher.settings.update');
});



Route::middleware(['role:councilor'])->prefix('councilor')->group(function () {

    Route::get('/Dashboard', [EvalutionCommentController::class, 'index'])->name('councilorDashboard.view');
    Route::get('/search', [EvalutionCommentController::class, 'search'])->name('councilor.search');
    Route::patch('/status/{id}', [EvalutionCommentController::class, 'updateStatus'])->name('councilor.updateStatus');
    Route::post('/schedule/{id}', [EvalutionCommentController::class, 'setSchedule'])->name('councilor.setSchedule');
    Route::patch('/councilor/referral/{id}/status', [EvalutionCommentController::class, 'updateStatus'])->name('councilor.updateStatus');
});
