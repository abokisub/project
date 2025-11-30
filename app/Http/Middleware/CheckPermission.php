<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthenticated',
            ], 401);
        }

        foreach ($permissions as $permission) {
            if ($request->user()->can($permission)) {
                return $next($request);
            }
        }

        return response()->json([
            'status' => 'error',
            'code' => 403,
            'message' => 'Unauthorized. Required permission: ' . implode(' or ', $permissions),
        ], 403);
    }
}

