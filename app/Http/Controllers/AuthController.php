<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Services\EmailAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $emailAuthService;

    public function __construct(EmailAuthorizationService $emailAuthService)
    {
        $this->emailAuthService = $emailAuthService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'reg_no';
        
        // For email logins, verify authorization
        if ($field === 'email' && !$this->emailAuthService->isEmailAuthorized($credentials['login'])) {
            throw ValidationException::withMessages([
                'login' => 'This email is not authorized for access. Please use your official trainee email.'
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

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'reg_no' => 'nullable|string|size:4|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify email is authorized
        if (!$this->emailAuthService->isEmailAuthorized($request->email)) {
            throw ValidationException::withMessages([
                'email' => 'This email is not authorized for registration. Please use your official trainee email.'
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'reg_no' => $request->reg_no,
            'password' => Hash::make($request->password),
            'role' => 'intern',
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}