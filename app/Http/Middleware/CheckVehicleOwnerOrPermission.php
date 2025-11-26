<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Staff;

class CheckVehicleOwnerOrPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        // Check if user is a vehicle owner
        $isVehicleOwner = Staff::where('user_id', auth()->id())
            ->where('staff_type', 'vehicle_owner')
            ->exists();
        
        // If vehicle owner, allow access
        if ($isVehicleOwner) {
            return $next($request);
        }
        
        // Otherwise, check permission if specified
        if ($permission && !auth()->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}
