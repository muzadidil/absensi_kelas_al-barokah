<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ClassSettingController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\RaportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Guru\AssignmentController as GuruAssignmentController;
use App\Http\Controllers\Guru\AssignmentQuestionController as GuruAssignmentQuestionController;
use App\Http\Controllers\Guru\TypingLevelController;
use App\Http\Controllers\Guru\QuizLevelController;
use App\Http\Controllers\Guru\QuizQuestionController;
use App\Http\Controllers\Learner\AssignmentController as LearnerAssignmentController;
use App\Http\Controllers\Learner\TypingController as LearnerTypingController;
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
})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated User Routes (All Roles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Guru
    Route::get('/guru/dashboard', [GuruController::class, 'index'])->name('guru.dashboard');

    // Absensi (Admin & Guru bisa isi)
    Route::middleware('role:admin|guru')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    });

    // ------------------------------------------------------------------
    // Guru-only routes: kelola Tugas (soal, penugasan ke murid) sepenuhnya
    // jadi tanggung jawab Guru. Admin hanya memantau & menilai (lihat grup
    // role:admin di bawah).
    // ------------------------------------------------------------------
    Route::middleware('role:guru')->group(function () {
        Route::resource('guru/assignments', GuruAssignmentController::class)->names('guru.assignments');

        Route::post('guru/assignments/{assignment}/questions', [GuruAssignmentQuestionController::class, 'store'])->name('guru.assignments.questions.store');
        Route::put('guru/assignments/{assignment}/questions/{question}', [GuruAssignmentQuestionController::class, 'update'])->name('guru.assignments.questions.update');
        Route::delete('guru/assignments/{assignment}/questions/{question}', [GuruAssignmentQuestionController::class, 'destroy'])->name('guru.assignments.questions.destroy');

        Route::post('guru/assignments/{assignment}/assign', [GuruAssignmentController::class, 'assignLearners'])->name('guru.assignments.assign');
        Route::delete('guru/assignments/{assignment}/unassign/{learner}', [GuruAssignmentController::class, 'unassignLearner'])->name('guru.assignments.unassign');

        // Master Latihan Mengetik 10 Jari (tahap & tombol yang dilatih)
        Route::get('guru/typing-levels', [TypingLevelController::class, 'index'])->name('guru.typing-levels.index');
        Route::post('guru/typing-levels', [TypingLevelController::class, 'store'])->name('guru.typing-levels.store');
        Route::post('guru/typing-levels/{typingLevel}/duplicate', [TypingLevelController::class, 'duplicate'])->name('guru.typing-levels.duplicate');
        Route::put('guru/typing-levels/{typingLevel}', [TypingLevelController::class, 'update'])->name('guru.typing-levels.update');
        Route::delete('guru/typing-levels/{typingLevel}', [TypingLevelController::class, 'destroy'])->name('guru.typing-levels.destroy');

        // Master Kuis Pilihan Ganda berjenjang (tahap + soal + opsi)
        Route::get('guru/quiz-levels', [QuizLevelController::class, 'index'])->name('guru.quiz-levels.index');
        Route::post('guru/quiz-levels', [QuizLevelController::class, 'store'])->name('guru.quiz-levels.store');
        Route::get('guru/quiz-levels/{quizLevel}', [QuizLevelController::class, 'show'])->name('guru.quiz-levels.show');
        Route::post('guru/quiz-levels/{quizLevel}/duplicate', [QuizLevelController::class, 'duplicate'])->name('guru.quiz-levels.duplicate');
        Route::put('guru/quiz-levels/{quizLevel}', [QuizLevelController::class, 'update'])->name('guru.quiz-levels.update');
        Route::delete('guru/quiz-levels/{quizLevel}', [QuizLevelController::class, 'destroy'])->name('guru.quiz-levels.destroy');

        Route::post('guru/quiz-levels/{quizLevel}/questions', [QuizQuestionController::class, 'store'])->name('guru.quiz-questions.store');
        Route::put('guru/quiz-levels/{quizLevel}/questions/{question}', [QuizQuestionController::class, 'update'])->name('guru.quiz-questions.update');
        Route::delete('guru/quiz-levels/{quizLevel}/questions/{question}', [QuizQuestionController::class, 'destroy'])->name('guru.quiz-questions.destroy');
    });

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

        // Rekap Absensi
        Route::get('/admin/rekap-absensi', [AttendanceController::class, 'rekap'])->name('attendance.rekap');

        // Jadwal Pelajaran (Mata Pelajaran & Jam Pelajaran)
        Route::get('/admin/schedule', [ScheduleController::class, 'index'])->name('admin.schedule.index');
        Route::post('/admin/subjects', [ScheduleController::class, 'storeSubject'])->name('admin.subjects.store');
        Route::put('/admin/subjects/{subject}', [ScheduleController::class, 'updateSubject'])->name('admin.subjects.update');
        Route::delete('/admin/subjects/{subject}', [ScheduleController::class, 'destroySubject'])->name('admin.subjects.destroy');
        Route::post('/admin/jam-pelajaran', [ScheduleController::class, 'storeJamPelajaran'])->name('admin.jam-pelajaran.store');
        Route::put('/admin/jam-pelajaran/{jamPelajaran}', [ScheduleController::class, 'updateJamPelajaran'])->name('admin.jam-pelajaran.update');
        Route::delete('/admin/jam-pelajaran/{jamPelajaran}', [ScheduleController::class, 'destroyJamPelajaran'])->name('admin.jam-pelajaran.destroy');

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

        // Tugas (read-only, dibuat & dikelola oleh Guru)
        Route::get('admin/assignments', [AssignmentController::class, 'index'])->name('admin.assignments.index');
        Route::get('admin/assignments/{assignment}', [AssignmentController::class, 'show'])->name('admin.assignments.show');

        // Nilai Jawaban Murid (Essay) — evaluasi tetap tugas Admin
        Route::get('admin/assignments/{assignment}/learner/{learner}', [AssignmentController::class, 'showLearnerAnswers'])->name('admin.assignments.learner-answers');
        Route::post('admin/assignments/{assignment}/learner/{learner}/grade', [AssignmentController::class, 'gradeLearnerAnswers'])->name('admin.assignments.learner-answers.grade');

        // Raport Siswa
        Route::get('admin/raport', [RaportController::class, 'index'])->name('admin.raport.index');
        Route::get('admin/raport/{learner}', [RaportController::class, 'show'])->name('admin.raport.show');

        // Pengaturan Situs (branding: favicon, logo login, alamat)
        Route::get('admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
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

    // Latihan Mengetik 10 Jari
    Route::get('/learner/mengetik', [LearnerTypingController::class, 'index'])->name('learner.typing.index');
    Route::get('/learner/mengetik/{typingLevel}', [LearnerTypingController::class, 'show'])->name('learner.typing.show');
    Route::post('/learner/mengetik/{typingLevel}/submit', [LearnerTypingController::class, 'submit'])->name('learner.typing.submit');
});


/*
|--------------------------------------------------------------------------
| Auth Routes (Login, Register, Password, etc.)
|--------------------------------------------------------------------------
*/

// Breeze auth routes (login, register, etc.)
require __DIR__.'/auth.php';
