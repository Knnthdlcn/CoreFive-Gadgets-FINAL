<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Services\EmailVerificationOtpService;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    private function sendEmailVerificationOtp(User $user, bool $force = false): bool
    {
        try {
            return app(EmailVerificationOtpService::class)->send($user, $force);
        } catch (\Throwable $e) {
            Log::warning('Email verification OTP service failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'email' => $user->email,
            ]);
            return false;
        }
    }

    private function formatPhilippinesAddress(array $data): ?string
    {
        $street = trim((string) ($data['address_street'] ?? ''));
        $postal = trim((string) ($data['address_postal_code'] ?? ''));

        $regionCode = $data['address_region_code'] ?? null;
        $provinceCode = $data['address_province_code'] ?? null;
        $cityCode = $data['address_city_code'] ?? null;
        $barangayCode = $data['address_barangay_code'] ?? null;

        if (!$street && !$postal && !$regionCode && !$provinceCode && !$cityCode && !$barangayCode) {
            return null;
        }

        $region = $regionCode
            ? DB::table('philippine_regions')->where('region_code', $regionCode)->value('name')
            : null;
        $province = $provinceCode
            ? DB::table('philippine_provinces')->where('province_code', $provinceCode)->value('name')
            : null;
        $city = $cityCode
            ? DB::table('philippine_cities')->where('city_code', $cityCode)->value('name')
            : null;
        $barangay = $barangayCode
            ? DB::table('philippine_barangays')->where('psgc_code', $barangayCode)->value('name')
            : null;

        $parts = array_values(array_filter([
            $street ?: null,
            $barangay ?: null,
            $city ?: null,
            $province ?: null,
            $region ?: null,
        ]));

        $address = implode(', ', $parts);
        if ($postal !== '') {
            $address .= ($address ? ' ' : '') . $postal;
        }

        return $address ?: null;
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }

        // Admin accounts must log in via the admin portal only.
        if (($user->role ?? 'customer') === 'admin') {
            return response()->json([
                'error' => 'This account can only log in via the admin portal.',
                'redirect_url' => route('admin.login'),
            ], 403);
        }

        // Banned (archived) accounts cannot log in.
        if (!empty($user->banned_at)) {
            return response()->json([
                'error' => 'Your account has been temporarily disabled (banned). Please contact Customer Service for help.',
                'banned' => true,
                'redirect_url' => route('account.disabled'),
            ], 403);
        }

        Auth::login($user, $request->has('remember'));

        $user->update(['last_login_at' => now()]);

        if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            // Send OTP code (best-effort). The verification page allows resending.
            $this->sendEmailVerificationOtp($user);

            return response()->json([
                'user' => $user,
                'verification_required' => true,
                'redirect_url' => route('verification.notice'),
                'message' => 'Please verify your email address to continue.',
            ]);
        }

        return response()->json([
            'user' => $user,
            'token' => base64_encode(random_bytes(24)),
            'redirect_url' => route('home'),
        ]);
    }

    public function signup(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'firstName' => 'required|string|min:2',
            'lastName' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'contact' => ['nullable', 'regex:/^09\d{9}$/'],
            'address' => 'nullable|string',
            'address_region_code' => 'nullable|string',
            'address_province_code' => 'nullable|string',
            'address_city_code' => 'nullable|string',
            'address_barangay_code' => 'nullable|string',
            'address_street' => 'nullable|string|max:255',
            'address_postal_code' => 'nullable|string|max:16',
        ]);

        $formattedAddress = $this->formatPhilippinesAddress($validated);
        if (!$formattedAddress) {
            $formattedAddress = $validated['address'] ?? null;
        }

        $user = User::create([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'contact' => $validated['contact'] ?? null,
            'address' => $formattedAddress,
            'address_region_code' => $validated['address_region_code'] ?? null,
            'address_province_code' => $validated['address_province_code'] ?? null,
            'address_city_code' => $validated['address_city_code'] ?? null,
            'address_barangay_code' => $validated['address_barangay_code'] ?? null,
            'address_street' => $validated['address_street'] ?? null,
            'address_postal_code' => $validated['address_postal_code'] ?? null,
            'role' => 'customer',
        ]);

        Auth::login($user);

        $otpSent = $this->sendEmailVerificationOtp($user, true);

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'verification_required' => true,
            'redirect_url' => route('verification.notice'),
            'message' => $otpSent
                ? 'Account created. We sent a 6-digit verification code to your email.'
                : 'Account created. Please resend the verification code on the Verify Email page.',
        ], 201);
    }

    public function logout(Request $request)
    {
        // Only log out the web user. Do NOT invalidate the whole session,
        // otherwise an active admin session in the same browser is also destroyed.
        Auth::guard('web')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }

    public function profile()
    {
        return view('profile');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|min:2',
            'last_name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'contact' => ['nullable', 'regex:/^09\d{9}$/'],
            'address' => 'nullable|string',
            'address_region_code' => 'nullable|string',
            'address_province_code' => 'nullable|string',
            'address_city_code' => 'nullable|string',
            'address_barangay_code' => 'nullable|string',
            'address_street' => 'nullable|string|max:255',
            'address_postal_code' => 'nullable|string|max:16',
        ]);

        $user = Auth::user();
        $emailChanged = $validated['email'] !== $user->email;

        $formattedAddress = $this->formatPhilippinesAddress($validated);
        if (!$formattedAddress) {
            $formattedAddress = $validated['address'] ?? null;
        }

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'contact' => $validated['contact'] ?? null,
            'address' => $formattedAddress,
            'address_region_code' => $validated['address_region_code'] ?? null,
            'address_province_code' => $validated['address_province_code'] ?? null,
            'address_city_code' => $validated['address_city_code'] ?? null,
            'address_barangay_code' => $validated['address_barangay_code'] ?? null,
            'address_street' => $validated['address_street'] ?? null,
            'address_postal_code' => $validated['address_postal_code'] ?? null,
        ];

        if ($emailChanged && method_exists($user, 'hasVerifiedEmail')) {
            $updateData['email_verified_at'] = null;
        }

        $user->update($updateData);

        if ($emailChanged && method_exists($user, 'sendEmailVerificationNotification')) {
            try {
                $user->sendEmailVerificationNotification();
                Log::info('Verification email sent (profile email change)', [
                    'to' => $user->email,
                    'mailer' => (string) config('mail.default'),
                    'from' => config('mail.from.address'),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Verification email send failed (profile email change)', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'email' => $user->email,
                    'mailer' => (string) config('mail.default'),
                    'from' => config('mail.from.address'),
                ]);
            }

            return redirect()
                ->route('verification.notice')
                ->with('success', 'Profile updated. Please verify your new email address.');
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('profile')->with('success', 'Password updated successfully!');
    }

    public function updatePhoto(Request $request)
    {
        try {
            $validated = $request->validate([
                // max is in kilobytes
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            // Ensure target directory exists
            Storage::disk('public')->makeDirectory('profile_photos');

            // Delete old photo if exists
            if (Auth::user()->profile_photo) {
                Storage::disk('public')->delete(Auth::user()->profile_photo);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile_photos', 'public');

            Auth::user()->update([
                'profile_photo' => $path
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'profile_photo' => $path,
                    'profile_photo_url' => asset('storage/' . $path),
                    'message' => 'Profile photo updated successfully!',
                ]);
            }

            return redirect()->route('profile')->with('success', 'Profile photo updated successfully!');
        } catch (\Throwable $e) {
            Log::warning('Profile photo upload failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The profile photo failed to upload.',
                ], 422);
            }

            return redirect()->route('profile')->withErrors([
                'profile_photo' => 'The profile photo failed to upload.',
            ]);
        }
    }
}
