<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureMahasiswaEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('mahasiswa')->user();

        if (!$user || !$user->hasVerifiedEmail()) {
            return redirect()->route('mahasiswa.verification.notice');
        }

        return $next($request);
    }
}
