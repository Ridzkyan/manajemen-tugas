<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Auth
use App\Http\Controllers\Auth\LoginDosenController;
use App\Http\Controllers\Auth\LoginMahasiswaController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;

// Admin
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\KontenController;
use App\Http\Controllers\Admin\PengaturanController;

// Dosen
use App\Http\Controllers\Dosen\SearchController;
use App\Http\Controllers\Dosen\KelasController as DosenKelasController;
use App\Http\Controllers\Dosen\MateriController;
use App\Http\Controllers\Dosen\TugasController;
use App\Http\Controllers\Dosen\RekapController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\TugasController as DosenTugasController;

use App\Http\Controllers\Dosen\ProfilDosenController;

// Mahasiswa
use App\Http\Controllers\Mahasiswa\HomeController;
use App\Http\Controllers\Mahasiswa\JoinKelasController;
use App\Http\Controllers\Mahasiswa\MateriController as MahasiswaMateriController;
use App\Http\Controllers\Mahasiswa\TugasController as MahasiswaTugasController;
use App\Http\Controllers\Mahasiswa\UjianController;
use App\Http\Controllers\Mahasiswa\KomunikasiController;
use App\Http\Controllers\Mahasiswa\PenilaianController;
use App\Http\Controllers\Mahasiswa\PengaturanController as MahasiswaPengaturanController;

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

// ==============================
// MAHASISWA - AUTH & VERIFIKASI
// ==============================

Route::get('/mahasiswa/login', [LoginMahasiswaController::class, 'showLoginForm'])->name('login.mahasiswa');
Route::post('/mahasiswa/login', [LoginMahasiswaController::class, 'login'])->name('mahasiswa.login');
Route::post('/mahasiswa/logout', [LoginMahasiswaController::class, 'logout'])->name('mahasiswa.logout');

Route::get('/mahasiswa/register', [RegisterController::class, 'showRegistrationForm'])->name('register.mahasiswa');
Route::post('/mahasiswa/register', [RegisterController::class, 'register'])->name('mahasiswa.register');

// Verifikasi Email (di luar auth:mahasiswa)
Route::get('/mahasiswa/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('mahasiswa.email-verification.verify');

// Halaman notifikasi & kirim ulang verifikasi
Route::middleware(['auth:mahasiswa'])->prefix('mahasiswa/email')->name('mahasiswa.email-verification.')->group(function () {
    Route::get('/verify', [VerificationController::class, 'show'])->name('notice');
    Route::post('/verification-notification', [VerificationController::class, 'resend'])->middleware('throttle:6,1')->name('resend');
});

// ==============================
// ADMIN ROUTES
// ==============================

Route::get('/admin', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

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

    Route::patch('/admin/konten/materi/{id}/setujui', [KontenController::class, 'setujuiMateri'])->name('admin.konten.materi.setujui');
    Route::patch('/admin/konten/materi/{id}/tolak', [KontenController::class, 'tolakMateri'])->name('admin.konten.materi.tolak');

    Route::patch('/admin/konten/tugas/{id}/setujui', [KontenController::class, 'setujuiTugas'])->name('admin.konten.tugas.setujui');
    Route::patch('/admin/konten/tugas/{id}/tolak', [KontenController::class, 'tolakTugas'])->name('admin.konten.tugas.tolak');

    Route::get('/pengaturan/data', [PengaturanController::class, 'data'])->name('pengaturan.data');
    Route::post('/backup', [PengaturanController::class, 'backup'])->name('backup');
    Route::post('/backup-zip', [PengaturanController::class, 'backupZip'])->name('backup.zip');
    Route::post('/restore', [PengaturanController::class, 'restore'])->name('restore');
});

// ==============================
// DOSEN ROUTES
// ==============================

Route::get('/dosen/login', [LoginDosenController::class, 'showLoginForm'])->name('login.dosen');
Route::post('/dosen/login', [LoginDosenController::class, 'login'])->name('dosen.login');
Route::post('/dosen/logout', [LoginDosenController::class, 'logout'])->name('dosen.logout');

Route::middleware(['auth:dosen', 'prevent-back-history'])->prefix('dosen')->name('dosen.')->group(function () {
    // Search Global
    Route::get('/search', [App\Http\Controllers\Dosen\SearchController::class, 'index'])->name('search');

    // Dashboard → views/dosen/dashboard.blade.php
    Route::get('/dashboard', [App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dashboard');

    // Materi & Kelas → views/dosen/materi_kelas/materi_dan_kelas.blade.php
    Route::get('/materi-kelas', [DosenKelasController::class, 'materiDanKelas'])->name('materi_kelas.index'); // Arham nambah
    Route::get('/materi-kelas/{id}-{slug}', [DosenKelasController::class, 'detailMateri'])->name('materi_kelas.detail'); // Arham nambah
    Route::delete('/materi-kelas/{id}', [MateriController::class, 'destroy'])->name('materi_kelas.destroy'); // Arham nambah
    Route::put('/materi-kelas/{id}', [MateriController::class, 'update'])->name('materi_kelas.update'); // Arham nambah
    Route::post('/materi-kelas/upload', [DosenKelasController::class, 'uploadMateriGlobal'])->name('materi_kelas.upload');

    // Kelola Kelas → views/dosen/kelola_kelas/
    Route::get('/kelas', [App\Http\Controllers\Dosen\KelasController::class, 'index'])->name('kelola_kelas.index');
    Route::get('/kelas/create', [App\Http\Controllers\Dosen\KelasController::class, 'create'])->name('kelola_kelas.create');
    Route::post('/kelas', [App\Http\Controllers\Dosen\KelasController::class, 'store'])->name('kelola_kelas.store');
    Route::get('/kelas/{id}/edit', [App\Http\Controllers\Dosen\KelasController::class, 'edit'])->name('kelola_kelas.edit');
    Route::put('/kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'update'])->name('kelola_kelas.update');
    Route::delete('/kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'destroy'])->name('kelola_kelas.destroy');
    Route::get('/kelas/{id}', [App\Http\Controllers\Dosen\KelasController::class, 'show'])->name('kelola_kelas.show');
    Route::get('/materi-kelas/{id}-{slug}', [DosenKelasController::class, 'detailMateri'])->name('materi_kelas.detail');

    // Komunikasi → views/dosen/komunikasi/komunikasi.blade.php
    Route::get('/komunikasi', [App\Http\Controllers\Dosen\KelasController::class, 'komunikasi'])->name('komunikasi');

    // Kelola isi kelas (materi & tugas per kelas)
    Route::get('/kelas/{id}/manage', [App\Http\Controllers\Dosen\KelasController::class, 'manage'])->name('kelola_kelas.manage');
    Route::post('/kelas/{id}/materi', [App\Http\Controllers\Dosen\KelasController::class, 'uploadMateri'])->name('kelola_kelas.upload_materi');

    // Pilih kelas untuk tugas/ujian
    Route::get('/tugas-ujian', [TugasController::class, 'pilihKelas'])->name('tugas_ujian.pilih_kelas');
    Route::get('/tugas-ujian/{kelas}', [TugasController::class, 'index'])->name('tugas_ujian.index');
    Route::post('/tugas-ujian/{kelas}', [TugasController::class, 'store'])->name('tugas_ujian.store');
    Route::get('/tugas-ujian/{kelas}/detail', [TugasController::class, 'detail'])->name('tugas_ujian.detail');
    Route::get('/tugas-ujian/{kelas}/{tugas}/edit', [TugasController::class, 'edit'])->name('tugas_ujian.edit');
    Route::put('/tugas-ujian/{kelas}/{tugas}', [TugasController::class, 'update'])->name('tugas_ujian.update');
    Route::delete('/tugas-ujian/{kelas}/{tugas}', [TugasController::class, 'destroy'])->name('tugas_ujian.destroy');
    Route::get('/tugas-ujian/{kelas}/{tugas}/penilaian', [TugasController::class, 'penilaian'])->name('tugas_ujian.penilaian');
    Route::post('/tugas-ujian/{kelas}/{tugas}/penilaian', [TugasController::class, 'nilaiTugas'])->name('tugas_ujian.nilai');
    Route::get('/tugas-ujian/{kelas}/{tugas}/mahasiswa', [TugasController::class, 'penilaianPerMahasiswa'])->name('tugas_ujian.mahasiswa');
    Route::get('/rekap-nilai', [App\Http\Controllers\Dosen\TugasController::class, 'rekapNilai'])->name('rekap_nilai.index');
    Route::get('/rekap-nilai/{kelas}', [App\Http\Controllers\Dosen\TugasController::class, 'rekapPerKelas'])->name('rekap_nilai.detail');
    Route::get('/rekap-nilai/export/{kelasId}', [App\Http\Controllers\Dosen\TugasController::class, 'exportRekap'])->name('rekap_nilai.export');

      // 🔧 Pengaturan Dosen
    Route::get('/pengaturan', [App\Http\Controllers\Dosen\ProfilDosenController::class, 'pengaturan'])->name('pengaturan');

    Route::get('/pengaturan/profil', [ProfilDosenController::class, 'editProfil'])->name('pengaturan.profil');
    Route::post('/pengaturan/profil', [ProfilDosenController::class, 'updateProfile'])->name('pengaturan.profil.update');

    Route::get('/pengaturan/password', [App\Http\Controllers\Dosen\ProfilDosenController::class, 'editPassword'])->name('pengaturan.password');
    Route::post('/pengaturan/password', [App\Http\Controllers\Dosen\ProfilDosenController::class, 'updatePassword'])->name('pengaturan.password.update');
});

// ==============================
// MAHASISWA ROUTES
// ==============================

    Route::middleware(['auth:mahasiswa', 'verified'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Kelas
    Route::get('/kelas', [MahasiswaMateriController::class, 'daftarKelasMateri'])->name('kelas.index');
    Route::get('/kelas/{id}', [JoinKelasController::class, 'show'])->name('kelas.show');
    Route::delete('/kelas/{id}', [JoinKelasController::class, 'leave'])->name('kelas.leave');

    // Join Kelas
    Route::get('/join', [JoinKelasController::class, 'index'])->name('join.index');
    Route::post('/join', [JoinKelasController::class, 'store'])->name('join.store');

    // Materi
    Route::get('/kelas/{kelas}/materi', [MahasiswaMateriController::class, 'index'])->name('materi.index');

    // Tugas
    Route::get('/kelas/{kelas}/tugas', [MahasiswaTugasController::class, 'index'])->name('kelas.tugas.index');
    Route::get('/kelas/{kelas}/tugas/{tugas}', [MahasiswaTugasController::class, 'show'])->name('kelas.tugas.show'); 
    Route::post('/kelas/{kelas}/tugas/{tugas}/upload', [MahasiswaTugasController::class, 'upload'])->name('kelas.tugas.upload');
    Route::get('/kelas/{kelas}/tugas/{tugas}/preview', [MahasiswaTugasController::class, 'preview'])->name('kelas.tugas.preview');
    Route::delete('/kelas/{kelas}/tugas/{tugas}/delete', [MahasiswaTugasController::class, 'delete'])->name('kelas.tugas.delete');
    Route::get('{kelasId}/{tugasId}/penilaian', [PenilaianController::class, 'show'])->name('penilaian');
    Route::post('{kelasId}/{tugasId}/penilaian', [PenilaianController::class, 'update'])->name('penilaian.update');

    // Ujian (CRUD Lengkap)
    Route::prefix('/kelas/{kelas}/ujian')->name('ujian.')->group(function () {
        Route::get('/', [UjianController::class, 'index'])->name('index');
        Route::get('/create', [UjianController::class, 'create'])->name('create');
        Route::post('/', [UjianController::class, 'store'])->name('store');
        Route::get('/{id}', [UjianController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UjianController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UjianController::class, 'update'])->name('update');
        Route::delete('/{id}', [UjianController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/kerjakan', [UjianController::class, 'kerjakan'])->name('kerjakan');
    });

    // Komunikasi
    Route::get('/komunikasi', [KomunikasiController::class, 'index'])->name('komunikasi.index');
    Route::get('/mahasiswa/komunikasi', [KomunikasiController::class, 'index'])->name('komunikasi.index');
    Route::post('/mahasiswa/komunikasi/post', [KomunikasiController::class, 'store'])->name('komunikasi.post');
    Route::post('/mahasiswa/komunikasi/reply/{id}', [KomunikasiController::class, 'reply'])->name('komunikasi.reply');

    

    // Pengaturan & Profil
    Route::get('/pengaturan', [MahasiswaPengaturanController::class, 'index'])->name('pengaturan.index');
    Route::get('/edit-profil', [MahasiswaPengaturanController::class, 'editProfile'])->name('profile-edit.edit');
    Route::post('/edit-profil', [MahasiswaPengaturanController::class, 'updateProfile'])->name('profile-edit.update');
    Route::get('/ganti-password', [MahasiswaPengaturanController::class, 'editPassword'])->name('password-edit.edit');
    Route::post('/ganti-password', [MahasiswaPengaturanController::class, 'updatePassword'])->name('password-edit.update');
});
