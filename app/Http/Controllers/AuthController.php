<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('user_id')) {
            // Redirect based on role
            if (session('role_id') == 1) { // Administrator
                return redirect('/dashboard');
            } else { // Employee
                return redirect('/employee/dashboard');
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])
                   ->where('is_active', true)
                   ->with('role')
                   ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            session([
                'user_id' => $user->id,
                'user_name' => $user->f_name . ' ' . $user->l_name,
                'user_role' => $user->role->name,
                'role_id' => $user->role_id,
                'username' => $user->username,
            ]);

            if ($user->role_id == 1) { // Administrator
                return redirect('/dashboard')->with('success', 'Welcome back, ' . $user->f_name . '!');
            } else { // Employee
                return redirect('/pos')->with('success', 'Welcome, ' . $user->f_name . '!');
            }
        }

        return back()->with('error', 'Invalid credentials .');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login')->with('success', 'Logged out successfully.');
    }
}
