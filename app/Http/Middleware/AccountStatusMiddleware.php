<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AccountStatusMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * This middleware ensures that only active and approved users can access the system.
     * It checks both account status and registration approval status.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check account status if user is authenticated and session is available
        if (!Auth::check() || !$request->hasSession()) {
            return $next($request);
        }
        
        $user = Auth::user();

        // Double-check user is still valid
        if (!$user) {
            return $next($request);
        }
        
        // Skip status checks for admin users performing admin operations
        // This prevents admins from being locked out while managing other users
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user account is active
        if (!$user->isActive()) {
            Auth::logout();
            
            $message = match($user->status) {
                User::STATUS_INACTIVE => 'Your account has been deactivated by an administrator. Please contact support for assistance.',
                User::STATUS_SUSPENDED => 'Your account has been suspended. Please contact support for more information.',
                default => 'Your account is not active. Please contact support for assistance.'
            };
            
            return redirect()->route('login')->withErrors([
                'email' => $message
            ]);
        }

        // Check registration approval status for patients
        if ($user->isPatient()) {
            if ($user->isRegistrationPending()) {
                Auth::logout();
                
                return redirect()->route('login')->withErrors([
                    'email' => 'Your registration is still pending approval. You will receive an email notification once your account is approved by an administrator.'
                ]);
            }
            
            if ($user->isRegistrationRejected()) {
                Auth::logout();
                
                $rejectionReason = $user->rejection_reason ? 
                    " Reason: {$user->rejection_reason}" : '';
                
                return redirect()->route('login')->withErrors([
                    'email' => "Your registration has been rejected.{$rejectionReason} Please contact support if you believe this is an error."
                ]);
            }
        }

        return $next($request);
    }
}