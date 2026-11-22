<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePinSetup
{
    /**
     * Force customers to create a PIN before using the portal.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->hasPin() || $request->routeIs('pin.setup', 'logout')) {
            return $next($request);
        }

        return redirect()->route('pin.setup');
    }
}
