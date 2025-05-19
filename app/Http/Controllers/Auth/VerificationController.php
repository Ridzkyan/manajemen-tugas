<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use App\Models\User\Mahasiswa;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:30,1')->only('verify', 'resend');
        $this->middleware('auth:mahasiswa')->only('show', 'resend');
    }

    /**
     * Menampilkan halaman notifikasi verifikasi email.
     */
    public function show(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * Proses verifikasi ketika link di email diklik.
     */
    public function verify(Request $request)
    {
        $user = Mahasiswa::findOrFail($request->route('id'));

        if (! hash_equals(sha1($user->getEmailForVerification()), $request->route('hash'))) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user)); // penting untuk trigger event Laravel
        }

        Auth::guard('mahasiswa')->login($user);

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Email berhasil diverifikasi!');
    }

    /**
     * Kirim ulang link verifikasi.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Link verifikasi telah dikirim ke email Anda.');
    }

}
