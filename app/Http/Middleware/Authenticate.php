<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
protected function redirectTo($request)
{
    if (! $request->expectsJson()) {
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('admin.login');
        } elseif ($request->is('dosen') || $request->is('dosen/*')) {
            return route('login.dosen');
        } elseif ($request->is('mahasiswa') || $request->is('mahasiswa/*')) {
            return route('login.mahasiswa');
        }

        // fallback ke halaman utama
        return route('welcome');
    }
}

}