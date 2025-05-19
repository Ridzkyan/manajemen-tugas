<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Dosen Pertama
        User::create([
            'name' => 'Dosen',
            'username' => 'dosen',
            'email' => 'dosen@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'kode_unik' => 'DSN-' . rand(10000, 99999),
        ]);

        // Mahasiswa
        User::create([
            'name' => 'Mahasiswa',
            'username' => 'mahasiswa',
            'email' => 'mahasiswa@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);
    }
}
