<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = new User();
        $user->fill([
            'name' => Arr::get($validated, 'name'),
            'email' => Arr::get($validated, 'email'),
        ]);
        $user->password = Hash::make(Arr::get($validated, 'password'));
        $user->save();

        Auth::login($user);

        return redirect()->intended('tasks')->with('success', 'Registration successful!');
    }
}
