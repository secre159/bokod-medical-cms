<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientProfileUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Services\ImgBBService;
use App\Services\LocalProfilePictureService;

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
        Log::info('REAL PATIENT: Profile update started', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role,
            'request_method' => $request->method(),
            'has_profile_picture' => $request->hasFile('profile_picture'),
            'all_request_data' => $request->all(),
            'profile_picture_info' => $request->hasFile('profile_picture') ? [
                'original_name' => $request->file('profile_picture')->getClientOriginalName(),
                'size' => $request->file('profile_picture')->getSize(),
                'mime_type' => $request->file('profile_picture')->getMimeType()
            ] : null
        ]);
        
        try {
            // Get the patient record for the authenticated user
            $patient = Patient::where('user_id', Auth::id())->first();
            
            if (!$patient) {
                return back()->withErrors(['error' => 'Patient profile not found.']);
            }
            
            DB::beginTransaction();
            
            // Get validated data
            $validatedData = $request->validated();
            
            // Handle profile picture upload - try ImgBB first, fallback to local
            if ($request->hasFile('profile_picture')) {
                $uploadSuccess = false;
                $profilePictureUrl = null;
                
                // Try ImgBB first
                try {
                    $imgbbService = new ImgBBService();
                    $uploadResult = $imgbbService->uploadProfilePicture($request->file('profile_picture'), $patient->user->id);
                    
                    if ($uploadResult['success']) {
                        $profilePictureUrl = $uploadResult['url'];
                        $uploadSuccess = true;
                        Log::info('Profile picture uploaded to ImgBB successfully');
                    }
                } catch (\Exception $e) {
                    Log::warning('ImgBB upload failed, trying local storage', ['error' => $e->getMessage()]);
                }
                
                // Fallback to local storage if ImgBB failed
                if (!$uploadSuccess) {
                    $localService = new LocalProfilePictureService();
                    $localResult = $localService->uploadProfilePicture($request->file('profile_picture'), $patient->user->id);
                    
                    if ($localResult['success']) {
                        $profilePictureUrl = $localResult['url'];
                        Log::info('Profile picture uploaded to local storage successfully');
                    } else {
                        throw new \Exception('Profile picture upload failed: ' . $localResult['error']);
                    }
                }
                
                // Update the user's profile picture
                if ($profilePictureUrl) {
                    Log::info('Updating user profile picture', [
                        'user_id' => $patient->user->id,
                        'old_profile_picture' => $patient->user->profile_picture,
                        'new_profile_picture' => $profilePictureUrl
                    ]);
                    
                    $patient->user->update([
                        'profile_picture' => $profilePictureUrl
                    ]);
                    
                    // Verify the update
                    $patient->user->refresh();
                    Log::info('Profile picture update verification', [
                        'user_id' => $patient->user->id,
                        'current_profile_picture' => $patient->user->profile_picture,
                        'update_successful' => $patient->user->profile_picture === $profilePictureUrl
                    ]);
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
            
            // Set session flag to indicate profile picture was updated (for cache-busting)
            if ($request->hasFile('profile_picture')) {
                session(['profile_updated_user_' . $patient->user->id => true]);
                // Clear the session flag after 15 minutes
                session(['profile_updated_user_' . $patient->user->id . '_expires' => now()->addMinutes(15)]);
            }
            
            Log::info('REAL PATIENT: Profile update completed successfully', [
                'user_id' => $patient->user->id,
                'final_profile_picture' => $patient->user->profile_picture,
                'has_profile_picture' => $patient->user->hasProfilePicture(),
                'redirect_route' => 'patient.profile.edit',
                'session_success' => 'Your profile has been updated successfully!',
                'profile_picture_updated' => $request->hasFile('profile_picture'),
                'session_cache_bust_set' => session('profile_updated_user_' . $patient->user->id, false)
            ]);
            
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
}
