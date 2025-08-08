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
        
        Log::debug('Google user data:', [
            'email' => $googleUser->getEmail(),
            'name' => $googleUser->getName()
        ]);
        
        $email = strtolower(trim($googleUser->getEmail()));
        
        if (!$this->emailAuthService->isEmailAuthorized($email)) {
            Log::error('Google email not authorized', ['email' => $email]);
            throw ValidationException::withMessages([
                'google' => 'Your Google email is not authorized for access.'
            ]);
        }
        
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(rand(100000, 999999)),
                'role' => 'intern'
            ]
        );

        Auth::login($user);
        return redirect('/dashboard');
        
    } catch (\Exception $e) {
        Log::error('Google auth failed: ' . $e->getMessage());
        return redirect('/login')->withErrors([
            'google' => 'Authentication failed. Please try again.'
        ]);
    }
}
}