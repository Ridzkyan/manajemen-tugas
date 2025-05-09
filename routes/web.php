<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Dosen\MateriController;
use App\Http\Controllers\Dosen\TugasController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Mahasiswa\JoinKelasController;
use App\Http\Controllers\Mahasiswa\MateriController as MahasiswaMateriController;
use App\Http\Controllers\Mahasiswa\TugasController as MahasiswaTugasController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\KontenController;
use App\Http\Controllers\Auth\LoginDosenController;
use App\Http\Controllers\Auth\LoginMahasiswaController;
use App\Http\Controllers\Auth\RegisterController;

// ==============================
// PUBLIC ROUTES
// ==============================

Route::get('/', fn () => view('welcome'))->name('welcome');
Route::redirect('/login', '/'); // Redirect default /login ke halaman utama (karena LoginController tidak digunakan)

// Login Dosen
Route::get('/dosen/login', [LoginDosenController::class, 'showLoginForm'])->name('login.dosen');
Route::post('/dosen/login', [LoginDosenController::class, 'login'])->name('dosen.login');
Route::post('/dosen/logout', [LoginDosenController::class, 'logout'])->name('dosen.logout');

// Login Mahasiswa
Route::get('/mahasiswa/login', [LoginMahasiswaController::class, 'showLoginForm'])->name('login.mahasiswa');
Route::post('/mahasiswa/login', [LoginMahasiswaController::class, 'login'])->name('mahasiswa.login');
Route::post('/mahasiswa/logout', [LoginMahasiswaController::class, 'logout'])->name('mahasiswa.logout');

// Register Mahasiswa
Route::get('/mahasiswa/register', [RegisterController::class, 'showRegistrationForm'])->name('register.mahasiswa');
Route::post('/mahasiswa/register', [RegisterController::class, 'register']);

// Login Admin
Route::get('/admin', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin', [App\Http\Controllers\Auth\AdminLoginController::class, 'login']);
Route::post('/admin/logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');


// Rute untuk notifikasi verifikasi email
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

// Rute untuk proses verifikasi saat klik link email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mahasiswa/dashboard'); // atau sesuaikan redirect sesuai rolenya
})->middleware(['auth', 'signed'])->name('verification.verify');

// Rute untuk mengirim ulang email verifikasi
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Link verifikasi telah dikirim!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ==============================
// PROTECTED ROUTES
// ==============================

// ---------- ADMIN ----------
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengaturan
    Route::get('/pengaturan', fn () => view('admin.admin_pengaturan.pengaturan'))->name('pengaturan');

    // User Management 
    Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users'); // <-- untuk menghindari error route tidak ditemukan
    Route::resource('/users', UserController::class)->except(['show'])->names([
        'index'   => 'users.index',
        'create'  => 'users.create',
        'store'   => 'users.store',
        'edit'    => 'users.edit',
        'update'  => 'users.update',
        'destroy' => 'users.destroy',
    ]);
    Route::put('/reset-password/{user}', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Profil Admin
    Route::get('/profil', [UserController::class, 'editProfile'])->name('profil.edit');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profil.update');
    Route::get('/ganti-password', [UserController::class, 'editPassword'])->name('password.edit');
    Route::put('/ganti-password', [UserController::class, 'updatePassword'])->name('password.update');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');

    // Monitoring & Konten
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/konten', [KontenController::class, 'index'])->name('konten.index');
});


// ---------- DOSEN ----------
Route::middleware(['auth:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dosen\KelasController::class, 'index'])->name('dashboard');
    Route::resource('/kelas', App\Http\Controllers\Dosen\KelasController::class)->except(['show']);
    Route::get('/kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{id}/manage', [App\Http\Controllers\Dosen\KelasController::class, 'manage'])->name('kelas.manage');
    Route::get('/kelas/{kelas}/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::post('/kelas/{kelas}/materi', [MateriController::class, 'store'])->name('materi.store');
    Route::delete('/materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');
    Route::delete('/materi/bulk-delete', [MateriController::class, 'bulkDelete'])->name('materi.bulkDelete');
    Route::get('/kelas/{kelas}/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::post('/kelas/{kelas}/tugas', [TugasController::class, 'store'])->name('tugas.store');
    Route::get('/kelas/{kelas}/tugas/{tugas}/penilaian', [TugasController::class, 'penilaian'])->name('tugas.penilaian');
    Route::post('/kelas/{kelas}/tugas/{tugas}/penilaian', [TugasController::class, 'nilaiTugas'])->name('tugas.nilai');
    Route::view('/materi', 'dosen.materi')->name('materi');
    Route::view('/tugas', 'dosen.tugas')->name('tugas');
    Route::view('/komunikasi', 'dosen.komunikasi')->name('komunikasi');
});

// ---------- MAHASISWA ----------
Route::middleware(['auth:mahasiswa', 'mahasiswa.verified'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Mahasiswa\HomeController::class, 'index'])->name('dashboard');
    Route::get('/join', [JoinKelasController::class, 'index'])->name('join.index');
    Route::post('/join', [JoinKelasController::class, 'store'])->name('join.store');
    Route::get('/kelas/{id}', [JoinKelasController::class, 'show'])->name('kelas.show');
    Route::delete('/kelas/{id}', [JoinKelasController::class, 'leave'])->name('kelas.leave');
    Route::delete('/leave-kelas/{id}', [JoinKelasController::class, 'leave'])->name('leave.kelas');
    Route::get('/kelas/{kelas}/materi', [MahasiswaMateriController::class, 'index'])->name('materi.index');
    Route::get('/kelas/{kelas}/tugas', [MahasiswaTugasController::class, 'index'])->name('tugas.index');

    // Email Verification Routes for Mahasiswa
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});
