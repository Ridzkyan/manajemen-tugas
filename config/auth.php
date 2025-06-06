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
            'model' => App\Models\User\User::class, 
        ],

        'dosens' => [
            'driver' => 'eloquent',
            'model' => App\Models\User\Dosen::class, 
        ],

        'mahasiswas' => [
            'driver' => 'eloquent',
            'model' => App\Models\User\Mahasiswa::class,
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
