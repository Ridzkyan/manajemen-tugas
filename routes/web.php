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

Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Auth routes (tidak perlu middleware)
    Route::get('/login', [LoginMahasiswaController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginMahasiswaController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginMahasiswaController::class, 'logout'])->name('logout');
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// Override untuk backward compatibility
Route::get('/mahasiswa/login', [LoginMahasiswaController::class, 'showLoginForm'])->name('login.mahasiswa');
Route::get('/mahasiswa/register', [RegisterController::class, 'showRegistrationForm'])->name('register.mahasiswa');

// Verifikasi Email (di luar auth:mahasiswa)
Route::get('/mahasiswa/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('mahasiswa.email-verification.verify');

// Halaman notifikasi & kirim ulang verifikasi
Route::middleware(['auth:mahasiswa'])->prefix('mahasiswa/email')->name('mahasiswa.email-verification.')->group(function () {
    Route::get('/verify', [VerificationController::class, 'show'])->name('notice');
    Route::post('/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('resend');
});

// ==============================
// ADMIN ROUTES
// ==============================

Route::prefix('admin')->name('admin.')->group(function () {
    // Auth routes (tidak perlu middleware)
    Route::get('/', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pengaturan', fn () => view('admin.admin_pengaturan.pengaturan'))->name('pengaturan');

    // User Management
    Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users');
    Route::resource('users', UserController::class)->except(['show']);
    Route::put('/reset-password/{user}', [UserController::class, 'resetPassword'])
        ->where('user', '[0-9]+')
        ->name('users.reset-password');

    // Profile Management
    Route::get('/profil', [UserController::class, 'editProfile'])->name('profil.edit');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profil.update');
    Route::get('/ganti-password', [UserController::class, 'editPassword'])->name('password.edit');
    Route::put('/ganti-password', [UserController::class, 'updatePassword'])->name('password.update');

    // Content Management
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/konten', [KontenController::class, 'index'])->name('konten.index');

    // Content Approval
    Route::patch('/konten/materi/{id}/setujui', [KontenController::class, 'setujuiMateri'])
        ->where('id', '[0-9]+')
        ->name('konten.materi.setujui');
    Route::patch('/konten/materi/{id}/tolak', [KontenController::class, 'tolakMateri'])
        ->where('id', '[0-9]+')
        ->name('konten.materi.tolak');
    Route::patch('/konten/tugas/{id}/setujui', [KontenController::class, 'setujuiTugas'])
        ->where('id', '[0-9]+')
        ->name('konten.tugas.setujui');
    Route::patch('/konten/tugas/{id}/tolak', [KontenController::class, 'tolakTugas'])
        ->where('id', '[0-9]+')
        ->name('konten.tugas.tolak');

    // System Management
    Route::get('/pengaturan/data', [PengaturanController::class, 'data'])->name('pengaturan.data');
    Route::post('/backup', [PengaturanController::class, 'backup'])->name('backup');
    Route::post('/backup-zip', [PengaturanController::class, 'backupZip'])->name('backup.zip');
    Route::post('/restore', [PengaturanController::class, 'restore'])->name('restore');
    
    // Testing route (hanya untuk admin dan environment local/testing)
    Route::get('/test-flask-connection', [TugasController::class, 'testFlaskConnection'])
        ->middleware('env:local,testing')
        ->name('test.flask');
});

// ==============================
// DOSEN ROUTES
// ==============================

Route::prefix('dosen')->name('dosen.')->group(function () {
    // Auth routes (tidak perlu middleware)
    Route::get('/login', [LoginDosenController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginDosenController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginDosenController::class, 'logout'])->name('logout');
});

// Override untuk backward compatibility
Route::get('/dosen/login', [LoginDosenController::class, 'showLoginForm'])->name('login.dosen');

Route::middleware(['auth:dosen', 'prevent-back-history'])->prefix('dosen')->name('dosen.')->group(function () {
    // Dashboard & Search
    Route::get('/dashboard', [DosenDashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Materi & Kelas Global
    Route::get('/materi-kelas', [DosenKelasController::class, 'materiDanKelas'])->name('materi_kelas.index');
    Route::get('/materi-kelas/{id}-{slug}', [DosenKelasController::class, 'detailMateri'])
        ->where(['id' => '[0-9]+', 'slug' => '[a-z0-9\-]+'])
        ->name('materi_kelas.detail');
    Route::delete('/materi-kelas/{id}', [MateriController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('materi_kelas.destroy');
    Route::put('/materi-kelas/{id}', [MateriController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('materi_kelas.update');
    Route::post('/materi-kelas/upload', [DosenKelasController::class, 'uploadMateriGlobal'])->name('materi_kelas.upload');

    // Kelola Kelas - menggunakan resource route
    Route::resource('kelas', DosenKelasController::class)->names([
        'index' => 'kelola_kelas.index',
        'create' => 'kelola_kelas.create',
        'store' => 'kelola_kelas.store',
        'show' => 'kelola_kelas.show',
        'edit' => 'kelola_kelas.edit',
        'update' => 'kelola_kelas.update',
        'destroy' => 'kelola_kelas.destroy'
    ])->where(['kelas' => '[0-9]+']);

    // Kelola isi kelas (materi & tugas per kelas)
    Route::get('/kelas/{id}/manage', [DosenKelasController::class, 'manage'])
        ->where('id', '[0-9]+')
        ->name('kelola_kelas.manage');
    Route::post('/kelas/{id}/materi', [DosenKelasController::class, 'uploadMateri'])
        ->where('id', '[0-9]+')
        ->name('kelola_kelas.upload_materi');

    // Komunikasi
    Route::get('/komunikasi', [DosenKelasController::class, 'komunikasi'])->name('komunikasi');

    // Tugas & Ujian
    Route::get('/tugas-ujian', [TugasController::class, 'pilihKelas'])->name('tugas_ujian.pilih_kelas');
    Route::get('/tugas-ujian/{kelas}', [TugasController::class, 'index'])
        ->where('kelas', '[0-9]+')
        ->name('tugas_ujian.index');
    Route::post('/tugas-ujian/{kelas}', [TugasController::class, 'store'])
        ->where('kelas', '[0-9]+')
        ->name('tugas_ujian.store');
    Route::get('/tugas-ujian/{kelas}/detail', [TugasController::class, 'detail'])
        ->where('kelas', '[0-9]+')
        ->name('tugas_ujian.detail');
    Route::get('/tugas-ujian/{kelas}/{tugas}/edit', [TugasController::class, 'edit'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('tugas_ujian.edit');
    Route::put('/tugas-ujian/{kelas}/{tugas}', [TugasController::class, 'update'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('tugas_ujian.update');
    Route::delete('/tugas-ujian/{kelas}/{tugas}', [TugasController::class, 'destroy'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('tugas_ujian.destroy');

    // Penilaian - FIXED: Pisahkan GET dan POST
    Route::get('/tugas-ujian/{kelas}/{tugas}/penilaian', [TugasController::class, 'penilaian'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('tugas_ujian.penilaian');
    Route::post('/tugas-ujian/{kelas}/{tugas}/penilaian', [TugasController::class, 'nilaiTugas'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('tugas_ujian.nilai');
    Route::post('/tugas-ujian/{kelas}/{tugas}/penilaian/batch', [TugasController::class, 'nilaiBatch'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('tugas_ujian.nilai.batch');
    Route::get('/tugas-ujian/{kelas}/{tugas}/mahasiswa', [TugasController::class, 'penilaianPerMahasiswa'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('tugas_ujian.mahasiswa');

    // Rekap Nilai
    Route::get('/rekap-nilai', [DosenTugasController::class, 'rekapNilai'])->name('rekap_nilai.index');
    Route::get('/rekap-nilai/{kelas}', [DosenTugasController::class, 'rekapPerKelas'])
        ->where('kelas', '[0-9]+')
        ->name('rekap_nilai.detail');
    Route::get('/rekap-nilai/export/{kelasId}', [DosenTugasController::class, 'exportRekap'])
        ->where('kelasId', '[0-9]+')
        ->name('rekap_nilai.export');

    // Pengaturan Dosen
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', [ProfilDosenController::class, 'pengaturan'])->name('index');
        
        Route::get('/profil', [ProfilDosenController::class, 'editProfil'])->name('profil');
        Route::post('/profil', [ProfilDosenController::class, 'updateProfile'])->name('profil.update');
        
        Route::get('/password', [ProfilDosenController::class, 'editPassword'])->name('password');
        Route::post('/password', [ProfilDosenController::class, 'updatePassword'])->name('password.update');
    });
});

// ==============================
// MAHASISWA ROUTES (AUTHENTICATED)
// ==============================

Route::middleware(['auth:mahasiswa', 'verified'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Kelas Management
    Route::get('/kelas', [MahasiswaMateriController::class, 'daftarKelasMateri'])->name('kelas.index');
    Route::get('/kelas/{id}', [JoinKelasController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('kelas.show');
    Route::delete('/kelas/{id}', [JoinKelasController::class, 'leave'])
        ->where('id', '[0-9]+')
        ->name('kelas.leave');

    // Join Kelas
    Route::get('/join', [JoinKelasController::class, 'index'])->name('join.index');
    Route::post('/join', [JoinKelasController::class, 'store'])->name('join.store');

    // Materi
    Route::get('/kelas/{kelas}/materi', [MahasiswaMateriController::class, 'index'])
        ->where('kelas', '[0-9]+')
        ->name('materi.index');

    // Tugas
    Route::get('/kelas/{kelas}/tugas', [MahasiswaTugasController::class, 'index'])
        ->where('kelas', '[0-9]+')
        ->name('kelas.tugas.index');
    Route::get('/kelas/{kelas}/tugas/{tugas}', [MahasiswaTugasController::class, 'show'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('kelas.tugas.show');
    Route::post('/kelas/{kelas}/tugas/{tugas}/upload', [MahasiswaTugasController::class, 'upload'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('kelas.tugas.upload');
    Route::get('/kelas/{kelas}/tugas/{tugas}/preview', [MahasiswaTugasController::class, 'preview'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('kelas.tugas.preview');
    Route::delete('/kelas/{kelas}/tugas/{tugas}/delete', [MahasiswaTugasController::class, 'delete'])
        ->where(['kelas' => '[0-9]+', 'tugas' => '[0-9]+'])
        ->name('kelas.tugas.delete');

    // Penilaian
    Route::get('/{kelasId}/{tugasId}/penilaian', [PenilaianController::class, 'show'])
        ->where(['kelasId' => '[0-9]+', 'tugasId' => '[0-9]+'])
        ->name('penilaian');
    Route::post('/{kelasId}/{tugasId}/penilaian', [PenilaianController::class, 'update'])
        ->where(['kelasId' => '[0-9]+', 'tugasId' => '[0-9]+'])
        ->name('penilaian.update');

    // Komunikasi - FIXED: Konsisten dengan prefix
    Route::get('/komunikasi', [KomunikasiController::class, 'index'])->name('komunikasi.index');
    Route::post('/komunikasi/post', [KomunikasiController::class, 'store'])->name('komunikasi.post');
    Route::post('/komunikasi/reply/{id}', [KomunikasiController::class, 'reply'])
        ->where('id', '[0-9]+')
        ->name('komunikasi.reply');

    // Pengaturan & Profil
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', [MahasiswaPengaturanController::class, 'index'])->name('index');
        
        Route::get('/edit-profil', [MahasiswaPengaturanController::class, 'editProfile'])->name('profile.edit');
        Route::post('/edit-profil', [MahasiswaPengaturanController::class, 'updateProfile'])->name('profile.update');
        
        Route::get('/ganti-password', [MahasiswaPengaturanController::class, 'editPassword'])->name('password.edit');
        Route::post('/ganti-password', [MahasiswaPengaturanController::class, 'updatePassword'])->name('password.update');
    });
});

// ==============================
// BACKWARD COMPATIBILITY ROUTES
// ==============================

// Redirect old routes untuk backward compatibility
Route::get('/testFlaskConnection', function() {
    return redirect()->route('admin.test.flask');
})->middleware('auth:admin');