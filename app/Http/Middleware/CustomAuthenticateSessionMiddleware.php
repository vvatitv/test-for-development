<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class CustomAuthenticateSessionMiddleware
{
    protected $auth;

    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        // if vapor/signed-storage/url , just ignore this middleware entirely

        if( $request->has('ignore_web_middleware') || $request->ignore_web_middleware )
        {
            return $next($request);
        }

        if( !$request->hasSession() || !$request->user() )
        {
            return $next($request);
        }

        if( $this->auth->viaRemember() )
        {
            $passwordHash = explode('|', $request->cookies->get($this->auth->getRecallerName()))[2];
            
            if( $passwordHash != $request->user()->getAuthPassword() )
            {
                $this->logout($request);
            }
        }

        if( !$request->session()->has('password_hash') )
        {
            $this->storePasswordHashInSession($request);
        }
        
        if( $request->session()->get('password_hash') !== $request->user()->getAuthPassword() )
        {
            $this->logout($request);
        }

        return tap($next($request), function () use ($request) {
            $this->storePasswordHashInSession($request);
        });
    }

    protected function storePasswordHashInSession($request)
    {
        if ( !$request->user() )
        {
            return;
        }

        $request->session()->put([
            'password_hash' => $request->user()->getAuthPassword(),
        ]);
    }

    protected function logout($request)
    {
        $this->auth->logoutCurrentDevice();
        $request->session()->flush();
        throw new AuthenticationException;
    }
}