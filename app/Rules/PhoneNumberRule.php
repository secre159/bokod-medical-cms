<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberRule implements ValidationRule
{
    /**
     * Run the validation rule.
     * 
     * Simplified Philippine mobile number validation: 09XXXXXXXXX (exactly 11 digits)
     * Must start with '09' followed by 9 digits - no strict network code validation
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if value is empty (handle nullable separately)
        if (empty($value)) {
            return;
        }
        
        // Handle international format (+63) by converting to local format
        $originalValue = trim($value);
        $processedValue = $originalValue;
        
        // Convert +63 format to 09 format
        if (str_starts_with($originalValue, '+63')) {
            $withoutCountryCode = substr($originalValue, 3);
            $processedValue = '0' . ltrim($withoutCountryCode, ' ');
        }
        
        // Remove all non-digit characters for validation
        $cleanNumber = preg_replace('/[^0-9]/', '', $processedValue);
        
        // Check if the original value contains only valid phone characters
        if (!preg_match('/^[\+]?[0-9\s()\-]+$/', $originalValue)) {
            $fail('The :attribute field must contain only numbers, spaces, parentheses, hyphens, and + symbol.');
            return;
        }
        
        // Enforce exact length: must be exactly 11 digits
        if (strlen($cleanNumber) !== 11) {
            if (strlen($cleanNumber) > 11) {
                $fail('The :attribute field has too many digits (' . strlen($cleanNumber) . '). Philippine mobile numbers must be exactly 11 digits (09XXXXXXXXX).');
            } else {
                $fail('The :attribute field has too few digits (' . strlen($cleanNumber) . '). Philippine mobile numbers must be exactly 11 digits (09XXXXXXXXX).');
            }
            return;
        }
        
        // Must start with '09'
        if (!str_starts_with($cleanNumber, '09')) {
            $fail('The :attribute field must start with "09" for Philippine mobile numbers.');
            return;
        }
        
        // That's it! No strict network code validation - just 11 digits starting with 09
        // This allows for flexibility with newer or less common network prefixes
    }
}
