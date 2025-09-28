<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordChangeController extends Controller
{
    /**
     * Show the password change form
     */
    public function show()
    {
        return view('auth.change-password');
    }

    /**
     * Handle password change for authenticated users
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Please enter your current password.',
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password is incorrect.'],
            ]);
        }

        // Update password
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Log the password change
        \Log::info('Password changed successfully', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    /**
     * Show forgot password option for authenticated users
     */
    public function showForgotOption()
    {
        return view('auth.forgot-password-option');
    }

    /**
     * Handle forgot password request for authenticated users
     */
    public function requestReset(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|accepted'
        ], [
            'confirmation.required' => 'You must confirm that you want to reset your password.',
            'confirmation.accepted' => 'You must confirm that you want to reset your password.'
        ]);

        $user = Auth::user();

        // Log out the current user
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the actual forgot password page with email pre-filled
        return redirect()->route('password.request')
                        ->with('email', $user->email)
                        ->with('info', 'You have been logged out. Please enter your email to reset your password.');
    }
}