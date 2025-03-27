<?php

namespace App\Http\Middleware;

use App\Filament\Pages\Unapproved;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApprovedUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Allow logout requests and non-authenticated requests to proceed
        if (!$user || $request->routeIs('*logout')) {
            return $next($request);
        }
        
        $isUnapprovedRoute = $request->routeIs(Unapproved::getRouteName());
        Log::info('ApprovedUserMiddleware: isUnapprovedRoute: ' . $isUnapprovedRoute);
        // If user is approved but trying to access Unapproved page, 
        // redirect to the appropriate dashboard
        if ($user->is_approved && $isUnapprovedRoute) {
            if ($user->isAdmin()) {
                return redirect()->route(\App\Filament\Admin\Pages\Dashboard::getRouteName());
            } else {
                return redirect()->route(\App\Filament\Pages\Dashboard::getRouteName());
            }
        }
        
        // If user is not approved and not on the Unapproved page, 
        // redirect to the Unapproved page
        if (!$user->is_approved && !$isUnapprovedRoute) {
            return redirect()->route(Unapproved::getRouteName());
        }
        
        // Otherwise, proceed with the request
        return $next($request);
    }
}
