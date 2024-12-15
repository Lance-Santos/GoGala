<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateOrrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Retrieve the event ID from the route (assuming it's passed as a route parameter)
        try {
            $eventId = $request->route('organization');
            // dd($eventId);
            if ($eventId) {
                $organization = Organization::where('organization_slug', $eventId)->first();
            } else {
                $org = Session::get('org');
                $organization = Organization::where('id', $org)->first();
                // dd($organization);
            }
            $isOwner = Auth::user()->id === $organization->user_id;
            if (!$isOwner) {
                return redirect('/');
            }
            return $next($request);
        } catch (Exception $e) {
            dd("Error");
        }
    }
}
