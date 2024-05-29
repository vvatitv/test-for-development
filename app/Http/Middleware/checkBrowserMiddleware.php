<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class checkBrowserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if( \App::environment('production') )
        {
            
            $agent = new \Jenssegers\Agent\Agent();
            $browser = $agent->browser();
            $version = $agent->version($browser);

            if( $browser == 'IE' )
            {
                if( $request->path() == 'bad-browser' )
                {
                    return $next($request);
                }
                return redirect()->route('bad-browser');
            }
            
        }

        return $next($request);
    }
}
