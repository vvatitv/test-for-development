<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class forceUpdateUsersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if( !$user )
        {
            return $next($request);
        }

        if( !empty($user->need_force_update) && $user->need_force_update == 1 )
        {
            $user->update([
                'need_force_update' => false
            ]);

            event(new \App\Events\UpdateUserInfoEvent($user));
        }

        return $next($request);
    }
}
