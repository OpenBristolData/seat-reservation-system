<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleController extends Controller
{
    private function isAuthorizedEmail($email)
    {
        // Always allow these admin emails
        $adminEmails = ['admin@example.com', 'admin@seat.com'];
        if (in_array(strtolower($email), array_map('strtolower', $adminEmails))) {
            return true;
        }

        // Cache the API response for 5 minutes to avoid repeated calls
        $cacheKey = 'active_trainees_' . md5($email);
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($email) {
            try {
                $response = Http::timeout(10)->retry(3, 100)->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post('https://prohub.slt.com.lk/ProhubTrainees/api/MainApi/AllActiveTrainees', [
                    'secretKey' => 'TraineesApi_SK_8d!x7F#mZ3@pL2vW'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['isSuccess']) && $data['isSuccess'] && isset($data['dataBundle'])) {
                        foreach ($data['dataBundle'] as $trainee) {
                            if (isset($trainee['Trainee_Email']) && 
                                strtolower($trainee['Trainee_Email']) === strtolower($email)) {
                                return true;
                            }
                        }
                    }
                }
                
                Log::error('Email authorization failed (Google)', [
                    'email' => $email,
                    'response' => $response->body() ?? 'NULL',
                    'status' => $response->status()
                ]);
                
                return false;
            } catch (\Exception $e) {
                Log::error('Email authorization exception (Google)', [
                    'email' => $email,
                    'error' => $e->getMessage()
                ]);
                // Fail open in case of API errors
                return true;
            }
        });
    }

public function redirectToGoogle()
    {
        // Only prompt for account selection if we don't have a session
        $parameters = [];
        
        if (!session()->has('google_auth_attempt')) {
            session()->put('google_auth_attempt', true);
            $parameters = ['prompt' => 'select_account'];
        } else {
            // For subsequent attempts, use auto-login if possible
            $parameters = ['prompt' => 'none'];
        }

        return Socialite::driver('google')
            ->with($parameters)
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $email = $googleUser->getEmail();

            // Clear the session flag after successful retrieval
            session()->forget('google_auth_attempt');

            if (!$email) {
                throw new \Exception("Google authentication failed - no email returned");
            }

            // Verify email is authorized
            if (!$this->isAuthorizedEmail($email)) {
                return redirect('/login')->with('error', 
                    'This email is not authorized. Please use your organization email.');
            }
            
            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(rand(100000, 999999)),
                    'google_id' => $googleUser->getId(),
                    'role' => 'intern',
                ]
            );

            // Update Google ID if not set
            if (empty($user->google_id)) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user, true);
            return redirect('/dashboard');
            
        } catch (\Exception $e) {
            Log::error('Google auth error: ' . $e->getMessage());
            
            // On error, clear the session flag to force account selection next time
            session()->forget('google_auth_attempt');
            
            return redirect('/login')->with('error', 
                'Google authentication failed. Please try again.');
        }
    }

     
}