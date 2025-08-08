<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailAuthorizationService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;

class GoogleController extends Controller
{
    protected EmailAuthorizationService $emailAuthService;

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
            
            if (!$this->emailAuthService->isEmailAuthorized($googleUser->getEmail())) {
                throw ValidationException::withMessages([
                    'google' => 'Your Google email is not authorized for access.'
                ]);
            }
            
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
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
            return redirect('/login')->withErrors([
                'google' => $e->getMessage()
            ]);
        }
    }
}