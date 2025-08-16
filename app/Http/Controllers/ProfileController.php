<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile picture.
     */
    public function updatePicture(Request $request): RedirectResponse
    {
        // Log the incoming request data for debugging
        Log::info('Update Picture Request: ', $request->all());

        $request->validate([
            'profile_image' => ['image', 'max:2048', 'mimes:png,jpg,jpeg,heic'],
        ], [
            'profile_image.image' => 'The file must be an image.',
            'profile_image.max' => 'The image must not exceed 2MB.',
            'profile_image.mimes' => 'Only PNG, JPG, JPEG, and HEIC files are allowed.',
        ]);

        $user = $request->user();

        // Debug: Check if file is present
        if ($request->hasFile('profile_image')) {
            Log::info('File detected: ', ['filename' => $request->file('profile_image')->getClientOriginalName()]);

            // Delete old image if it exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
                Log::info('Old image deleted: ', ['path' => $user->profile_image]);
            }

            // Store new image
            $profileImage = $request->file('profile_image');
            $profileImagePath = $profileImage->store('profile_images', 'public');
            if ($profileImagePath) {
                $user->profile_image = $profileImagePath;
                Log::info('New image stored: ', ['path' => $profileImagePath]);
            } else {
                Log::error('Failed to store new image.');
                return Redirect::back()->withErrors(['profile_image' => 'Failed to upload the image. Please try again.']);
            }
        } else {
            Log::warning('No file uploaded.');
            return Redirect::back()->withErrors(['profile_image' => 'No image file was uploaded.']);
        }

        // Save the user and log the result
        if ($user->save()) {
            Log::info('User saved, new profile_image: ', ['value' => $user->profile_image]);
        } else {
            Log::error('Failed to save user.');
            return Redirect::back()->withErrors(['profile_image' => 'Failed to update profile picture.']);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-picture-updated');
    }

    /**
     * Remove the user's profile picture.
     */
    public function removePicture(Request $request): RedirectResponse
    {
        $user = $request->user();
        $dummyImagePath = 'dummy_profile_images/placeholder.jpg';

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
            $user->profile_image = null;
        }

        if (!Storage::disk('public')->exists($dummyImagePath)) {
            try {
                $dummyImageContent = @file_get_contents('https://placehold.co/150');
                if ($dummyImageContent !== false) {
                    Storage::disk('public')->put($dummyImagePath, $dummyImageContent);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to download dummy image: ' . $e->getMessage());
            }
        }

        $user->profile_image = Storage::disk('public')->exists($dummyImagePath) ? $dummyImagePath : $dummyImagePath;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-picture-removed');
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

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
