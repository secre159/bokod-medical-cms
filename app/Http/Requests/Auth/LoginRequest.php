<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // First, check if credentials are valid
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // Check account status - logout and show appropriate message if account is not active
        if (!$user->isActive()) {
            Auth::logout();
            
            $message = match($user->status) {
                User::STATUS_INACTIVE => 'Your account has been deactivated by an administrator. Please contact support for assistance.',
                User::STATUS_SUSPENDED => 'Your account has been suspended. Please contact support for more information.',
                default => 'Your account is not active. Please contact support for assistance.'
            };
            
            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        // Check registration approval status for patients
        if ($user->isPatient()) {
            if ($user->isRegistrationPending()) {
                Auth::logout();
                
                throw ValidationException::withMessages([
                    'email' => 'Your registration is still pending approval. You will receive an email notification once your account is approved by an administrator.',
                ]);
            }
            
            if ($user->isRegistrationRejected()) {
                Auth::logout();
                
                $rejectionReason = $user->rejection_reason ? 
                    " Reason: {$user->rejection_reason}" : '';
                
                throw ValidationException::withMessages([
                    'email' => "Your registration has been rejected.{$rejectionReason} Please contact support if you believe this is an error.",
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
        
        // Fire the Login event to trigger last login tracking
        event(new Login('web', Auth::user(), $this->boolean('remember')));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
