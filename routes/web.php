<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\KontenController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Auth\LoginDosenController;
use App\Http\Controllers\Auth\LoginMahasiswaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dosen\TugasController;
use App\Http\Controllers\Mahasiswa\JoinKelasController;
use App\Http\Controllers\Mahasiswa\MateriController as MahasiswaMateriController;
use App\Http\Controllers\Mahasiswa\TugasController as MahasiswaTugasController;
use App\Http\Controllers\Mahasiswa\KomunikasiController;
use App\Http\Controllers\Mahasiswa\PengaturanController as MahasiswaPengaturanController;
use App\Http\Controllers\Mahasiswa\HomeController;
use App\Http\Controllers\Mahasiswa\UjianController;
use App\Http\Controllers\Auth\VerificationController;

// ==============================
// PUBLIC ROUTES
// ==============================

Route::get('/', fn () => view('welcome'))->name('welcome');

Route::get('/login', function () {
    if (Auth::guard('admin')->check()) return redirect()->route('admin.dashboard');
    if (Auth::guard('dosen')->check()) return redirect()->route('dosen.dashboard');
    if (Auth::guard('mahasiswa')->check()) {
        $user = Auth::guard('mahasiswa')->user();
        return $user->hasVerifiedEmail()
            ? redirect()->route('mahasiswa.dashboard')
            : redirect()->route('mahasiswa.email-verification.notice');
    }
    return redirect()->route('login.mahasiswa');
})->name('login');

Route::get('/home', fn () => redirect()->route('login'));

// Login & Register Mahasiswa
Route::get('/mahasiswa/login', [LoginMahasiswaController::class, 'showLoginForm'])->name('login.mahasiswa');
Route::post('/mahasiswa/login', [LoginMahasiswaController::class, 'login'])->name('mahasiswa.login');
Route::post('/mahasiswa/logout', [LoginMahasiswaController::class, 'logout'])->name('mahasiswa.logout');
Route::get('/mahasiswa/register', [RegisterController::class, 'showRegistrationForm'])->name('register.mahasiswa');
Route::post('/mahasiswa/register', [RegisterController::class, 'register'])->name('mahasiswa.register');

// Verifikasi Email Mahasiswa (HARUS di luar auth:mahasiswa)
Route::get('/mahasiswa/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('mahasiswa.email-verification.verify');

// Halaman notifikasi & kirim ulang
Route::middleware(['auth:mahasiswa'])->prefix('mahasiswa/email')->name('mahasiswa.email-verification.')->group(function () {
    Route::get('/verify', [VerificationController::class, 'show'])->name('notice');
    Route::post('/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('resend');
});

// Login Dosen
Route::get('/dosen/login', [LoginDosenController::class, 'showLoginForm'])->name('login.dosen');
Route::post('/dosen/login', [LoginDosenController::class, 'login'])->name('dosen.login');
Route::post('/dosen/logout', [LoginDosenController::class, 'logout'])->name('dosen.logout');

// Login Admin
Route::get('/admin', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin', [App\Http\Controllers\Auth\AdminLoginController::class, 'login']);
Route::post('/admin/logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');

// ==============================
// PROTECTED ROUTES
// ==============================

// ADMIN
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/pengaturan', 'admin.admin_pengaturan.pengaturan')->name('pengaturan');
    Route::resource('/users', UserController::class)->except(['show'])->names('users');
    Route::put('/reset-password/{user}', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/profil', [UserController::class, 'editProfile'])->name('profil.edit');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profil.update');
    Route::get('/ganti-password', [UserController::class, 'editPassword'])->name('password.edit');
    Route::put('/ganti-password', [UserController::class, 'updatePassword'])->name('password.update');
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/konten', [KontenController::class, 'index'])->name('konten.index');
    Route::patch('/konten/materi/{id}/setujui', [KontenController::class, 'setujuiMateri'])->name('konten.materi.setujui');
    Route::patch('/konten/materi/{id}/tolak', [KontenController::class, 'tolakMateri'])->name('konten.materi.tolak');
    Route::patch('/konten/tugas/{id}/setujui', [KontenController::class, 'setujuiTugas'])->name('konten.tugas.setujui');
    Route::patch('/konten/tugas/{id}/tolak', [KontenController::class, 'tolakTugas'])->name('konten.tugas.tolak');
    Route::post('/backup', [PengaturanController::class, 'backup'])->name('backup');
    Route::post('/backup-zip', [PengaturanController::class, 'backupZip'])->name('backup.zip');
    Route::post('/restore', [PengaturanController::class, 'restore'])->name('restore');
});

// DOSEN
Route::middleware(['auth:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/materi-kelas', [App\Http\Controllers\Dosen\KelasController::class, 'materiDanKelas'])->name('materi_kelas.index');
    Route::post('/materi-kelas/upload', [App\Http\Controllers\Dosen\KelasController::class, 'uploadMateriGlobal'])->name('materi_kelas.upload');
    Route::get('/materi-kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'detailMateri'])->name('materi_kelas.detail');
    Route::resource('/kelas', App\Http\Controllers\Dosen\KelasController::class)->names('kelola_kelas');
    Route::get('/kelas/{id}/manage', [App\Http\Controllers\Dosen\KelasController::class, 'manage'])->name('kelola_kelas.manage');
    Route::post('/kelas/{id}/materi', [App\Http\Controllers\Dosen\KelasController::class, 'uploadMateri'])->name('kelola_kelas.upload_materi');
    Route::get('/komunikasi', [App\Http\Controllers\Dosen\KelasController::class, 'komunikasi'])->name('komunikasi');
    Route::resource('/tugas-ujian', TugasController::class)->parameters(['tugas-ujian' => 'kelas'])->names('tugas_ujian');
    Route::get('/rekap-nilai', [TugasController::class, 'rekapNilai'])->name('rekap_nilai.index');
    Route::get('/rekap-nilai/{kelas}', [TugasController::class, 'rekapPerKelas'])->name('rekap_nilai.detail');
    Route::get('/rekap-nilai/export/{kelasId}', [TugasController::class, 'exportRekap'])->name('rekap_nilai.export');
});

// MAHASISWA
Route::middleware(['auth:mahasiswa', 'verified'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/kelas', [MahasiswaMateriController::class, 'daftarKelasMateri'])->name('kelas.index');
    Route::get('/kelas/{id}', [JoinKelasController::class, 'show'])->name('kelas.show');
    Route::delete('/kelas/{id}', [JoinKelasController::class, 'leave'])->name('kelas.leave');
    Route::get('/join', [JoinKelasController::class, 'index'])->name('join.index');
    Route::post('/join', [JoinKelasController::class, 'store'])->name('join.store');
    Route::get('/kelas/{kelas}/materi', [MahasiswaMateriController::class, 'index'])->name('materi.index');
    Route::resource('/kelas/{kelas}/tugas', MahasiswaTugasController::class)->names('kelas.tugas');
    Route::prefix('/kelas/{kelas}/ujian')->name('ujian.')->group(function () {
        Route::get('/', [UjianController::class, 'index'])->name('index');
        Route::get('/create', [UjianController::class, 'create'])->name('create');
        Route::post('/', [UjianController::class, 'store'])->name('store');
        Route::get('/{id}', [UjianController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UjianController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UjianController::class, 'update'])->name('update');
        Route::delete('/{id}', [UjianController::class, 'destroy'])->name('destroy');
    });
    Route::get('/komunikasi', [KomunikasiController::class, 'index'])->name('komunikasi.index');
    Route::get('/pengaturan', [MahasiswaPengaturanController::class, 'index'])->name('pengaturan.index');
    Route::get('/edit-profil', [MahasiswaPengaturanController::class, 'editProfile'])->name('profile-edit.edit');
    Route::post('/edit-profil', [MahasiswaPengaturanController::class, 'updateProfile'])->name('profile-edit.update');
    Route::get('/ganti-password', [MahasiswaPengaturanController::class, 'editPassword'])->name('password-edit.edit');
    Route::post('/ganti-password', [MahasiswaPengaturanController::class, 'updatePassword'])->name('password-edit.update');
});
