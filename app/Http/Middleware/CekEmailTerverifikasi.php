<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekEmailTerverifikasi
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('mahasiswa')->user();
    
        // Bypass route tertentu agar tidak terjebak di redirect loop
        if ($request->routeIs('verification.notice', 'verification.send', 'verification.verify')) {
            return $next($request);
        }
    
        if ($user && !$user->hasVerifiedEmail() && $request->route()->getName() !== 'verification.notice') {
            return redirect()->route('verification.notice')->with('error', 'Verifikasi email dulu ya!');
        }
    
        return $next($request);
    }
}
