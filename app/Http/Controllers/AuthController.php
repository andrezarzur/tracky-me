<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function index(Request $request) {
        if (auth()->user()) {
            return redirect('/home');
        }
        return Inertia::render('Login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::where('id', auth()->user()->id)->first();
            $metrics = $user->metrics;
            $metrics['visits'] += 1;
            $user->update(['metrics' => $metrics]);

            return redirect('/home');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function setupPassword() {
        // if (auth()->user()->password_set) {
        //     return redirect('/home');
        // }
        return Inertia::render('SetupPassword');
    }

    public function storePassword(Request $request) {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->password_set = true;
        $user->save();

        return redirect()->route('home');
    }
}