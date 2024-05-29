<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class checkLogoutUsers
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if( !$user )
        {
            return $next($request);
        }
        
        if( !empty($user->need_logout) && $user->need_logout == 1 )
        {
            $user->update([
                'need_logout' => 0
            ]);

            if( $user->tokens->count() )
            {
                $user->tokens()->delete();
            }
        
            $user->update([
                'api_token' => null
            ]);

            auth()->guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('home');
        }
        
        return $next($request);
    }
}
