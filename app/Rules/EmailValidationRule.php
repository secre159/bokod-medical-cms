<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if value is empty (handle nullable separately)
        if (empty($value)) {
            return;
        }
        
        // Trim and convert to lowercase for validation
        $email = strtolower(trim($value));
        
        // Basic format validation using PHP's filter
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail('The :attribute field must be a valid email address (name@domain.com).');
            return;
        }
        
        // Enforce stricter email format: name@domain.com
        if (!preg_match('/^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9]+([.-][a-z0-9]+)*\.[a-z]{2,}$/i', $email)) {
            $fail('The :attribute field must follow the format name@domain.com (letters and numbers only, with optional dots, underscores, or hyphens).');
            return;
        }
        
        // Split email into local and domain parts
        list($local, $domain) = explode('@', $email, 2);
        
        // Local part (name) validation
        if (strlen($local) < 1) {
            $fail('The email name part (before @) cannot be empty.');
            return;
        }
        
        if (strlen($local) > 64) {
            $fail('The email name part (before @) cannot exceed 64 characters.');
            return;
        }
        
        // Local part must start and end with alphanumeric characters
        if (!preg_match('/^[a-z0-9]/', $local) || !preg_match('/[a-z0-9]$/', $local)) {
            $fail('The email name part must start and end with a letter or number.');
            return;
        }
        
        // Check for consecutive special characters
        if (preg_match('/[._-]{2,}/', $local)) {
            $fail('The email name part cannot contain consecutive dots, underscores, or hyphens.');
            return;
        }
        
        // Domain part validation
        if (strlen($domain) < 4) { // minimum: a.co
            $fail('The email domain is too short (minimum: domain.com).');
            return;
        }
        
        if (strlen($domain) > 255) {
            $fail('The email domain part (after @) cannot exceed 255 characters.');
            return;
        }
        
        // Domain must contain at least one dot
        if (!str_contains($domain, '.')) {
            $fail('The email domain must contain at least one dot (e.g., domain.com).');
            return;
        }
        
        // Get domain parts
        $domainParts = explode('.', $domain);
        
        // Check each domain part
        foreach ($domainParts as $part) {
            if (empty($part)) {
                $fail('The email domain cannot have empty parts (consecutive dots).');
                return;
            }
            
            if (!preg_match('/^[a-z0-9-]+$/i', $part)) {
                $fail('The email domain can only contain letters, numbers, and hyphens.');
                return;
            }
            
            if (str_starts_with($part, '-') || str_ends_with($part, '-')) {
                $fail('Domain parts cannot start or end with hyphens.');
                return;
            }
        }
        
        // Get TLD (top-level domain)
        $tld = end($domainParts);
        
        if (strlen($tld) < 2) {
            $fail('The email domain extension must be at least 2 characters long.');
            return;
        }
        
        // TLD must contain only letters
        if (!preg_match('/^[a-z]{2,}$/i', $tld)) {
            $fail('The email domain extension must contain only letters (e.g., .com, .org, .net).');
            return;
        }
        
        // Total email length check (RFC 5321 limit)
        if (strlen($email) > 320) {
            $fail('The email address is too long. Maximum length is 320 characters.');
            return;
        }
    }
}
