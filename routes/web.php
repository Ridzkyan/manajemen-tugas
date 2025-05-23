<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Dosen\MateriController;
use App\Http\Controllers\Dosen\TugasController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Mahasiswa\JoinKelasController;
use App\Http\Controllers\Mahasiswa\MateriController as MahasiswaMateriController;
use App\Http\Controllers\Mahasiswa\TugasController as MahasiswaTugasController;

// ==============================
// PUBLIC ROUTES
// ==============================

// Halaman landing awal
Route::get('/', fn () => view('welcome'))->name('welcome');

Auth::routes(['register' => true]);
Auth::routes(['verify' => true]);

// Login multi-role
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Login khusus admin (terpisah)
Route::get('/admin', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin', [App\Http\Controllers\Auth\AdminLoginController::class, 'login']);
Route::post('/admin/logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');

// ==============================
// PROTECTED ROUTES (butuh login)
// ==============================

// ---------- ADMIN ----------
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::get('/pengaturan', fn () => view('admin.admin_pengaturan.pengaturan'))->name('pengaturan');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('dashboard.users');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::get('/edit/{user}', [UserController::class, 'edit'])->name('edit');
    Route::put('/update/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/destroy/{user}', [UserController::class, 'destroy'])->name('destroy');
    Route::put('/reset-password/{user}', [UserController::class, 'resetPassword'])->name('reset.password');
    Route::get('/profil', [UserController::class, 'editProfile'])->name('profil.edit');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profil.update');
    Route::get('/ganti-password', [UserController::class, 'editPassword'])->name('password.edit');
    Route::put('/ganti-password', [UserController::class, 'updatePassword'])->name('password.update');
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
Route::middleware(['auth:mahasiswa, verified'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Mahasiswa\HomeController::class, 'index'])->name('dashboard');

    Route::get('/join', [JoinKelasController::class, 'index'])->name('join.index');
    Route::post('/join', [JoinKelasController::class, 'store'])->name('join.store');
    Route::get('/kelas/{id}', [JoinKelasController::class, 'show'])->name('kelas.show');
    Route::delete('/kelas/{id}', [JoinKelasController::class, 'leave'])->name('kelas.leave');
    Route::delete('/leave-kelas/{id}', [JoinKelasController::class, 'leave'])->name('leave.kelas');

    Route::get('/kelas/{kelas}/materi', [MahasiswaMateriController::class, 'index'])->name('materi.index');
    Route::get('/kelas/{kelas}/tugas', [MahasiswaTugasController::class, 'index'])->name('tugas.index');
});
