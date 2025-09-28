<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberRule implements ValidationRule
{
    /**
     * Run the validation rule.
     * 
     * Enforces Philippine mobile number format: 09XXXXXXXXX (exactly 11 digits)
     * Must start with '09' followed by 9 digits with valid network codes
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
        
        // Additional validation: Check for valid network prefixes (updated 2024)
        $networkCode = substr($cleanNumber, 2, 2);
        $validNetworkCodes = [
            // Globe/TM network codes
            '05', '06', '15', '16', '17', '26', '27', '35', '36', '37', '94', '95', '96', '97',
            // Smart/TNT/Sun network codes (verified active prefixes)
            '07', '08', '09', '10', '11', '13', '14', '18', '19', '20', '21', '22', '23', 
            '28', '29', '30', '31', '32', '33', '34', '38', '39', '40', '42', '43', '44', 
            '81', '83', '84', '89', '98', '99',
            // DITO network codes
            '91', '92', '93'
        ];
        
        if (!in_array($networkCode, $validNetworkCodes)) {
            $fail('The :attribute field contains an invalid Philippines network code "' . $networkCode . '". Valid examples: 0917 (Globe), 0905 (Globe), 0998 (Smart), 0913 (Smart), 0991 (DITO).');
            return;
        }
    }
}
