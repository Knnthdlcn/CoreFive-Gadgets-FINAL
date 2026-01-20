<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showLoginForm()
    {
        // If already logged in as admin, redirect to dashboard
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }
    
    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        // Attempt to log in with admin guard
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Check if the logged-in user is actually an admin
            if (auth('admin')->user()->role !== 'admin') {
                Auth::guard('admin')->logout();
                
                throw ValidationException::withMessages([
                    'email' => 'These credentials do not match our records or you do not have admin privileges.',
                ]);
            }
            
            return redirect()->intended(route('admin.dashboard'));
        }
        
        throw ValidationException::withMessages([
            'email' => 'These credentials do not match our records.',
        ]);
    }
    
    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Do NOT invalidate the whole session, otherwise the web guard session
        // in the same browser will also be destroyed.
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully');
    }
}
