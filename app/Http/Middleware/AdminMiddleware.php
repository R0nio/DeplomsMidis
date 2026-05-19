<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем авторизацию и роль
        if (!auth()->check() || auth()->user()->role !== 'Admin') {
            // Если это AJAX запрос
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен. Требуются права администратора.'
                ], 403);
            }
            
            // Обычный запрос
            abort(403, 'Доступ запрещен. Требуются права администратора.');
        }

        return $next($request);
    }
}