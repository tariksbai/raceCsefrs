<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'user_type' => 'citizen',
        ]);

        $citizenRole = Role::where('slug', 'citizen')->first();
        if ($citizenRole) {
            $user->roles()->attach($citizenRole);
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Inscription réussie. Bienvenue sur la plateforme !');
    }
}
