<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCacheMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
        
        return $response;
    }
}