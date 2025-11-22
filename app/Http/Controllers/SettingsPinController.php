<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SettingsPinController extends Controller
{
    /**
     * Show PIN setup page
     */
    public function showSetup()
    {
        $user = auth()->user();
        
        // Only super admin can access
        if (!$user->is_super_admin) {
            abort(403, 'Unauthorized');
        }
        
        return view('settings.pin-setup', [
            'has_pin' => !empty($user->settings_pin)
        ]);
    }
    
    /**
     * Save or update PIN
     */
    public function savePin(Request $request)
    {
        $user = auth()->user();
        
        // Only super admin can set PIN
        if (!$user->is_super_admin) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'pin' => 'required|digits_between:4,6|confirmed',
            'pin_confirmation' => 'required',
            'current_pin' => $user->settings_pin ? 'required' : 'nullable'
        ]);
        
        // If user already has a PIN, verify current PIN first
        if ($user->settings_pin) {
            if (!Hash::check($request->current_pin, $user->settings_pin)) {
                return back()->withErrors(['current_pin' => 'Current PIN is incorrect'])->withInput();
            }
        }
        
        // Save new PIN
        $user->settings_pin = Hash::make($request->pin);
        $user->save();
        
        // Clear any existing session
        session()->forget(['settings_pin_verified', 'settings_pin_user_id', 'settings_pin_expires_at']);
        
        Log::info('Settings PIN updated', ['user_id' => $user->id, 'email' => $user->email]);
        
        return redirect()->route('settings.pin.setup')
            ->with('success', 'Settings PIN has been ' . ($request->current_pin ? 'updated' : 'created') . ' successfully!');
    }
    
    /**
     * Show PIN verification page
     */
    public function showVerify()
    {
        $user = auth()->user();
        
        // Only super admin should see this
        if (!$user->is_super_admin) {
            abort(403, 'Unauthorized');
        }
        
        // If no PIN is set, redirect to setup
        if (!$user->settings_pin) {
            return redirect()->route('settings.pin.setup')
                ->with('info', 'Please set up your Settings PIN first.');
        }
        
        return view('settings.pin-verify');
    }
    
    /**
     * Verify PIN and grant access
     */
    public function verifyPin(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->is_super_admin) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'pin' => 'required|digits_between:4,6'
        ]);
        
        // Check if PIN matches
        if (!Hash::check($request->pin, $user->settings_pin)) {
            Log::warning('Failed settings PIN attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);
            
            return back()->withErrors(['pin' => 'Incorrect PIN. Please try again.'])->withInput();
        }
        
        // Grant access for 30 minutes
        session([
            'settings_pin_verified' => true,
            'settings_pin_user_id' => $user->id,
            'settings_pin_expires_at' => now()->addMinutes(30)
        ]);
        
        Log::info('Settings access granted', [
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => now()->addMinutes(30)
        ]);
        
        // Redirect to intended URL or settings index
        $intendedUrl = session('intended_url', route('settings.index'));
        session()->forget('intended_url');
        
        return redirect($intendedUrl)->with('success', 'Access granted! Your session is valid for 30 minutes.');
    }
    
    /**
     * Lock settings (clear PIN session)
     */
    public function lock()
    {
        session()->forget(['settings_pin_verified', 'settings_pin_user_id', 'settings_pin_expires_at']);
        
        Log::info('Settings manually locked', [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email
        ]);
        
        return redirect()->route('dashboard.index')->with('success', 'Settings access has been locked.');
    }
    
    /**
     * Remove PIN (emergency access)
     */
    public function removePin(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->is_super_admin) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'password' => 'required'
        ]);
        
        // Verify user password for security
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password'])->withInput();
        }
        
        $user->settings_pin = null;
        $user->save();
        
        // Clear session
        session()->forget(['settings_pin_verified', 'settings_pin_user_id', 'settings_pin_expires_at']);
        
        Log::warning('Settings PIN removed', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        return redirect()->route('settings.pin.setup')
            ->with('success', 'Settings PIN has been removed. You can now access settings without a PIN.');
    }
}
