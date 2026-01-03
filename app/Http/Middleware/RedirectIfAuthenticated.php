<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Check if user is driver or medical staff
                $staff = \App\Models\Staff::where('user_id', Auth::id())->first();
                
                if ($staff && in_array($staff->staff_type, ['driver', 'medical_staff'])) {
                    // Redirect to profile page for driver and medical staff
                    return redirect()->route('profile.edit');
                }
                
                // All other users go to dashboard
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
