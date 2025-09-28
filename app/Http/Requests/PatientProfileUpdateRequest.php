<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\PhoneNumberRule;
use App\Rules\EmailValidationRule;

class PatientProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow authenticated users with patient role
        $authorized = Auth::check() && Auth::user()->role === 'patient';
        
        \Log::info('REAL PATIENT: Authorization check', [
            'auth_check' => Auth::check(),
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role ?? 'not logged in',
            'authorized' => $authorized
        ]);
        
        return $authorized;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $patient = Patient::where('user_id', Auth::id())->first();
        
        $rules = [
            // Contact Information (patients can edit)
            'phone_number' => ['nullable', new PhoneNumberRule],
            'email' => [
                'required',
                new EmailValidationRule,
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
            'address' => 'nullable|string|max:1000',
            'course' => 'nullable|string|max:100',
            
            // Emergency Contact Information (patients can edit)
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_phone' => ['nullable', new PhoneNumberRule],
            'emergency_contact_address' => 'nullable|string|max:1000',
            
            // Profile Picture
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ];
        
        \Log::info('REAL PATIENT: Validation rules prepared', [
            'user_id' => Auth::id(),
            'patient_found' => $patient ? true : false,
            'rules' => array_keys($rules),
            'has_profile_picture_file' => request()->hasFile('profile_picture')
        ]);
        
        return $rules;
    }
    
    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use by another account.',
            'phone_number' => 'Phone number must be a valid format with at least 11 digits or start with +639.',
            'address.max' => 'Address cannot exceed 1000 characters.',
            'course.max' => 'Course/Department cannot exceed 100 characters.',
            'emergency_contact_name.max' => 'Emergency contact name cannot exceed 255 characters.',
            'emergency_contact_relationship.max' => 'Relationship cannot exceed 100 characters.',
            'emergency_contact_phone' => 'Emergency contact phone must be a valid format with at least 11 digits or start with +639.',
            'emergency_contact_address.max' => 'Emergency contact address cannot exceed 1000 characters.',
            'profile_picture.image' => 'Profile picture must be an image file.',
            'profile_picture.mimes' => 'Profile picture must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'profile_picture.max' => 'Profile picture size cannot exceed 5MB.',
            
        ];
    }
    
    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'phone_number' => 'phone number',
            'email' => 'email address',
            'address' => 'address',
            'course' => 'course/department',
            'emergency_contact_name' => 'emergency contact name',
            'emergency_contact_relationship' => 'emergency contact relationship',
            'emergency_contact_phone' => 'emergency contact phone',
            'emergency_contact_address' => 'emergency contact address',
            'profile_picture' => 'profile picture',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Log::error('REAL PATIENT: Validation failed', [
            'user_id' => Auth::id(),
            'errors' => $validator->errors()->toArray(),
            'input_data' => $this->all(),
            'file_info' => $this->hasFile('profile_picture') ? [
                'name' => $this->file('profile_picture')->getClientOriginalName(),
                'size' => $this->file('profile_picture')->getSize(),
                'mime' => $this->file('profile_picture')->getMimeType()
            ] : 'no file'
        ]);

        parent::failedValidation($validator);
    }
}
