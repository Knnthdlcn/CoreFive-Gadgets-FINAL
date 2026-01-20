<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $intended = (string) $request->query('intended', '');
        if ($intended !== '' && str_starts_with($intended, '/')) {
            $request->session()->put('url.intended', $intended);
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->with([
                // Always ask which Google account to use (no silent auto-login)
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    public function callback(Request $request)
    {
        if ($request->filled('error')) {
            $error = (string) $request->query('error');
            if ($error === 'access_denied') {
                return redirect()->route('home')->with('error', 'Google login was cancelled.');
            }

            return redirect()->route('home')->with('error', 'Google login failed: ' . $error);
        }

        try {
            $driver = Socialite::driver('google');
            // Local dev often hits state/session mismatch due to host/port/cookie quirks.
            if (app()->environment('local')) {
                $driver->stateless();
                // Windows local setups frequently lack a properly configured CA bundle for cURL.
                // Only in local env: disable TLS verification to unblock OAuth.
                $driver->setHttpClient(new Client(['verify' => false]));
            }

            $googleUser = $driver->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth callback failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'query' => $request->query(),
            ]);
            return redirect()->route('home')->with('error', 'Google login was cancelled or failed. Please try again.');
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect()->route('home')->with('error', 'Google did not return an email address.');
        }

        $name = trim((string) $googleUser->getName());
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $firstName = $parts[0] ?? 'User';
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        // Trust Google email as verified
        if (empty($user->email_verified_at)) {
            $user->email_verified_at = now();
        }

        if (Schema::hasColumn('users', 'google_id')) {
            $user->google_id = $googleUser->getId();
        }

        $user->save();

        if (!empty($user->banned_at)) {
            Auth::guard('web')->logout();
            Auth::guard('admin')->logout();
            return redirect()->route('account.disabled')
                ->with('error', 'Your account has been temporarily disabled (banned). Please contact Customer Service for help.');
        }

        // Allow admin + web sessions to coexist in the same browser.
        // We do not force-log-out the other guard here.
        if (($user->role ?? 'customer') === 'admin') {
            Auth::guard('admin')->login($user, true);
            return redirect()->intended(route('admin.dashboard'));
        }

        Auth::guard('web')->login($user, true);

        return redirect()->intended(route('home'));
    }
}
