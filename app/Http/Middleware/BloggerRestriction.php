<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BloggerRestriction
{
    /**
     * Handle an incoming request.
     *
     * Restrict bloggers to only blog-related routes.
     * Admins have full access to everything.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Admins have full access to everything - bypass all restrictions
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        // Bloggers can only access blog-related routes
        if ($request->user()->role === 'blogger') {
            $allowedPatterns = [
                'blogs/*',
                'blog/*',
                'logout',
                'password.*',
                'verification.*',
                'profile.*',
                'account.settings*',
            ];

            $currentRoute = $request->route()->getName();
            
            // Check if current route is allowed
            $isAllowed = false;
            foreach ($allowedPatterns as $pattern) {
                if ($currentRoute === $pattern || str_starts_with($currentRoute, rtrim($pattern, '*'))) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                // Redirect to blog dashboard if trying to access restricted area
                return redirect()->route('blogs.dashboard')
                    ->with('error', 'Bloggers can only access blog-related features. To upload memes, please contact support to change your account type.');
            }
        }

        return $next($request);
    }
}
