<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning("Unauthorized: User not authenticated.");
            return Redirect::route('login');
        }

        // Format: role:status (e.g. student:approved)
        $authorized = collect($roles)->contains(function ($roleStatus) use ($user) {
            [$role, $status] = array_pad(explode(':', $roleStatus), 2, null);
            return $user->role === $role && ($status === null || $user->status === $status);
        });

        if (!$authorized) {
            Log::warning("Unauthorized: User ID {$user->id}, Role: {$user->role}, Status: {$user->status}, Allowed: " . implode(', ', $roles));

            switch ($user->role) {
                case 'coordinator':
                    return Redirect::route('dashboard.index');
                case 'admin':
                    return Redirect::route('dashboard2.index');
                case 'agency':
                    return Redirect::route('dashboard3.index');
                case 'student':
                    return match ($user->status) {
                        'approved' => Redirect::route('application.agency'),
                        'pending' => Redirect::route('intern.pending'),
                        default => Redirect::route('dashboard4.index'),
                    };
                default:
                    return abort(403, 'Unauthorized access.');
            }
        }

        Log::info("Authorized: User ID {$user->id}, Role: {$user->role}, Status: {$user->status}");
        return $next($request);
    }
}
