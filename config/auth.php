<?php

return [

    'defaults' => [
        'guard' => 'web', // default bisa tetap web, tapi akan kita override berdasarkan role
        'passwords' => 'users',
    ],

    // =============================
    // GUARDS
    // =============================
    'guards' => [
        'web' => [ // default Laravel login
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
        'users' => [ // dipakai oleh admin
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'dosens' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class, 
        ],

        'mahasiswas' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class, 
        ],
    ],

    // =============================
    // RESET PASSWORD (boleh tetap satu saja)
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
