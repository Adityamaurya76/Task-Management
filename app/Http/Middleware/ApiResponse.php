<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If response is an error and request expects JSON, ensure JSON format
        if ($request->expectsJson() && $response->getStatusCode() >= 400) {
            try {
                $data = json_decode($response->getContent(), true);
                if (!isset($data['success'])) {
                    return response()->json([
                        'success' => false,
                        'message' => $data['message'] ?? 'An error occurred',
                        'errors' => $data['errors'] ?? null
                    ], $response->getStatusCode());
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please provide a valid token.'
                ], 401);
            }
        }

        return $response;
    }
}
