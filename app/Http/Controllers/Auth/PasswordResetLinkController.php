<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     * 
     * Only active and approved users can reset their passwords.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Check if user exists and validate account status
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            // Check if account is active
            if (!$user->isActive()) {
                $message = match($user->status) {
                    User::STATUS_INACTIVE => 'Your account has been deactivated. Please contact support for assistance.',
                    User::STATUS_SUSPENDED => 'Your account has been suspended. Please contact support for more information.',
                    default => 'Your account is not active. Please contact support for assistance.'
                };
                
                return back()->withInput($request->only('email'))
                            ->withErrors(['email' => $message]);
            }
            
            // Check registration approval for patients
            if ($user->isPatient()) {
                if ($user->isRegistrationPending()) {
                    return back()->withInput($request->only('email'))
                                ->withErrors(['email' => 'Your registration is still pending approval. Password reset is not available until your account is approved.']);
                }
                
                if ($user->isRegistrationRejected()) {
                    $rejectionReason = $user->rejection_reason ? " Reason: {$user->rejection_reason}" : '';
                    return back()->withInput($request->only('email'))
                                ->withErrors(['email' => "Your registration has been rejected.{$rejectionReason} Please contact support."]);
                }
            }
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
