<?php

namespace App\Services;

use App\Mail\EmailVerificationOtpMail;
use App\Models\EmailVerificationOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailVerificationOtpService
{
    public const OTP_EXPIRES_MINUTES = 10;
    public const OTP_MAX_ATTEMPTS = 8;
    public const RESEND_COOLDOWN_SECONDS = 30;

    /**
     * Send (or re-send) an email verification OTP.
     * Returns true if an email send was attempted successfully.
     */
    public function send(User $user, bool $force = false): bool
    {
        if (method_exists($user, 'hasVerifiedEmail') && $user->hasVerifiedEmail()) {
            return true;
        }

        if (config('mail.default') === 'log') {
            return false;
        }

        $otp = EmailVerificationOtp::query()
            ->where('email', $user->email)
            ->whereNull('used_at')
            ->orderByDesc('id')
            ->first();

        if ($otp && !$force && $otp->last_sent_at && $otp->last_sent_at->diffInSeconds(now()) < self::RESEND_COOLDOWN_SECONDS) {
            return true;
        }

        $code = (string) random_int(100000, 999999);

        if (!$otp) {
            $otp = new EmailVerificationOtp();
            $otp->email = $user->email;
            $otp->attempts = 0;
        }

        $otp->code_hash = Hash::make($code);
        $otp->expires_at = now()->addMinutes(self::OTP_EXPIRES_MINUTES);
        $otp->last_sent_at = now();
        $otp->used_at = null;
        $otp->attempts = 0;
        $otp->save();

        try {
            Mail::to($user->email)->send(new EmailVerificationOtpMail($code, self::OTP_EXPIRES_MINUTES));
            Log::info('Email verification OTP sent', ['to' => $user->email]);
            return true;
        } catch (\Throwable $e) {
            Log::warning('Email verification OTP send failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'to' => $user->email,
            ]);
            // If mailer fails, also write a short file to storage/logs for quick inspection
            try {
                \file_put_contents(storage_path('logs/mail-send-fail.log'), now()->toDateTimeString() . " - {$user->email} - " . $e->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
            } catch (\Throwable $__) {
                // ignore failures writing the debug file
            }
            return false;
        }
    }
}
