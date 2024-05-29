<?php

namespace App\Http\Controllers\Rating;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Support\Facades\Auth;
use App\Models\Step;
use App\Models\Setting;

class IndexController extends Controller
{
    public function __construct()
    {
        SEOMeta::setTitle(env('APP_DEFAULT_SEO_TITLE'));
    }
    
    public function index(Request $request)
    {
        $currentSettingsStep = Setting::where('slug', 'current-step')->first();
        $steps = Step::get();

        switch ($currentSettingsStep->values)
        {
            case 1:

                $step =  $steps->where('id', 1)->first();

            break;
            case 2:

                $step =  $steps->where('id', 1)->first();

            break;
            case 3:

                $step =  $steps->where('id', 2)->first();

            break;
            case 4:

                $step =  $steps->where('id', 3)->first();

            break;
            default:

                $step =  $steps->where('id', 1)->first();

            break;
        }

        return redirect()->route('step.show.rating.index', $step);
    }
}
