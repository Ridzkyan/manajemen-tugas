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
        // contoh: redirect berdasarkan guard
        if (Auth::guard('dosen')->check()) {
            return route('dosen.login');
        } elseif (Auth::guard('mahasiswa')->check()) {
            return route('mahasiswa.login');
        }

        // default fallback
        return route('mahasiswa.login');
    }
}
}