<?php
// app/Http/Controllers/Auth/GoogleController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailAuthorizationService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;

class GoogleController extends Controller
{
    protected $emailAuthService;

    public function __construct(EmailAuthorizationService $emailAuthService)
    {
        $this->emailAuthService = $emailAuthService;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Verify email is authorized
            if (!$this->emailAuthService->isEmailAuthorized($googleUser->getEmail())) {
                throw ValidationException::withMessages([
                    'google' => 'This Google email is not authorized for access. Please use your official trainee email.'
                ]);
            }
            
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(rand(100000, 999999)),
                    'google_id' => $googleUser->getId(),
                    'role' => 'intern',
                ]);
            } else {
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }
            
            Auth::login($user);
            return redirect('/dashboard');
            
        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'google' => $e->getMessage()
            ]);
        }
    }
}