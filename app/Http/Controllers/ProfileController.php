<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\ProfilePictureService;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Redirect patient users to the specialized patient profile edit page
        if ($request->user()->role === 'patient') {
            return redirect()->route('patient.profile.edit')
                ->with('info', 'Redirected to your patient profile page with full editing capabilities.');
        }
        
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();
        
        // Handle avatar upload using ProfilePictureService (supports Cloudinary)
        if ($request->hasFile('avatar')) {
            try {
                $avatarPath = ProfilePictureService::uploadProfilePicture($request->file('avatar'), $user);
                $validatedData['avatar'] = $avatarPath;
                $validatedData['profile_picture'] = $avatarPath;
                \Log::info('Profile picture updated via ProfileController for user: ' . $user->id);
            } catch (\Exception $e) {
                \Log::error('Profile picture upload failed in ProfileController for user: ' . $user->id . ': ' . $e->getMessage());
                return Redirect::route('profile.edit')
                    ->withErrors(['avatar' => 'Failed to upload profile picture: ' . $e->getMessage()]);
            }
        }
        
        $user->fill($validatedData);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
