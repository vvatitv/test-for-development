<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
        Paginator::useBootstrap();
        Schema::defaultStringLength(255);

        \Carbon\Carbon::setLocale(config('app.locale'));

        if( !\Illuminate\Support\Facades\App::environment('local') && env('APP_USE_HTTPS', false) || env('APP_USE_HTTPS', false) )
        {
            //\Illuminate\Support\Facades\URL::forceSchema('https');
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        RateLimiter::for('EmailNewsletter', function ($job) {
            // return Limit::perMinute(5);
            return Limit::perMinute(25);
        });
        
        RateLimiter::for('EmailNotification', function ($job) {
            // return Limit::perMinute(5);
            return Limit::perMinute(25);
        });

        \Illuminate\Database\Eloquent\Builder::macro('toSqlData', function(){
            return vsprintf(str_replace(['?'], ['\'%s\''], $this->toSql()), $this->getBindings());
        });
    }
}
