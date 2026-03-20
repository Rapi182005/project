<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin() {
        return view('login');
    }

    public function showRegister() {
        return view('register');
    }

    public function register(Request $request) {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->username,
            'email' => $request->email , 
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    public function login(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');

        // ADMIN LOGIN
        if ($username === 'admin' && $password === 'admin') {
            session(['admin_logged_in' => true, 'user_role' => 'Admin']);
            return redirect()->route('admin.dashboard');
        }

        // STORE OWNER LOGIN
        if ($username === 'owner' && $password === 'owner123') {
            session(['admin_logged_in' => true, 'user_role' => 'Store Owner']);
            return redirect()->route('admin.dashboard');
        }

        $user = User::where('name', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            session([
                'user_logged_in' => true,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role 
            ]);
            return redirect()->route('user.dashboard');
        }

        return back()->with('error', 'Wrong username or password!');
    }
}