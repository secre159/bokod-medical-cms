<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientProfileUpdateRequest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class PatientProfileController extends Controller
{
    /**
     * Display the patient's profile edit form.
     */
    public function edit()
    {
        // Get the patient record for the authenticated user
        $patient = Patient::where('user_id', Auth::id())->first();
        
        if (!$patient) {
            return redirect()->route('dashboard.index')
                           ->with('error', 'Patient profile not found. Please contact the administrator.');
        }
        
        return view('patient-profile.edit', compact('patient'));
    }
    
    /**
     * Update the patient's profile information.
     */
    public function update(PatientProfileUpdateRequest $request)
    {
        try {
            // Get the patient record for the authenticated user
            $patient = Patient::where('user_id', Auth::id())->first();
            
            if (!$patient) {
                return back()->withErrors(['error' => 'Patient profile not found.']);
            }
            
            DB::beginTransaction();
            
            // Handle profile picture upload or removal
            $validatedData = $request->validated();
            
            // Check if user wants to remove current profile picture
            if ($request->input('remove_profile_picture') == '1') {
                if ($patient->user->profile_picture) {
                    Storage::disk('public')->delete($patient->user->profile_picture);
                }
                $patient->user->update(['profile_picture' => null]);
            }
            // Handle new profile picture upload
            elseif ($request->hasFile('profile_picture')) {
                \Log::info('Profile picture upload started for patient ID: ' . $patient->id);
                
                $profilePicturePath = $this->handleProfilePictureUpload($request->file('profile_picture'), $patient);
                
                // Update the user's profile_picture field
                if ($profilePicturePath) {
                    $patient->user->update([
                        'profile_picture' => $profilePicturePath
                    ]);
                    \Log::info('Profile picture updated successfully: ' . $profilePicturePath);
                } else {
                    \Log::error('Profile picture upload failed for patient ID: ' . $patient->id);
                    return back()->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.'])
                                ->withInput();
                }
            }
            
            // Remove profile_picture from validated data as it's not a Patient field
            unset($validatedData['profile_picture']);
            
            // Update the patient record with validated data
            $patient->update($validatedData);
            
            // Also update the user's email if it was changed
            if ($request->has('email') && $request->email !== $patient->user->email) {
                $patient->user->update([
                    'email' => $request->email
                ]);
            }
            
            DB::commit();
            
            // Force refresh the patient model to get updated data
            $patient->refresh();
            $patient->user->refresh();
            
            return redirect()->route('patient.profile.edit')
                           ->with('success', 'Your profile has been updated successfully!');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()])
                        ->withInput();
        }
    }
    
    /**
     * Display the patient's profile (read-only view).
     */
    public function show()
    {
        // Get the patient record for the authenticated user
        $patient = Patient::where('user_id', Auth::id())->first();
        
        if (!$patient) {
            return redirect()->route('dashboard.index')
                           ->with('error', 'Patient profile not found. Please contact the administrator.');
        }
        
        return view('patient-profile.show', compact('patient'));
    }
    
    /**
     * Handle profile picture file upload
     */
    private function handleProfilePictureUpload($file, $patient)
    {
        try {
            \Log::info('Starting profile picture upload for patient: ' . $patient->id);
            \Log::info('Original file: ' . $file->getClientOriginalName() . ', Size: ' . $file->getSize() . ' bytes');
            
            // Delete old profile picture if it exists
            if ($patient->user->profile_picture) {
                \Log::info('Deleting old profile picture: ' . $patient->user->profile_picture);
                Storage::disk('public')->delete($patient->user->profile_picture);
            }
            
            // Get configuration settings
            $config = config('image_processing.profile_pictures');
            $directory = $config['directory'] ?? 'profile-pictures';
            
            // Check if image processing is available
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                return $this->processImageWithIntervention($file, $patient, $directory);
            } else {
                return $this->saveImageDirectly($file, $patient, $directory);
            }
            
        } catch (\Exception $e) {
            // Log the detailed error
            \Log::error('Profile picture upload failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }
    
    private function processImageWithIntervention($file, $patient, $directory)
    {
        // Get configuration settings
        $config = config('image_processing.profile_pictures');
        $maxWidth = $config['max_width'] ?? 400;
        $maxHeight = $config['max_height'] ?? 400;
        $quality = $config['jpeg_quality'] ?? 85;
        
        // Ensure the directory exists
        $fullDirectory = storage_path('app/public/' . $directory);
        if (!file_exists($fullDirectory)) {
            \Log::info('Creating directory: ' . $fullDirectory);
            mkdir($fullDirectory, 0755, true);
        }
        
        // Generate a unique filename
        $filename = 'profile_' . $patient->id . '_' . time() . '_' . Str::random(8) . '.jpg';
        $fullPath = $fullDirectory . '/' . $filename;
        
        \Log::info('Processing image with Intervention to: ' . $fullPath);
        
        // Process and optimize the image
        $image = Image::read($file->getRealPath());
        
        // Resize image maintaining aspect ratio
        $image->scaleDown(width: $maxWidth, height: $maxHeight);
        
        // Convert to JPEG with configured quality
        $image->toJpeg(quality: $quality);
        
        // Save the optimized image
        $image->save($fullPath);
        
        // Verify the file was saved
        if (!file_exists($fullPath)) {
            throw new \Exception('File was not saved to: ' . $fullPath);
        }
        
        $relativePath = $directory . '/' . $filename;
        \Log::info('Profile picture processed and saved successfully: ' . $relativePath);
        
        return $relativePath;
    }
    
    private function saveImageDirectly($file, $patient, $directory)
    {
        \Log::info('Image processing extensions not available, saving directly');
        
        // Get original file extension
        $originalExtension = $file->getClientOriginalExtension();
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array(strtolower($originalExtension), $allowedExtensions)) {
            throw new \Exception('Invalid file extension: ' . $originalExtension);
        }
        
        // Generate a unique filename with original extension
        $filename = 'profile_' . $patient->id . '_' . time() . '_' . Str::random(8) . '.' . $originalExtension;
        
        // Store the file using Laravel's built-in storage
        $path = Storage::disk('public')->putFileAs(
            $directory,
            $file,
            $filename
        );
        
        if (!$path) {
            throw new \Exception('Failed to save file using Storage::putFileAs');
        }
        
        \Log::info('Profile picture saved directly: ' . $path);
        
        return $path;
    }
}
