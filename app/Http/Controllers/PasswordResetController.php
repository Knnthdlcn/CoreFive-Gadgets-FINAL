<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Services\EmailVerificationOtpService;

class PasswordResetController extends Controller
{
    private const OTP_EXPIRES_MINUTES = 10;
    private const OTP_MAX_ATTEMPTS = 8;

    public function request()
    {
        return view('auth.forgot-password');
    }

    public function requestOtp()
    {
        // Keep this route as an alias to the main Forgot Password page.
        return redirect()->route('password.request');
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => "We can't find a user with that email address."]);
        }

        // Require email verification before password reset.
        if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            app(EmailVerificationOtpService::class)->send($user, true);

            // IMPORTANT: user is not logged in during password reset.
            // Redirect them to a guest OTP page where they can actually enter the code.
            return redirect()->route('verification.guest.notice', [
                'email' => $user->email,
                'next' => 'password-reset',
            ])->with('status', 'verification-code-sent');
        }

        // If the mailer is set to "log" (common in local dev), Laravel will not send real emails.
        // Fail fast with an actionable message so users don't think an email was delivered.
        if (config('mail.default') === 'log') {
            return back()->withErrors([
                'email' => 'Email sending is not configured (MAIL_MAILER=log). Configure Gmail SMTP in your .env to receive the OTP in your inbox.',
            ]);
        }

        $code = (string) random_int(100000, 999999);

        // Upsert a single active OTP record per email
        $otp = PasswordResetOtp::query()
            ->where('email', $user->email)
            ->whereNull('used_at')
            ->first();

        if (!$otp) {
            $otp = new PasswordResetOtp();
            $otp->email = $user->email;
            $otp->attempts = 0;
        }

        $otp->code_hash = Hash::make($code);
        $otp->expires_at = now()->addMinutes(self::OTP_EXPIRES_MINUTES);
        $otp->last_sent_at = now();
        $otp->used_at = null;
        $otp->save();

        try {
            Mail::to($user->email)->send(new PasswordResetOtpMail($code, self::OTP_EXPIRES_MINUTES));
            Log::info('Email OTP sent', [
                'to' => $user->email,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Email OTP send failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'to' => $user->email,
            ]);

            $detail = app()->environment('local') ? (' (' . $e->getMessage() . ')') : '';
            return back()->withErrors([
                'email' => 'Unable to send OTP email right now. Please check your mail settings and try again.' . $detail,
            ]);
        }

        return redirect()->route('password.otp.reset', ['email' => $user->email])
            ->with('status', 'OTP sent. Please check your email (Spam/Promotions too).');
    }

    public function otpResetForm(Request $request)
    {
        return view('auth.reset-password-otp', [
            'email' => $request->query('email'),
        ]);
    }

    public function otpUpdate(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => ['required', 'digits:6'],
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => "We can't find a user with that email address."]);
        }

        $otp = PasswordResetOtp::query()
            ->where('email', $user->email)
            ->whereNull('used_at')
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'No OTP request found. Please request a new code.']);
        }

        if ($otp->expires_at->isPast()) {
            return back()->withErrors(['code' => 'This code has expired. Please request a new one.']);
        }

        if ($otp->attempts >= self::OTP_MAX_ATTEMPTS) {
            return back()->withErrors(['code' => 'Too many attempts. Please request a new code.']);
        }

        $otp->attempts = (int) $otp->attempts + 1;
        $otp->save();

        if (!Hash::check($validated['code'], $otp->code_hash)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user->forceFill([
            'password' => bcrypt($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        $otp->used_at = now();
        $otp->save();

        event(new PasswordReset($user));

        return redirect()->route('home')->with('status', 'Password updated successfully. You can now log in.');
    }

    public function email(Request $request)
    {
        // OTP-only password reset flow (no reset-link)
        return $this->sendOtp($request);
    }

    public function resetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('home')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
