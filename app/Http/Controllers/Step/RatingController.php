<?php

namespace App\Http\Controllers\Step;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Support\Facades\Auth;
use App\Models\Step;
use App\Models\Setting;

class RatingController extends Controller
{
    public function __construct()
    {
        SEOMeta::setTitle(env('APP_DEFAULT_SEO_TITLE'));
    }

    public function index(Step $step, Request $request)
    {

        // //
        // $agent = new \Jenssegers\Agent\Agent();
        // dd(
        //     $agent,
        //     $request->ip(),
        //     session(),
        //     env('SESSION_COOKIE'),
        //     config('session.cookie'),
        //     $request->cookie(env('SESSION_COOKIE')),
        //     $request->cookie(env('SESSION_COOKIE'))[config('session.cookie')],
        // );

        SEOMeta::setTitle('Рейтинг ' . $step->id . '-го этапа конкурса');
        // SEOMeta::setDescription('');
        // SEOMeta::setKeywords('');

        $currentStep = Setting::where('slug', 'current-step')->first();
        $page = 1;

        if( $request->filled('page') )
        {
            $page = $request->input('page');
        }

        // switch ($currentStep->values)
        // {
        //     case 3:
        //     case 4:

        //         if( $step->id == $currentStep->values )
        //         {
        //             return redirect()->route('step.show.rating.index', Step::where('id', $step->id - 1)->first());
        //         }

        //     break;
        //     default:
        //     break;
        // }

        if( $step->id > 4 )
        {
            return redirect()->route('step.show.rating.index', Step::where('id', $step->id - 1)->first());
        }
        
        return view('steps.ratings.index', compact('step', 'page'));
    }
}
