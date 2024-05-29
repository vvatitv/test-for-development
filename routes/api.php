<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'prefix' => 'api',
	'as' => 'api.',
], function(){

    Route::get('/', function(){
        return abort(404);
    });

    Route::group([
        'prefix' => 'steps',
        'as' => 'step.',
    ], function(){

        Route::get('/', [
            'as' => 'index',
            'uses' => '\App\Http\Controllers\Api\Steps\IndexController@index'
        ]);

        Route::group([
            'prefix' => '{step:slug}',
            'as' => 'show.',
        ], function(){

            Route::get('/', [
                'as' => 'index',
                'uses' => '\App\Http\Controllers\Api\Steps\IndexController@show'
            ]);

            Route::post('update', [
                'as' => 'update',
                'uses' => '\App\Http\Controllers\Api\Steps\IndexController@update'
            ]);

        });

    });

    Route::group([
        'prefix' => 'tracks',
        'as' => 'track.',
    ], function(){

        Route::get('/', [
            'as' => 'index',
            'uses' => '\App\Http\Controllers\Api\Track\IndexController@index'
        ]);
        
        Route::group([
            'prefix' => 'ideas',
            'as' => 'idea.',
        ], function(){
    
            Route::group([
                'prefix' => 'themes',
                'as' => 'theme.',
            ], function(){
        
                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Track\Idea\Theme\IndexController@index'
                ]);
                
            });
            
        });

    });

    Route::group([
        'prefix' => 'users',
        'as' => 'users.',
    ], function(){

        Route::get('/', [
            'as' => 'index',
            'uses' => '\App\Http\Controllers\Api\Users\IndexController@index'
        ]);

        Route::post('send-notifications', [
            'as' => 'sendnotification',
            'uses' => '\App\Http\Controllers\Api\Users\IndexController@sendNotifications'
        ]);

        Route::post('destroy', [
            'as' => 'destroy',
            'uses' => '\App\Http\Controllers\Api\Users\IndexController@destroyArray'
        ]);

        Route::post('approved', [
            'as' => 'approved',
            'uses' => '\App\Http\Controllers\Api\Users\IndexController@approved'
        ]);

        Route::group([
            'prefix' => 'notifications',
            'as' => 'notification.',
        ], function(){

            Route::post('subscription', [
                'as' => 'subscription',
                'uses' => '\App\Http\Controllers\Api\Users\IndexController@notificationSubscription'
            ]);

            Route::post('unsubscription', [
                'as' => 'unsubscription',
                'uses' => '\App\Http\Controllers\Api\Users\IndexController@notificationUnsubscription'
            ]);

            Route::group([
                'prefix' => 'sends',
                'as' => 'send.',
            ], function(){

                Route::post('registration', [
                    'as' => 'registration',
                    'uses' => '\App\Http\Controllers\Api\Users\NotificationController@registration'
                ]);

            });
        });

        Route::group([
            'prefix' => '{user:slug}',
            'as' => 'show.',
        ], function(){

            Route::get('/', [
                'as' => 'index',
                'uses' => '\App\Http\Controllers\Api\Users\IndexController@show'
            ]);

            Route::post('update', [
                'as' => 'update',
                'uses' => '\App\Http\Controllers\Api\Users\IndexController@update'
            ]);

            Route::post('destroy', [
                'as' => 'destroy',
                'uses' => '\App\Http\Controllers\Api\Users\IndexController@destroy'
            ]);

            Route::group([
                'prefix' => 'teams',
                'as' => 'team.',
            ], function(){

                Route::group([
                    'prefix' => '{team:slug}',
                    'as' => 'show.',
                ], function(){

                    Route::get('set-default', [
                        'as' => 'set-default',
                        'uses' => '\App\Http\Controllers\Api\Users\TeamController@setDefault'
                    ]);

                });

            });

            Route::group([
                'prefix' => 'impersonates',
                'as' => 'impersonate.',
            ], function(){

                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Users\IndexController@impersonateJoin'
                ]);

                Route::get('leave', [
                    'as' => 'leave',
                    'uses' => '\App\Http\Controllers\Api\Users\IndexController@impersonateLeave'
                ]);

            });

        });

    });


    Route::group([
        'prefix' => 'organizations',
        'as' => 'organization.',
    ], function(){

        Route::get('/', [
            'as' => 'index',
            'uses' => '\App\Http\Controllers\Api\Organizations\IndexController@index'
        ]);

    });

    
    Route::group([
        'prefix' => 'teams',
        'as' => 'teams.',
    ], function(){

        Route::get('/', [
            'as' => 'index',
            'uses' => '\App\Http\Controllers\Api\Teams\IndexController@index'
        ]);

        Route::post('create', [
            'as' => 'create',
            'uses' => '\App\Http\Controllers\Api\Teams\IndexController@create'
        ]);

        Route::group([
            'prefix' => 'ideas',
            'as' => 'idea.',
        ], function(){
    
            Route::get('/', [
                'as' => 'index',
                'uses' => '\App\Http\Controllers\Api\Teams\IdeaController@index'
            ]);
    
            Route::post('approved', [
                'as' => 'approved',
                'uses' => '\App\Http\Controllers\Api\Teams\IdeaController@approved'
            ]);
    
            Route::post('reject', [
                'as' => 'reject',
                'uses' => '\App\Http\Controllers\Api\Teams\IdeaController@reject'
            ]);

            Route::post('cancel', [
                'as' => 'cancel',
                'uses' => '\App\Http\Controllers\Api\Teams\IdeaController@cancel'
            ]);

            Route::group([
                'prefix' => 'themes',
                'as' => 'theme.',
            ], function(){
    
                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Teams\IdeaThemeController@index'
                ]);
    
            });

            Route::group([
                'prefix' => '{idea:slug}',
                'as' => 'show.',
            ], function(){

                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Teams\IdeaController@show'
                ]);

            });

        });

        Route::group([
            'prefix' => 'passports',
            'as' => 'passport.',
        ], function(){
    
            Route::get('/', [
                'as' => 'index',
                'uses' => '\App\Http\Controllers\Api\Teams\PassportController@index'
            ]);
    
            Route::post('approved', [
                'as' => 'approved',
                'uses' => '\App\Http\Controllers\Api\Teams\PassportController@approved'
            ]);
    
            Route::post('reject', [
                'as' => 'reject',
                'uses' => '\App\Http\Controllers\Api\Teams\PassportController@reject'
            ]);

            Route::post('cancel', [
                'as' => 'cancel',
                'uses' => '\App\Http\Controllers\Api\Teams\PassportController@cancel'
            ]);

            Route::group([
                'prefix' => 'themes',
                'as' => 'theme.',
            ], function(){
    
                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Teams\PassportThemeController@index'
                ]);
    
            });

            Route::group([
                'prefix' => '{passport:slug}',
                'as' => 'show.',
            ], function(){

                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Teams\PassportController@show'
                ]);

            });

        });

        Route::group([
            'prefix' => 'steps',
            'as' => 'step.',
        ], function(){

            Route::post('update', [
                'as' => 'update',
                'middleware' => 'auth:sanctum',
                'uses' => '\App\Http\Controllers\Api\Teams\StepController@update'
            ]);

            Route::post('attach', [
                'as' => 'attach',
                'middleware' => 'auth:sanctum',
                'uses' => '\App\Http\Controllers\Api\Teams\StepController@attach'
            ]);

            Route::post('detach', [
                'as' => 'detach',
                'middleware' => 'auth:sanctum',
                'uses' => '\App\Http\Controllers\Api\Teams\StepController@detach'
            ]);

        });

        Route::group([
            'prefix' => 'tracks_ideas',
            'as' => 'tracks_idea.',
        ], function(){
        
            Route::post('update', [
                'as' => 'update',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\Idea\IndexController@update'
            ]);

            Route::post('approved', [
                'as' => 'approved',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\Idea\IndexController@approved'
            ]);
    
            Route::post('reject', [
                'as' => 'reject',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\Idea\IndexController@reject'
            ]);

            Route::post('cancel', [
                'as' => 'cancel',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\Idea\IndexController@cancel'
            ]);

        });

        Route::group([
            'prefix' => 'team-track-take-survey',
            'as' => 'teamtracktakesurvey.',
        ], function(){
        
            Route::post('update', [
                'as' => 'update',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@takeSurveyUpdate'
            ]);

            Route::post('approved', [
                'as' => 'approved',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@takeSurveyApproved'
            ]);
    
            Route::post('reject', [
                'as' => 'reject',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@takeSurveyReject'
            ]);

            Route::post('cancel', [
                'as' => 'cancel',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@takeSurveyCancel'
            ]);

        });

        Route::group([
            'prefix' => 'team-track-presentation',
            'as' => 'teamtrackpresentation.',
        ], function(){
        
            Route::post('update', [
                'as' => 'update',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@presentationUpdate'
            ]);

            Route::post('approved', [
                'as' => 'approved',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@presentationApproved'
            ]);
    
            Route::post('reject', [
                'as' => 'reject',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@presentationReject'
            ]);

            Route::post('cancel', [
                'as' => 'cancel',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@presentationCancel'
            ]);

        });

        Route::group([
            'prefix' => 'team-track-selection-case-part-2',
            'as' => 'teamtrackselectioncasepart2.',
        ], function(){
        
            Route::post('update', [
                'as' => 'update',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@selectionCasePart2Update'
            ]);

            Route::post('approved', [
                'as' => 'approved',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@selectionCasePart2Approved'
            ]);
    
            Route::post('reject', [
                'as' => 'reject',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@selectionCasePart2Reject'
            ]);

            Route::post('cancel', [
                'as' => 'cancel',
                'uses' => '\App\Http\Controllers\Api\Teams\Track\ProjectController@selectionCasePart2Cancel'
            ]);

        });

        Route::group([
            'prefix' => '{team:slug}',
            'as' => 'show.',
        ], function(){

            Route::get('/', [
                'as' => 'index',
                'uses' => '\App\Http\Controllers\Api\Teams\IndexController@show'
            ]);

            Route::post('update', [
                'as' => 'update',
                'uses' => '\App\Http\Controllers\Api\Teams\IndexController@update'
            ]);
            

            Route::post('unset-lead', [
                'as' => 'unset-lead',
                'middleware' => 'auth:sanctum',
                'uses' => '\App\Http\Controllers\Api\Teams\IndexController@membersUnSetLead'
            ]);
            

            Route::post('unset-mentor', [
                'as' => 'unset-mentor',
                'middleware' => 'auth:sanctum',
                'uses' => '\App\Http\Controllers\Api\Teams\IndexController@membersUnSetMentor'
            ]);
            
            Route::group([
                'prefix' => 'mentors',
                'as' => 'mentor.',
            ], function(){
                
                Route::group([
                    'prefix' => 'notifications',
                    'as' => 'notification.',
                ], function(){
    
                    Route::post('sending', [
                        'as' => 'sending',
                        'uses' => '\App\Http\Controllers\Api\Teams\Mentor\NotificationController@sending'
                    ]);
    
                });
        
            });

            Route::group([
                'prefix' => 'media',
                'as' => 'media.',
            ], function(){
                
                Route::get('/', function(){
                    return abort(404);
                });

                Route::post('update-video', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'updateVideo',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@videoUpdate'
                ]);

                Route::post('preview', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'preview',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@previewMedia'
                ]);

            });

            Route::group([
                'prefix' => 'values',
                'as' => 'values.',
            ], function(){
                
                Route::get('/', function(){
                    return abort(404);
                });

                Route::post('update', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@updateValues'
                ]);

            });

            Route::group([
                'prefix' => 'tracks',
                'as' => 'track.',
            ], function(){
                
                Route::get('/', function(){
                    return abort(404);
                });

                Route::post('store', [
                    'as' => 'store',
                    'uses' => '\App\Http\Controllers\Api\Teams\Track\IndexController@store'
                ]);

                Route::post('update', [
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\Track\IndexController@update'
                ]);

                Route::group([
                    'prefix' => 'ideas',
                    'as' => 'idea.',
                ], function(){
            
                    Route::post('store', [
                        'as' => 'store',
                        'uses' => '\App\Http\Controllers\Api\Teams\Track\Idea\IndexController@store'
                    ]);
        
                    Route::group([
                        'prefix' => 'themes',
                        'as' => 'theme.',
                    ], function(){
            
                        Route::get('/', [
                            'as' => 'index',
                            'uses' => '\App\Http\Controllers\Api\Teams\Track\Idea\ThemeController@index'
                        ]);
            
                    });
        
                });

            });

            Route::group([
                'prefix' => 'avatar',
                'as' => 'avatar.',
            ], function(){

                Route::get('/', function(){
                    return abort(404);
                });

                Route::post('update', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@updateAvatar'
                ]);

                Route::post('preview', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'preview',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@previewAvatar'
                ]);

            });

            Route::group([
                'prefix' => 'tasks',
                'as' => 'task.',
            ], function(){

                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@tasks'
                ]);

                Route::post('update', [
                    'as' => 'update',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@tasksUpdate'
                ]);

                Route::post('attach', [
                    'as' => 'attach',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@tasksAttach'
                ]);

                Route::post('detach', [
                    'as' => 'detach',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@tasksDetach'
                ]);

            });

            Route::group([
                'prefix' => 'steps',
                'as' => 'step.',
            ], function(){

                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@steps'
                ]);

                Route::post('update', [
                    'as' => 'update',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@stepsUpdate'
                ]);

                Route::post('attach', [
                    'as' => 'attach',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@stepsAttach'
                ]);

                Route::post('detach', [
                    'as' => 'detach',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@stepsDetach'
                ]);

                Route::group([
                    'prefix' => '{step:slug}',
                    'as' => 'show.',
                ], function(){

                    Route::post('update', [
                        'as' => 'update',
                        'middleware' => 'auth:sanctum',
                        'uses' => '\App\Http\Controllers\Api\Teams\IndexController@stepUpdate'
                    ]);

                });

            });

            Route::group([
                'prefix' => 'members',
                'as' => 'member.',
            ], function(){

                Route::get('/', [
                    'as' => 'index',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@members'
                ]);

                Route::post('update', [
                    'as' => 'update',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@membersUpdate'
                ]);

                Route::post('attach', [
                    'as' => 'attach',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@membersAttach'
                ]);

                Route::post('detach', [
                    'as' => 'detach',
                    'middleware' => 'auth:sanctum',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@membersDetach'
                ]);

                Route::group([
                    'prefix' => '{member:slug}',
                    'as' => 'show.',
                ], function(){

                    Route::get('/', function(){
                        return abort(404);
                    });

                    Route::post('set-lead', [
                        'as' => 'set-lead',
                        'middleware' => 'auth:sanctum',
                        'uses' => '\App\Http\Controllers\Api\Teams\IndexController@membersSetLead'
                    ]);

                    Route::post('set-mentor', [
                        'as' => 'set-mentor',
                        'middleware' => 'auth:sanctum',
                        'uses' => '\App\Http\Controllers\Api\Teams\IndexController@membersSetMentor'
                    ]);

                    Route::post('set-participate', [
                        'as' => 'set-participate',
                        'middleware' => 'auth:sanctum',
                        'uses' => '\App\Http\Controllers\Api\Teams\IndexController@membersSetParticipate'
                    ]);

                });

            });

            Route::group([
                'prefix' => 'ideas',
                'as' => 'idea.',
            ], function(){
        
                Route::get('/', function(){
                    return abort(404);
                });
        
                Route::post('update', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@ideasUpdate'
                ]);
                
            });

            Route::group([
                'prefix' => 'passports',
                'as' => 'passport.',
            ], function(){
        
                Route::get('/', function(){
                    return abort(404);
                });
        
                Route::post('update', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@passportsUpdate'
                ]);
                
            });

            Route::group([
                'prefix' => 'projects',
                'as' => 'project.',
            ], function(){
        
                Route::get('/', function(){
                    return abort(404);
                });
        
                Route::post('update', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\IndexController@projectsUpdate'
                ]);
                
            });

            Route::group([
                'prefix' => 'team-track-take-survey',
                'as' => 'teamtracktakesurvey.',
            ], function(){
        
                Route::get('/', function(){
                    return abort(404);
                });
        
                Route::post('update', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\ProjectController@teamtracktakesurveyUpdate'
                ]);
                
            });

            Route::group([
                'prefix' => 'team-track-selection-case-part-2',
                'as' => 'teamtrackselectioncasepart2.',
            ], function(){
        
                Route::get('/', function(){
                    return abort(404);
                });
        
                Route::post('update', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\ProjectController@teamtrackselectioncasepart2Update'
                ]);
                
            });

            Route::group([
                'prefix' => 'team-take-quest',
                'as' => 'teamtakequest.',
            ], function(){
        
                Route::get('/', function(){
                    return abort(404);
                });
        
                Route::post('update', [
                    'middleware' => 'auth:sanctum',
                    'as' => 'update',
                    'uses' => '\App\Http\Controllers\Api\Teams\ProjectController@teamtakequestUpdate'
                ]);
                
            });

        });

    });

});
