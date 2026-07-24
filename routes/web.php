<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\LearnerAttendanceController;
use App\Http\Controllers\Admin\ClassSettingController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AssignmentQuestionController;
use App\Http\Controllers\Admin\RaportController;
use App\Http\Controllers\Learner\AssignmentController as LearnerAssignmentController;
use App\Http\Controllers\Auth\LearnerLoginController;

/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/
// Welcome page — only for guests (not logged in)
Route::get('/', function () {
    return view('welcome');
})->middleware('guest');

// Murid login lewat Kelas + Nama + PIN (bukan email/password, terpisah dari Auth::user())
Route::get('/api/learners-by-grade/{gradeLevel}', [LearnerLoginController::class, 'getByGrade']);
Route::post('/learner-login', [LearnerLoginController::class, 'login'])
    ->middleware('throttle:20,1')
    ->name('learner.login');


/*
|--------------------------------------------------------------------------
| Authenticated Redirect Based on Role
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match (true) {
        $user->hasRole('admin') => redirect('/admin/dashboard'),
        $user->hasRole('guru') => redirect('/guru/dashboard'),
        $user->hasRole('learner') => redirect('/learner/dashboard'),
        default => abort(403),
    };
// })->middleware(['auth'])->name('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated User Routes (All Roles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
// Route::middleware(['auth'])->group(function () {

    // Guru
    Route::get('/guru/dashboard', [GuruController::class, 'index'])->name('guru.dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Edit the User Profile
    Route::get('/admin/profile/edit', [ProfileController::class, 'edit'])
        ->middleware('auth')
        ->name('admin.profile.edit');

    Route::patch('/admin/profile/update', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('admin.profile.update');

    // Update password from admin profile page
    Route::put('/admin/profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware('auth')
        ->name('admin.profile.password');

    // Delete account from admin profile page
    Route::delete('/admin/profile/delete', [ProfileController::class, 'destroy'])
        ->middleware('auth')
        ->name('admin.profile.destroy');

    // ------------------------------------------------------------------
    // Admin-only routes (require the 'admin' role, not just being logged in)
    // ------------------------------------------------------------------
    Route::middleware('role:admin')->group(function () {

        // Show registration form
        Route::get('/register-user', [RegisterController::class, 'showAdminRegisterForm'])->name('admin.register.form');

        // Handle registration
        Route::post('/register-user', [RegisterController::class, 'registerByAdmin'])->name('admin.register.user');

        // Admin
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        // Guru management
        Route::get('/admin/guru', [GuruController::class, 'manage'])->name('admin.guru.index');
        Route::delete('/admin/guru/{user}', [GuruController::class, 'destroy'])->name('admin.guru.destroy');

        // Reports
        Route::view('/admin/reports', 'admin.reports.index')->name('admin.reports');

        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/sendmail', [UserController::class, 'sendMail'])->name('users.sendmail');
        Route::get('/users/sendmail', fn() => redirect()->route('users.index'));

        // Attendance
        Route::get('attendance', [LearnerAttendanceController::class, 'index'])->name('admin.attendance.index');
        Route::post('attendance/store', [LearnerAttendanceController::class, 'store'])->name('admin.attendance.store');

        // Class Settings (Tingkat Kelas & Kelompok)
        Route::get('/admin/class-settings', [ClassSettingController::class, 'index'])->name('admin.class-settings.index');
        Route::post('/admin/grade-levels', [ClassSettingController::class, 'storeGradeLevel'])->name('admin.grade-levels.store');
        Route::put('/admin/grade-levels/{gradeLevel}', [ClassSettingController::class, 'updateGradeLevel'])->name('admin.grade-levels.update');
        Route::delete('/admin/grade-levels/{gradeLevel}', [ClassSettingController::class, 'destroyGradeLevel'])->name('admin.grade-levels.destroy');
        Route::post('/admin/sections', [ClassSettingController::class, 'storeSection'])->name('admin.sections.store');
        Route::put('/admin/sections/{section}', [ClassSettingController::class, 'updateSection'])->name('admin.sections.update');
        Route::delete('/admin/sections/{section}', [ClassSettingController::class, 'destroySection'])->name('admin.sections.destroy');

        // Learners (data murid) — was public with no auth at all, now admin-only
        Route::resource('learners', LearnerController::class)->names('admin.learners');

        // Assignments (Tugas)
        Route::resource('admin/assignments', AssignmentController::class)->names('admin.assignments');

        // Assignment Questions (Soal)
        Route::post('admin/assignments/{assignment}/questions', [AssignmentQuestionController::class, 'store'])->name('admin.assignments.questions.store');
        Route::put('admin/assignments/{assignment}/questions/{question}', [AssignmentQuestionController::class, 'update'])->name('admin.assignments.questions.update');
        Route::delete('admin/assignments/{assignment}/questions/{question}', [AssignmentQuestionController::class, 'destroy'])->name('admin.assignments.questions.destroy');

        // Assign / Unassign Murid ke Tugas
        Route::post('admin/assignments/{assignment}/assign', [AssignmentController::class, 'assignLearners'])->name('admin.assignments.assign');
        Route::delete('admin/assignments/{assignment}/unassign/{learner}', [AssignmentController::class, 'unassignLearner'])->name('admin.assignments.unassign');

        // Nilai Jawaban Murid (Essay)
        Route::get('admin/assignments/{assignment}/learner/{learner}', [AssignmentController::class, 'showLearnerAnswers'])->name('admin.assignments.learner-answers');
        Route::post('admin/assignments/{assignment}/learner/{learner}/grade', [AssignmentController::class, 'gradeLearnerAnswers'])->name('admin.assignments.learner-answers.grade');

        // Raport Siswa
        Route::get('admin/raport', [RaportController::class, 'index'])->name('admin.raport.index');
        Route::get('admin/raport/{learner}', [RaportController::class, 'show'])->name('admin.raport.show');
    });
});


/*
|--------------------------------------------------------------------------
| Learner (Murid) Routes — sesi terpisah, bukan Auth::user()
|--------------------------------------------------------------------------
*/
Route::middleware('auth.learner')->group(function () {
    Route::get('/learner/dashboard', [LearnerController::class, 'learnerDashboard'])->name('learner.dashboard');
    Route::post('/learner-logout', [LearnerLoginController::class, 'logout'])->name('learner.logout');

    // Tugas (dikerjakan oleh murid)
    Route::get('/learner/tugas', [LearnerAssignmentController::class, 'index'])->name('learner.assignments.index');
    Route::get('/learner/tugas/{assignment}', [LearnerAssignmentController::class, 'show'])->name('learner.assignments.show');
    Route::post('/learner/tugas/{assignment}/submit', [LearnerAssignmentController::class, 'submit'])->name('learner.assignments.submit');

    // Raport (read-only)
    Route::get('/learner/raport', [LearnerAssignmentController::class, 'raport'])->name('learner.raport');
});


/*
|--------------------------------------------------------------------------
| Auth Routes (Login, Register, Password, etc.)
|--------------------------------------------------------------------------
*/

// Breeze auth routes (login, register, etc.)
require __DIR__.'/auth.php';
