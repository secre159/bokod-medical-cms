<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * This middleware checks both role authorization and account status.
     * Users must have the correct role AND be active/approved to access resources.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check account status first
        if (!$user->isActive()) {
            $message = match($user->status) {
                \App\Models\User::STATUS_INACTIVE => 'Your account has been deactivated by an administrator.',
                \App\Models\User::STATUS_SUSPENDED => 'Your account has been suspended.',
                default => 'Your account is not active.'
            };
            
            abort(403, $message . ' Please contact support for assistance.');
        }

        // Check registration approval for patients
        if ($user->isPatient() && !$user->isRegistrationApproved()) {
            if ($user->isRegistrationPending()) {
                abort(403, 'Your registration is still pending approval. Please wait for admin approval.');
            } elseif ($user->isRegistrationRejected()) {
                $rejectionReason = $user->rejection_reason ? " Reason: {$user->rejection_reason}" : '';
                abort(403, "Your registration has been rejected.{$rejectionReason} Please contact support.");
            }
        }

        // Check role authorization
        if ($user->role !== $role) {
            abort(403, 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}
