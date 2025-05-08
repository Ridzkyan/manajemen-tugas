<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        
        if (Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'admin'
        ])) {
            $user = Auth::guard('admin')->user();
            $user->is_online = true;
            $user->save();
        
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $user->is_online = false;
        $user->save();
    
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/admin');
    }
}
