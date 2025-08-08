<?php
// app/Services/EmailAuthorizationService.php

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
            
            if ($data['isSuccess'] && isset($data['dataBundle'])) {
                $authorizedEmails = collect($data['dataBundle'])
                    ->map(fn($item) => strtolower($item['Trainee_Email']))
                    ->toArray();
                
                return in_array(strtolower($email), $authorizedEmails);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Email authorization API error: ' . $e->getMessage());
            return false;
        }
    }
}