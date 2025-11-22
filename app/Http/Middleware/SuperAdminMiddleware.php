<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Check if user is super admin
        if (!$user || !$user->is_super_admin) {
            abort(403, 'Unauthorized. Only Super Admin can access System Settings.');
        }
        
        // If no PIN is set yet, redirect to setup (first-time setup)
        if (!$user->settings_pin) {
            return redirect()->route('settings.pin.setup')
                ->with('info', 'Please set up your Settings PIN first for enhanced security.');
        }
        
        // Check if PIN verification is required and valid
        if ($user->settings_pin) {
            // If PIN is set, check if session is authorized
            if (!session()->has('settings_pin_verified') || 
                session('settings_pin_verified') !== true ||
                session('settings_pin_user_id') !== $user->id) {
                
                // Redirect to PIN verification page
                return redirect()->route('settings.verify-pin')
                    ->with('intended_url', $request->fullUrl());
            }
            
            // Check session timeout (30 minutes)
            if (session()->has('settings_pin_expires_at') && 
                now()->gt(session('settings_pin_expires_at'))) {
                
                // Session expired, clear and redirect
                session()->forget(['settings_pin_verified', 'settings_pin_user_id', 'settings_pin_expires_at']);
                
                return redirect()->route('settings.verify-pin')
                    ->with('intended_url', $request->fullUrl())
                    ->with('warning', 'Your settings access session has expired. Please verify again.');
            }
        }
        
        return $next($request);
    }
}
