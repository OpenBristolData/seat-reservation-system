<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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