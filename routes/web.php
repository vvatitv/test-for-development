<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group([
    // 'middleware' => ['auth', 'verified']
], function(){

    Route::get('/', [
        'as' => 'home',
        'uses' => '\App\Http\Controllers\IndexController@index'
    ]);

    Route::get('home', function(){
        return redirect(route('home'), 301);
    });

    Route::get('bad-browser', [
        'as' => 'bad-browser',
        'uses' => '\App\Http\Controllers\IndexController@BadBrowser'
    ]);

    Route::group([
        'prefix' => 'ratings',
        'as' => 'rating.',
    ], function(){

        Route::get('/', [
            'as' => 'index',
            'uses' => '\App\Http\Controllers\Rating\IndexController@index'
        ]);

    });


    ////////////////////////////////////
    // Этапы конкурса
    ////////////////////////////////////

    Route::group([
        'prefix' => 'steps',
        'as' => 'step.',
    ], function(){

        Route::get('/', [
            'as' => 'index',
            'middleware' => ['auth', 'verified'],
            'uses' => '\App\Http\Controllers\Step\IndexController@index'
        ]);
        
        Route::group([
            'prefix' => '{step:slug}',
            'as' => 'show.',
        ], function(){

            ////////////////////////////////////
            // Рейтинг
            ////////////////////////////////////

            Route::group([
                'prefix' => 'ratings',
                'as' => 'rating.',
            ], function(){
                
                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Step\RatingController@index'
                ]);
                
            });
            
        });

    });

});