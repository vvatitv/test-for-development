<?php

namespace App\Http\Middleware;

use Closure;

class RedirectToHttps
{
    public function handle($request, Closure $next)
    {
        
        if( !\App::environment('local') && env('APP_USE_HTTPS', false) || env('APP_USE_HTTPS', false) )
        {
            if ( !$request->secure() )
            {
                return redirect()->secure($request->path(), 301);
            }
        }

        return $next($request);
    }
}
