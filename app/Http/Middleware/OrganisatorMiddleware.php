<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganisatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'Organisator') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не найдено'
                ], 404);
            }
            
            abort(404, 'Не найдено');
        }

        return $next($request);
    }
}
