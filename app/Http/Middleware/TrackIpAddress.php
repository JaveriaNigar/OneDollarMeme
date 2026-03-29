<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\IpAddress;

class TrackIpAddress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track IP address for authenticated users
        if (auth()->check()) {
            IpAddress::recordLogin(
                auth()->id(),
                $request->ip(),
                $request->userAgent()
            );
        }

        return $response;
    }
}
