<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailAuthorizationService
{
    public function isEmailAuthorized(string $email): bool
    {
        try {
            $response = Http::post('https://prohub.slt.com.lk/ProhubTrainees/api/MainApi/AllActiveTrainees', [
                'secretKey' => 'TraineesApi_SK_8d!x7F#mZ3@pL2vW'
            ]);
            
            Log::debug('API Response:', $response->json());
            
            $data = $response->json();
            
            if (!($data['isSuccess'] ?? false)) {
                Log::error('API returned unsuccessful response', $data);
                return false;
            }
            
            $authorizedEmails = collect($data['dataBundle'] ?? [])
                ->pluck('Trainee_Email')
                ->map(fn ($email) => strtolower(trim($email)))
                ->filter()
                ->toArray();
                
            $inputEmail = strtolower(trim($email));
            
            Log::debug('Checking email:', [
                'input' => $inputEmail,
                'authorized' => $authorizedEmails,
                'result' => in_array($inputEmail, $authorizedEmails)
            ]);
            
            return in_array($inputEmail, $authorizedEmails);
            
        } catch (\Exception $e) {
            Log::error('Email authorization failed: ' . $e->getMessage());
            return false;
        }
    }
}