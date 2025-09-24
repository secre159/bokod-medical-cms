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
        
        // Basic format validation
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('The :attribute field must be a valid email address.');
            return;
        }
        
        // Additional checks for email format
        $email = strtolower(trim($value));
        
        // Check for basic structure
        if (!preg_match('/^[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,}$/i', $email)) {
            $fail('The :attribute field must be a valid email format.');
            return;
        }
        
        // Split email into local and domain parts
        list($local, $domain) = explode('@', $email, 2);
        
        // Local part validation
        if (strlen($local) > 64) {
            $fail('The email local part (before @) cannot exceed 64 characters.');
            return;
        }
        
        if (strlen($local) < 1) {
            $fail('The email local part (before @) cannot be empty.');
            return;
        }
        
        // Domain part validation
        if (strlen($domain) > 255) {
            $fail('The email domain part (after @) cannot exceed 255 characters.');
            return;
        }
        
        if (strlen($domain) < 4) { // minimum: a.co
            $fail('The email domain is too short.');
            return;
        }
        
        // Check for valid characters in local part
        if (!preg_match('/^[a-z0-9._-]+$/i', $local)) {
            $fail('The email address contains invalid characters. Only letters, numbers, dots, hyphens, and underscores are allowed.');
            return;
        }
        
        // Check that local part doesn't start or end with dots
        if (str_starts_with($local, '.') || str_ends_with($local, '.')) {
            $fail('The email address cannot start or end with a dot.');
            return;
        }
        
        // Check for consecutive dots
        if (str_contains($local, '..')) {
            $fail('The email address cannot contain consecutive dots.');
            return;
        }
        
        // Domain validation
        if (!preg_match('/^[a-z0-9.-]+$/i', $domain)) {
            $fail('The email domain contains invalid characters.');
            return;
        }
        
        // Check domain has at least one dot and valid TLD
        if (!str_contains($domain, '.')) {
            $fail('The email domain must contain at least one dot.');
            return;
        }
        
        // Get TLD (top-level domain)
        $domainParts = explode('.', $domain);
        $tld = end($domainParts);
        
        if (strlen($tld) < 2) {
            $fail('The email domain extension is too short.');
            return;
        }
        
        // Check for valid TLD format (only letters)
        if (!preg_match('/^[a-z]{2,}$/i', $tld)) {
            $fail('The email domain extension must contain only letters.');
            return;
        }
        
        // Total email length check (RFC 5321 limit)
        if (strlen($email) > 320) {
            $fail('The email address is too long. Maximum length is 320 characters.');
            return;
        }
    }
}
