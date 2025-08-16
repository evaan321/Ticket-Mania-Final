<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            'role' => 'required|in:organizer,participant',
            'profile_image' => ['required', File::types(['png', 'jpg', 'jpeg','heic'])],
        ]);


            $profileImage = $request->file('profile_image');

            $profileImagePath = $profileImage->store('profile_images');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_image' => $profileImagePath,

        ]);

        event(new Registered($user));

        Auth::login($user);


        if ($request->role === 'organizer') {
            return redirect()->intended(route('events.index', absolute: false));
        }

        return redirect()->intended(route('participant.events.index', absolute: false));
    }
}
