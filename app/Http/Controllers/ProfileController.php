<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile and settings center.
     */
    public function edit(Request $request)
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile details.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar in the avatars folder
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        auth()->setUser($user->fresh());

        return redirect()->route('profile.edit')->with('success', __('messages.update_profile_success'));
    }

    /**
     * Securely update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit', ['tab' => 'security'])->with('success', __('messages.update_password_success'));
    }

    /**
     * Update user's settings and preferences (theme, language, alerts).
     */
    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'theme_preference' => ['required', 'string', 'in:light,dark,system'],
            'language_preference' => ['required', 'string', 'in:en,hi'],
            'location_permission_granted' => ['nullable', 'boolean'],
            'notifications' => ['nullable', 'array'],
        ]);

        // Clean and update preferences
        $user->theme_preference = $validated['theme_preference'];
        $user->language_preference = $validated['language_preference'];
        $user->location_permission_granted = (bool) ($validated['location_permission_granted'] ?? false);

        // Save notification checkboxes array
        $user->notification_preferences = [
            'email' => isset($validated['notifications']['email']),
            'push' => isset($validated['notifications']['push']),
            'sms' => isset($validated['notifications']['sms']),
        ];

        $user->save();

        // Update active locale session instantly
        session()->put('locale', $validated['language_preference']);
        app()->setLocale($validated['language_preference']);

        return redirect()->route('profile.edit', ['tab' => 'settings'])
            ->with('success', __('messages.update_settings_success'))
            ->with('theme_updated', $validated['theme_preference']);
    }
}
