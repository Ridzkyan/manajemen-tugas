<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    // =============================
    // GUARDS
    // =============================
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'dosen' => [
            'driver' => 'session',
            'provider' => 'dosens',
        ],

        'mahasiswa' => [
            'driver' => 'session',
            'provider' => 'mahasiswas',
        ],
    ],

    // =============================
    // PROVIDERS
    // =============================
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class, // ✅ disesuaikan
        ],

        'dosens' => [
            'driver' => 'eloquent',
            'model' => App\Models\Dosen::class, // ✅ disesuaikan
        ],

        'mahasiswas' => [
            'driver' => 'eloquent',
            'model' => App\Models\Mahasiswa::class, // ✅ disesuaikan
        ],
    ],

    // =============================
    // RESET PASSWORD
    // =============================
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
