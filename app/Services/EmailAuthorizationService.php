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
            
            $data = $response->json();
            
            if ($data['isSuccess'] ?? false) {
                $authorizedEmails = collect($data['dataBundle'] ?? [])
                    ->pluck('Trainee_Email')
                    ->map(fn ($email) => strtolower($email))
                    ->toArray();
                
                return in_array(strtolower($email), $authorizedEmails);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Email authorization failed: ' . $e->getMessage());
            return false;
        }
    }
}