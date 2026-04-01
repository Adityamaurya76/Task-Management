<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, return JSON error instead of redirect
        if ($request->expectsJson()) {
            return null;
        }

        return route('login');
    }

    /**
     * Handle unauthenticated requests for API
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Authentication token is missing or invalid. Please log in first.'
            ], 401));
        }

        parent::unauthenticated($request, $guards);
    }
}
