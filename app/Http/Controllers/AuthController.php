<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private function isAuthorizedEmail($email)
{
    // Always allow these admin emails
    $adminEmails = ['admin@example.com', 'admin@seat.com'];
    if (in_array(strtolower($email), array_map('strtolower', $adminEmails))) {
        return true;
    }

    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://prohub.slt.com.lk/ProhubTrainees/api/MainApi/AllActiveTrainees', [
            'secretKey' => 'TraineesApi_SK_8d!x7F#mZ3@pL2vW'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['isSuccess']) && $data['isSuccess'] && isset($data['dataBundle'])) {
                foreach ($data['dataBundle'] as $trainee) {
                    if (isset($trainee['Trainee_Email']) && strtolower($trainee['Trainee_Email']) === strtolower($email)) {
                        return true;
                    }
                }
            }
        }
        
        Log::error('Email authorization failed', [
            'email' => $email,
            'response' => $response->body(),
            'status' => $response->status()
        ]);
        
        return false;
    } catch (\Exception $e) {
        Log::error('Email authorization exception', [
            'email' => $email,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string', // Can be email or reg_no
            'password' => 'required|string',
        ]);

        // Determine if login is email or reg_no
        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'reg_no';
        
        // If logging in with email, verify it's authorized
        if ($field === 'email' && !$this->isAuthorizedEmail($credentials['login'])) {
            return back()->withErrors([
                'login' => 'This email is not authorized for student access.',
            ]);
        }

        if (Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    // Show registration form
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'reg_no' => 'nullable|string|size:4|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify email is authorized before registration
        if (!$this->isAuthorizedEmail($request->email)) {
            return back()->withErrors([
                'email' => 'This email is not authorized for student registration.',
            ])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'reg_no' => $request->reg_no, // Will auto-generate if empty
            'password' => Hash::make($request->password),
            'role' => 'intern',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}