<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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

        Auth::login($user, $request->has('remember'));

        $user->update(['last_login_at' => now()]);

        return response()->json([
            'user' => $user,
            'token' => base64_encode(random_bytes(24)),
        ]);
    }

    public function signup(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'firstName' => 'required|string|min:2',
            'lastName' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'contact' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'contact' => $validated['contact'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'customer',
        ]);

        Auth::login($user);

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
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
        ]);

        Auth::user()->update($validated);

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
        $validated = $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old photo if exists
        if (Auth::user()->profile_photo) {
            $oldPhotoPath = storage_path('app/public/' . Auth::user()->profile_photo);
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile_photos', 'public');

        Auth::user()->update([
            'profile_photo' => $path
        ]);

        return redirect()->route('profile')->with('success', 'Profile photo updated successfully!');
    }
}
