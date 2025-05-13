<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\KontenController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Auth\LoginDosenController;
use App\Http\Controllers\Auth\LoginMahasiswaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dosen\MateriController;
use App\Http\Controllers\Dosen\TugasController;
use App\Http\Controllers\Mahasiswa\JoinKelasController;
use App\Http\Controllers\Mahasiswa\MateriController as MahasiswaMateriController;
use App\Http\Controllers\Mahasiswa\TugasController as MahasiswaTugasController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\KontenController;
use App\Http\Controllers\Auth\LoginDosenController;
use App\Http\Controllers\Auth\LoginMahasiswaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dosen\RekapController;
// ==============================
// PUBLIC ROUTES
// ==============================

Route::get('/', fn () => view('welcome'))->name('welcome');

Route::get('/login', function () {
    if (Auth::guard('admin')->check()) return redirect()->route('admin.dashboard');
    if (Auth::guard('dosen')->check()) return redirect()->route('dosen.dashboard');
    if (Auth::guard('mahasiswa')->check()) return redirect()->route('mahasiswa.dashboard');
    return redirect()->route('login.mahasiswa');
})->name('login');

// Home fallback untuk default redirect
Route::get('/home', function () {
    return redirect()->route('login');
});

// Login & Register Mahasiswa
Route::get('/mahasiswa/login', [LoginMahasiswaController::class, 'showLoginForm'])->name('login.mahasiswa');
Route::post('/mahasiswa/login', [LoginMahasiswaController::class, 'login'])->name('mahasiswa.login');
Route::post('/mahasiswa/logout', [LoginMahasiswaController::class, 'logout'])->name('mahasiswa.logout');

Route::get('/mahasiswa/register', [RegisterController::class, 'showRegistrationForm'])->name('register.mahasiswa');
Route::post('/mahasiswa/register', [RegisterController::class, 'register'])->name('mahasiswa.register');

// Login Dosen
Route::get('/dosen/login', [LoginDosenController::class, 'showLoginForm'])->name('login.dosen');
Route::post('/dosen/login', [LoginDosenController::class, 'login'])->name('dosen.login');
Route::post('/dosen/logout', [LoginDosenController::class, 'logout'])->name('dosen.logout');

// Login Admin
Route::get('/admin', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin', [App\Http\Controllers\Auth\AdminLoginController::class, 'login']);
Route::post('/admin/logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');

// Email Verification
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mahasiswa/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link verifikasi telah dikirim!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ==============================
// PROTECTED ROUTES
// ==============================

// ---------- ADMIN ----------
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pengaturan', fn () => view('admin.admin_pengaturan.pengaturan'))->name('pengaturan');

    Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users');
    Route::resource('/users', UserController::class)->except(['show'])->names('users');
    Route::put('/reset-password/{user}', [UserController::class, 'resetPassword'])->name('users.reset-password');

    Route::get('/profil', [UserController::class, 'editProfile'])->name('profil.edit');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profil.update');
    Route::get('/ganti-password', [UserController::class, 'editPassword'])->name('password.edit');
    Route::put('/ganti-password', [UserController::class, 'updatePassword'])->name('password.update');

    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/konten', [KontenController::class, 'index'])->name('konten.index');
});

// ---------- DOSEN ----------
Route::middleware(['auth:dosen', 'prevent-back-history'])->prefix('dosen')->name('dosen.')->group(function () {
    // Dashboard → views/dosen/dashboard.blade.php
    Route::get('/dashboard', [App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dashboard');

    // Materi & Kelas → views/dosen/materi_kelas/materi_dan_kelas.blade.php
    Route::get('/materi-kelas', [App\Http\Controllers\Dosen\KelasController::class, 'materiDanKelas'])->name('materi_kelas.index');
    Route::post('/materi-kelas/upload', [App\Http\Controllers\Dosen\KelasController::class, 'uploadMateriGlobal'])->name('materi_kelas.upload');
    Route::get('/materi-kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'detailMateri'])->name('materi_kelas.detail');

    // Kelola Kelas → views/dosen/kelola_kelas/
    Route::get('/kelas', [App\Http\Controllers\Dosen\KelasController::class, 'index'])->name('kelola_kelas.index');
    Route::get('/kelas/create', [App\Http\Controllers\Dosen\KelasController::class, 'create'])->name('kelola_kelas.create');
    Route::post('/kelas', [App\Http\Controllers\Dosen\KelasController::class, 'store'])->name('kelola_kelas.store');
    Route::get('/kelas/{id}/edit', [App\Http\Controllers\Dosen\KelasController::class, 'edit'])->name('kelola_kelas.edit');
    Route::put('/kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'update'])->name('kelola_kelas.update');
    Route::delete('/kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'destroy'])->name('kelola_kelas.destroy');
    Route::get('/kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'show'])->name('kelola_kelas.show');

    // Komunikasi → views/dosen/komunikasi/komunikasi.blade.php
    Route::get('/komunikasi', [App\Http\Controllers\Dosen\KelasController::class, 'komunikasi'])->name('komunikasi');

    // Kelola isi kelas (materi & tugas per kelas)
    Route::get('/kelas/{id}/manage', [App\Http\Controllers\Dosen\KelasController::class, 'manage'])->name('kelola_kelas.manage');
    Route::post('/kelas/{id}/materi', [App\Http\Controllers\Dosen\KelasController::class, 'uploadMateri'])->name('kelola_kelas.upload_materi');

    // Tugas & Ujian → views/dosen/tugas_ujian/
    Route::get('/tugas-ujian', [TugasController::class, 'pilihKelas'])->name('tugas_ujian.pilih_kelas');
    Route::get('/tugas-ujian', [App\Http\Controllers\Dosen\TugasController::class, 'pilihKelas'])->name('tugas_ujian.pilih_kelas'); // <--- Tambahan: pilih kelas dulu
    Route::get('/tugas-ujian/{kelas}', [App\Http\Controllers\Dosen\TugasController::class, 'index'])->name('tugas_ujian.index');
    Route::post('/tugas-ujian/{kelas}', [App\Http\Controllers\Dosen\TugasController::class, 'store'])->name('tugas_ujian.store');
    Route::get('/tugas-ujian/{kelas}/detail', [App\Http\Controllers\Dosen\TugasController::class, 'detail'])->name('tugas_ujian.detail');
    Route::get('/tugas-ujian/{kelas}/{tugas}/penilaian', [App\Http\Controllers\Dosen\TugasController::class, 'penilaian'])->name('tugas_ujian.penilaian');
    Route::post('/tugas-ujian/{kelas}/{tugas}/penilaian', [App\Http\Controllers\Dosen\TugasController::class, 'nilaiTugas'])->name('tugas_ujian.nilai');
    Route::get('/tugas-ujian/{kelas}/{tugas}/mahasiswa', [App\Http\Controllers\Dosen\TugasController::class, 'penilaianPerMahasiswa'])->name('tugas_ujian.mahasiswa');

    // Rekap Nilai → views/dosen/rekap_nilai/
    Route::get('/rekap-nilai', [App\Http\Controllers\Dosen\TugasController::class, 'rekapNilai'])->name('rekap_nilai.index');
    Route::get('/rekap-nilai/{kelas}', [App\Http\Controllers\Dosen\TugasController::class, 'rekapPerKelas'])->name('rekap_nilai.detail');
    Route::get('/rekap-nilai/export/{kelasId}', [App\Http\Controllers\Dosen\TugasController::class, 'exportRekap'])->name('rekap_nilai.export');

      // 🔧 Pengaturan Dosen
    Route::get('/pengaturan', [App\Http\Controllers\Dosen\ProfilDosenController::class, 'pengaturan'])->name('pengaturan');

    Route::get('/pengaturan/index', [App\Http\Controllers\Dosen\ProfilDosenController::class, 'editProfil'])->name('pengaturan.profil');
    Route::post('/pengaturan/index', [App\Http\Controllers\Dosen\ProfilDosenController::class, 'updateProfil'])->name('pengaturan.profil.update');

    Route::get('/pengaturan/password', [App\Http\Controllers\Dosen\ProfilDosenController::class, 'editPassword'])->name('pengaturan.password');
    Route::post('/pengaturan/password', [App\Http\Controllers\Dosen\ProfilDosenController::class, 'updatePassword'])->name('pengaturan.password.update');
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
});
