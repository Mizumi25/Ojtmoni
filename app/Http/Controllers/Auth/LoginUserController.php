<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Location; // Add this

class LoginUserController extends Controller
{
    public function login() {
        return view('auth.login');
    }

    public function store(Request $request) {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
    
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';
        $remember = $request->has('remember');
    
        $user = User::where($loginType, $request->login)->first();
    
        if (!$user) {
            return back()->withErrors([
                'login' => 'No account found with this ' . ($loginType === 'email' ? 'email.' : 'student ID.'),
            ]);
        }
    
        if (!Auth::guard('web')->attempt([$loginType => $request->login, 'password' => $request->password], $remember)) {
            return back()->withErrors([
                'password' => 'Wrong password. Please try again.',
            ]);
        }
    
        $user = Auth::user();
    
        // Location creation if student
        if ($user->role === 'student' && !$user->location) {
            $location = Location::create([
                'user_id' => $user->id,
                'latitude' => null,
                'longitude' => null,
            ]);
            $user->location_id = $location->id;
            $user->save();
        }
    
        // Redirect by role & status
        if ($user->role === 'student') {
            return match ($user->status) {
                'approved' => redirect()->route('application.agency'),
                'intern' => redirect()->route('dashboard4.index'),
                'pending' => redirect()->route('intern.pending'),
                default => redirect()->route('login')->withErrors([
                    'login' => 'Unauthorized student status.',
                ]),
            };
        }
    
        return match ($user->role) {
            'coordinator' => redirect()->route('dashboard.index'),
            'admin' => redirect()->route('dashboard2.index'),
            'agency' => redirect()->route('dashboard3.index'),
            default => redirect()->route('dashboard.index'),
        };
    }


    public function logout(Request $request) {
        $user = Auth::user();
    
        if ($user && $user->role === 'student') {
            // Delete location record
            $user->location()?->delete();
    
            // Clear location_id from users table
            $user->location_id = null;
            $user->save();
        }
    
        Auth::guard('web')->logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return to_route('login');
    }

}
