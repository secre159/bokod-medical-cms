<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberRule implements ValidationRule
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
        
        // Remove all non-digit characters for validation
        $cleanNumber = preg_replace('/[^0-9]/', '', $value);
        
        // Check if the number contains only digits (after cleaning)
        if (!preg_match('/^[0-9+\s()-]+$/', $value)) {
            $fail('The :attribute field must contain only numbers and valid phone number characters.');
            return;
        }
        
        // Special check for malformed international numbers (e.g., ++639...)
        if (str_contains($value, '++')) {
            $fail('The :attribute field contains invalid formatting.');
            return;
        }
        
        // Check for Philippines format with proper network codes
        if (preg_match('/^\+639[0-9]{9}$/', $cleanNumber)) {
            // Valid Philippines international format (+639XXXXXXXXX = exactly 12 digits after +)
            // Additional check for valid network codes (91-99 for mobile)
            $networkCode = substr($cleanNumber, 4, 2);
            if (in_array($networkCode, ['17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '46', '47', '48', '49', '73', '74', '81', '82', '88', '89', '92', '93', '94', '95', '96', '97', '98', '99'])) {
                return;
            } else {
                // Philippines format but invalid network code
                $fail('The :attribute field must be a valid Philippines number (+639XXXXXXXX or 09XXXXXXXX) or international format with 11-15 digits.');
                return;
            }
        }
        
        if (preg_match('/^09[0-9]{9}$/', $cleanNumber)) {
            // Valid Philippines local format (09XXXXXXXXX = exactly 11 digits)
            // Additional check for valid network codes
            $networkCode = substr($cleanNumber, 2, 2);
            if (in_array($networkCode, ['17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '46', '47', '48', '49', '73', '74', '81', '82', '88', '89', '92', '93', '94', '95', '96', '97', '98', '99'])) {
                return;
            } else {
                // Philippines format but invalid network code
                $fail('The :attribute field must be a valid Philippines number (+639XXXXXXXX or 09XXXXXXXX) or international format with 11-15 digits.');
                return;
            }
        }
        
        // For non-Philippines numbers, accept exactly 11-15 digits (international standard)
        if (strlen($cleanNumber) >= 11 && strlen($cleanNumber) <= 15) {
            // Must start with + for international numbers or be exactly 11 digits
            if (str_starts_with($value, '+') || strlen($cleanNumber) === 11) {
                return;
            }
        }
        
        $fail('The :attribute field must be a valid Philippines number (+639XXXXXXXX or 09XXXXXXXX) or international format with 11-15 digits.');
    }
}
