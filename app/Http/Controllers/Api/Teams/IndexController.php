<?php

namespace App\Http\Controllers\Api\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

use Arhitector\Yandex\Disk as YandexDiskAPI;
use Illuminate\Support\Facades\Http;
use Laminas\Diactoros\Request as LaminasRequest;
use App\Libraries\Yandex\Disk as YandexDiskHelper;
use Intervention\Image\ImageManager;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Team;
use App\Models\Teams\Idea;
use App\Models\Teams\Passport;
use App\Models\Teams\IdeaTheme;
use App\Models\Teams\PassportTheme;
use App\Models\Task;
use App\Models\User;
use App\Models\Media;
use App\Models\Step;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class IndexController extends BaseController
{
    protected $yandexDisk;
    protected $yandexDiskHelper;
    protected $Intervention;

    public function __construct()
    {
        $this->yandexDisk = new YandexDiskAPI(env('YANDEX_API_DISK_TOKEN', null));
        $this->yandexDiskHelper = new YandexDiskHelper();
        $this->Intervention = new ImageManager([
        	'driver' => 'gd'
        ]);
    }

    public function index(\App\Filters\TeamFilters $filters, Request $request)
    {
        $teams = Team::query();
        
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $teams instanceof Team )
            {
                $teams = $teams->load($request->input('with'));
            }
            elseif( $teams instanceof \Illuminate\Database\Eloquent\Builder )
            {
                $teams = $teams->with($request->input('with'));
            }
            else
            {
                $teams = $teams->each->load($request->input('with'));
            }
        }

        $teams = $teams
                    ->filter($filters)
                    ->distinct();

        $teams = $teams
            ->get();

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $teams instanceof Team )
            {
                $teams = $teams->setAppends($request->input('appends'));
            }
            else
            {
                $teams = $teams->each->setAppends($request->input('appends'));
            }
        }
        


        if( $request->filled('filter') )
        {
            if( \App\Libraries\Helper\JsonHelper::isJSON($request->input('filter')) )
            {
                $param = json_decode($request->input('filter'), true);
            }
            
            if( is_array($param) )
            {
                foreach ($param as $type => $value)
                {
                    switch ($type)
                    {
                        case 'step':

                            $teams = $teams->filter(function($team) use ($value){

                                if( is_array($value) )
                                {
                                    $value = $value['text'];
                                }

                                $step = $team->steps()->where('id', $value)->first();

                                if( !empty($step->pivot->options) )
                                {
                                    if( isset($step->pivot->options['tasks-access']) )
                                    {
                                        if( $step->pivot->options['tasks-access'] == false )
                                        {
                                            return false;
                                        }
                                    }
                                }

                                return true;
                            });

                        break;
                        default:
                        break;
                    }

                }
            }
        }

        if( $request->filled('sortBy') )
        {
            if( \App\Libraries\Helper\JsonHelper::isJSON($request->input('sortBy')) )
            {
                $request->merge([
                    'sortBy' => json_decode($request->input('sortBy'), true)
                ]);
            }

            if( !$request->filled('sortBy.0') )
            {
                $request->merge([
                    'sortBy' => [$request->input('sortBy')]
                ]);
            }
            
            // dd(
            //     $request->input('sortBy')
            // );

            foreach ($request->input('sortBy') as $key => $filterArr)
            {
                if( \App\Libraries\Helper\JsonHelper::isJSON($filterArr) )
                {
                    $filterArr = json_decode($filterArr, true);
                }

                switch($filterArr['field'])
                {
                    case 'id':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return $team->id;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return $team->id;
                                });
                            break;
                        }
                    break;
                    case 'name':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return \Illuminate\Support\Str::upper($team->name);
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return \Illuminate\Support\Str::upper($team->name);
                                });
                            break;
                        }
                    break;
                    case 'organization':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return \Illuminate\Support\Str::upper($team->organization->name);
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return \Illuminate\Support\Str::upper($team->organization->name);
                                });
                            break;
                        }
                    break;
                    case 'members-counts':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return $team->members->count();
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return $team->members->count();
                                });
                            break;
                        }
                    break;
                    case 'lead-full_name':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    if( !empty($team->lead) )
                                    {
                                        $u = $team->lead->withFields()->toArray();

                                        if( !empty($u['full_name']) )
                                        {
                                            return \Illuminate\Support\Str::upper($u['full_name']);
                                        }

                                        $full_name = collect([]);
    
                                        if( !empty($u['last_name']) )
                                        {
                                            $full_name->push($u['last_name']->pivot->value);
                                        }
                            
                                        if( !empty($u['first_name']) )
                                        {
                                            $full_name->push($u['first_name']->pivot->value);
                                        }
                            
                                        if( !empty($u['middle_name']) )
                                        {
                                            $full_name->push($u['middle_name']->pivot->value);
                                        }
                            
                                        if( $full_name->count() )
                                        {
                                            return \Illuminate\Support\Str::upper($full_name->implode(' '));
                                        }
    
                                        return 0;
                                    }
                                    return 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    if( !empty($team->lead) )
                                    {
                                        $u = $team->lead->withFields()->toArray();

                                        if( !empty($u['full_name']) )
                                        {
                                            return \Illuminate\Support\Str::upper($u['full_name']);
                                        }

                                        $full_name = collect([]);
    
                                        if( !empty($u['last_name']) )
                                        {
                                            $full_name->push($u['last_name']->pivot->value);
                                        }
                            
                                        if( !empty($u['first_name']) )
                                        {
                                            $full_name->push($u['first_name']->pivot->value);
                                        }
                            
                                        if( !empty($u['middle_name']) )
                                        {
                                            $full_name->push($u['middle_name']->pivot->value);
                                        }
                            
                                        if( $full_name->count() )
                                        {
                                            return \Illuminate\Support\Str::upper($full_name->implode(' '));
                                        }
    
                                        return 0;
                                    }
                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'photo-start':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return collect($team->tasks)->where('slug', $filterArr['field'])->count();
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return collect($team->tasks)->where('slug', $filterArr['field'])->count();
                                });
                            break;
                        }
                    break;
                    case 'captain-mission':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return collect($team->members)->sum(function ($member) {
                                        return collect($member->tests)->where('name', 'styles-of-leadership')->count() ? 1 : 0;
                                    });
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return collect($team->members)->sum(function ($member) {
                                        return collect($member->tests)->where('name', 'styles-of-leadership')->count() ? 1 : 0;
                                    });
                                });
                            break;
                        }
                    break;
                    case 'team-roles':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return collect($team->members)->sum(function ($member) {
                                        return collect($member->tests)->where('name', 'belbin')->count() ? 1 : 0;
                                    });
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return collect($team->members)->sum(function ($member) {
                                        return collect($member->tests)->where('name', 'belbin')->count() ? 1 : 0;
                                    });
                                });
                            break;
                        }
                    break;
                    case 'time-to-meet':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return collect($team->tasks)->where('slug', $filterArr['field'])->count();
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return collect($team->tasks)->where('slug', $filterArr['field'])->count();
                                });
                            break;
                        }
                    break;
                    case 'values-question':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return collect($team->tasks)->where('slug', $filterArr['field'])->count();
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return collect($team->tasks)->where('slug', $filterArr['field'])->count();
                                });
                            break;
                        }
                    break;
                    case 'team-have-idea':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->idea) ? (int) $team->idea->status_id + 1 : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->idea) ? (int) $team->idea->status_id + 1 : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-idea-voting':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->idea) ? $team->idea->votes->count() : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->idea) ? $team->idea->votes->count() : 0;
                                });
                            break;
                        }
                    break;
                    case 'join-in-tracks':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return $team->tracks->count();
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return $team->tracks->count();
                                });
                            break;
                        }
                    break;
                    case 'team-have-passport':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->passport) ? (int) $team->passport->status_id + 1 : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->passport) ? (int) $team->passport->status_id + 1 : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-have-passport-score':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->passport) ? ( !empty($team->passport->score) ? $team->passport->score : 0 ) : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->passport) ? ( !empty($team->passport->score) ? $team->passport->score : 0 ) : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-have-passport-point':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    if( !collect($team->tasks)->where('slug', 'team-have-passport')->count() )
                                    {
                                        return 0;
                                    }
                                    return !empty(optional(collect($team->tasks)->where('slug', 'team-have-passport')->first())->pivot->options['point']) ? optional(collect($team->tasks)->where('slug', 'team-have-passport')->first())->pivot->options['point'] : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    if( !collect($team->tasks)->where('slug', 'team-have-passport')->count() )
                                    {
                                        return 0;
                                    }
                                    return !empty(optional(collect($team->tasks)->where('slug', 'team-have-passport')->first())->pivot->options['point']) ? optional(collect($team->tasks)->where('slug', 'team-have-passport')->first())->pivot->options['point'] : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-have-passport-w-score':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->passport) && $team->passport->status_id == 1 ? ( !empty($team->passport->score) ? $team->passport->score + 1 : 1 ) : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->passport) && $team->passport->status_id == 1 ? ( !empty($team->passport->score) ? $team->passport->score + 1 : 1 ) : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-take-quiz':

                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    if( empty($team->members) )
                                    {
                                        return -1;
                                    }

                                    $point = 0;

                                    foreach ($team->members as $member)
                                    {
                                        $hasQuizzes = collect($member->quizzes)
                                        ->filter(
                                            function($quizz) use ($filterArr)
                                            {
                                                return $quizz->status_id == 200 && $quizz->quizze->hash == $filterArr['field'];
                                            }
                                        )
                                        ->values();

                                        if( $hasQuizzes->count() )
                                        {
                                            $point = collect($hasQuizzes->first()->answers)->sum('pivot.point');
                                        }
                                    }

                                    return $point;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    
                                    if( empty($team->members) )
                                    {
                                        return -1;
                                    }

                                    $point = 0;

                                    foreach ($team->members as $member)
                                    {
                                        $hasQuizzes = collect($member->quizzes)
                                        ->filter(
                                            function($quizz) use ($filterArr)
                                            {
                                                return $quizz->status_id == 200 && $quizz->quizze->hash == $filterArr['field'];
                                            }
                                        )
                                        ->values();

                                        if( $hasQuizzes->count() )
                                        {
                                            $point = collect($hasQuizzes->first()->answers)->sum('pivot.point');
                                        }
                                    }

                                    return $point;
                                });
                            break;
                        }
                        
                    break;
                    case 'in-person-assignment':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    if( !collect($team->tasks)->where('slug', 'in-person-assignment')->count() )
                                    {
                                        return 0;
                                    }
                                    return !empty(optional(collect($team->tasks)->where('slug', 'in-person-assignment')->first())->pivot->options['point']) ? optional(collect($team->tasks)->where('slug', 'in-person-assignment')->first())->pivot->options['point'] : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    if( !collect($team->tasks)->where('slug', 'in-person-assignment')->count() )
                                    {
                                        return 0;
                                    }
                                    return !empty(optional(collect($team->tasks)->where('slug', 'in-person-assignment')->first())->pivot->options['point']) ? optional(collect($team->tasks)->where('slug', 'in-person-assignment')->first())->pivot->options['point'] : 0;
                                });
                            break;
                        }
                    break;
                    case 'awarding':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    if( !collect($team->tasks)->where('slug', 'awarding')->count() )
                                    {
                                        return 0;
                                    }
                                    return !empty(optional(collect($team->tasks)->where('slug', 'awarding')->first())->pivot->options['position']) ? collect($team->tasks)->where('slug', 'awarding')->first()->pivot->options['position'] : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    if( !collect($team->tasks)->where('slug', 'awarding')->count() )
                                    {
                                        return 0;
                                    }
                                    return !empty(optional(collect($team->tasks)->where('slug', 'awarding')->first())->pivot->options['position']) ? collect($team->tasks)->where('slug', 'awarding')->first()->pivot->options['position'] : 0;
                                });
                            break;
                        }
                    break;
                    case 'tasks-counts':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return (int) collect($team->tasks)->count();
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return (int) collect($team->tasks)->count();
                                });
                            break;
                        }
                    break;
                    case 'team-take-quiz-w-time':
                    break;
                    case 'team-take-quiz-time':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    if( !collect($team->tasks)->where('slug', 'team-take-quiz')->count() )
                                    {
                                        return 0;
                                    }
                                    return !empty(collect($team->tasks)->where('slug', 'team-take-quiz')->first()->pivot->options) ? (int) optional(collect($team->tasks)->where('slug', 'team-take-quiz')->first())->pivot->options['time'] : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    if( !collect($team->tasks)->where('slug', 'team-take-quiz')->count() )
                                    {
                                        return 0;
                                    }
                                    return !empty(collect($team->tasks)->where('slug', 'team-take-quiz')->first()->pivot->options) ? (int) optional(collect($team->tasks)->where('slug', 'team-take-quiz')->first())->pivot->options['time'] : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-take-quiz-w-time-team-have-passport-w-score':
                    break;
                    case 'team-track-project':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->tracksIdea) ? (int) $team->tracksIdea->status_id + 1 : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->tracksIdea) ? (int) $team->tracksIdea->status_id + 1 : 0;
                                });
                            break;
                        }
                    break;
                    case 'tracks-idea-score':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->tracksIdea) ? ( !empty($team->tracksIdea->score) ? $team->tracksIdea->score : 0 ) : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->tracksIdea) ? ( !empty($team->tracksIdea->score) ? $team->tracksIdea->score : 0 ) : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-track-selection-case-part-2-score':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtrackselectioncasepart2) ? ( !empty($team->teamtrackselectioncasepart2->score) ? $team->teamtrackselectioncasepart2->score : 0 ) : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtrackselectioncasepart2) ? ( !empty($team->teamtrackselectioncasepart2->score) ? $team->teamtrackselectioncasepart2->score : 0 ) : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-track-selection-case-part-2':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtrackselectioncasepart2) ? (int) $team->teamtrackselectioncasepart2->status_id + 1 : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtrackselectioncasepart2) ? (int) $team->teamtrackselectioncasepart2->status_id + 1 : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-track-selection-case':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return $team->briefcases->count();
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return $team->briefcases->count();
                                });
                            break;
                        }
                    break;
                    case 'team-track-take-survey':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtracktakesurvey) ? (int) $team->teamtracktakesurvey->status_id + 1 : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtracktakesurvey) ? (int) $team->teamtracktakesurvey->status_id + 1 : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-track-take-survey-score':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtracktakesurvey) ? ( !empty($team->teamtracktakesurvey->score) ? $team->teamtracktakesurvey->score : 0 ) : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtracktakesurvey) ? ( !empty($team->teamtracktakesurvey->score) ? $team->teamtracktakesurvey->score : 0 ) : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-take-quest':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtakequest) ? (int) $team->teamtakequest->status_id + 1 : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtakequest) ? (int) $team->teamtakequest->status_id + 1 : 0;
                                });
                            break;
                        }
                    break;
                    case 'team-take-quest-score':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $teams = $teams->sortByDesc(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtakequest) ? ( !empty($team->teamtakequest->score) ? $team->teamtakequest->score : 0 ) : 0;
                                });
                            break;
                            default:
                                $teams = $teams->sortBy(function($team) use ($request, $filterArr){
                                    return !empty($team->teamtakequest) ? ( !empty($team->teamtakequest->score) ? $team->teamtakequest->score : 0 ) : 0;
                                });
                            break;
                        }
                    break;
                    default:
                    break;
                }
            }
        }

        if( $request->filled('returnAs') )
        {
            switch ($request->input('returnAs'))
            {
                case 'count':
                    return $teams->count();
                break;
                default:
                break;
            }
        }

        if( $request->filled('limit') )
        {

            $teams = $teams->take((int) $request->input('limit'));

        }

        if( $request->filled('pagination') )
        {
            $request->merge([
                'pagination' => json_decode($request->input('pagination'), true)
            ]);

            $teams = $teams->paginate(
                $perPage = ( $request->filled('pagination.perPage') ? $request->input('pagination.perPage') : 15 ),
                $pageName = ( $request->filled('pagination.pageName') ? $request->input('pagination.pageName') : 'page' ),
                $page = ( $request->filled('pagination.page') ? $request->input('pagination.page') : ( $request->filled('page') ? $request->input('page') : null ) )
            );

            return $this->sendResponse($data = new \App\Http\Resources\Team\PaginateResource($teams), $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = \App\Http\Resources\Team\IndexResource::collection($teams), $message = null, $code = 200, $isRaw = false);
    }

    public function show(Team $team, Request $request)
    {
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $team instanceof Team )
            {
                $team = $team->load($request->input('with'));
            }else{
                $team = $team->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $team instanceof Team )
            {
                $team = $team->setAppends($request->input('appends'));
            }else{
                $team = $team->each->setAppends($request->input('appends'));
            }
        }
        
        return $this->sendResponse($data = new \App\Http\Resources\Team\IndexResource($team), $message = null, $code = 200, $isRaw = false);
    }

    public function tasks(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $rows = $team->tasks()->get();
        
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $rows instanceof Task )
            {
                $rows = $rows->load($request->input('with'));
            }else{
                $rows = $rows->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $rows instanceof Task )
            {
                $rows = $rows->setAppends($request->input('appends'));
            }else{
                $rows = $rows->each->setAppends($request->input('appends'));
            }
        }
        
        if( $request->filled('pagination') )
        {
            $request->merge([
                'pagination' => json_decode($request->input('pagination'), true)
            ]);

            $rows = $rows->paginate(
                $perPage = ( $request->filled('pagination.perPage') ? $request->input('pagination.perPage') : 15 ),
                $pageName = ( $request->filled('pagination.pageName') ? $request->input('pagination.pageName') : 'page' ),
                $page = ( $request->filled('pagination.page') ? $request->input('pagination.page') : ( $request->filled('page') ? $request->input('page') : null ) )
            );
            return $this->sendResponse($data = new \App\Http\Resources\Task\PaginateResource($rows), $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = \App\Http\Resources\Task\IndexResource::collection($rows), $message = null, $code = 200, $isRaw = false);
    }

    public function tasksUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'tasks' => 'required|array',
            'tasks.*.slug' => 'required',
            'tasks.*.options' => 'array',
        ], [
            'tasks.required' => 'Список задач команды обязателен для заполнения',
            'tasks.array' => 'Список задач должен быть массивом',
            'tasks.*.slug.required' => 'slug задачи обязателен для заполнения',
            'tasks.*.options.array' => 'Дополнительная информация к задаче должна быть массивом',
        ]);
        
        // if( $team->tasks()->count() )
        // {
        //     $team->tasks()->detach();
        // }

        foreach ($request->input('tasks') as $key => $task)
        {
            $currentTask = Task::where('slug', $task['slug'])->first();

            if( !empty($task['delete']) && $task['delete'] == true )
            {
                if( $team->tasks()->where('slug', $currentTask->slug)->exists() )
                {
                    $team->tasks()->detach($currentTask->id);
                }

            }
            else
            {

                if( $team->tasks()->where('slug', $currentTask->slug)->exists() )
                {
                    switch ($currentTask->slug)
                    {
                        case 'awarding':

                            $task['options']['position'] = (int) $task['options']['position'];

                            if( $task['options']['position'] == 0 )
                            {
                                
                                $team->tasks()->detach($currentTask->id);

                            }else{

                                $team->tasks()->updateExistingPivot($currentTask->id, [
                                    'options' => !empty($task['options']) ? $task['options'] : null
                                ]);

                            }

                        break;
                        default:
                            $team->tasks()->updateExistingPivot($currentTask->id, [
                                'options' => !empty($task['options']) ? $task['options'] : null
                            ]);
                        break;
                    }

                }else{

                    $team->tasks()->attach($currentTask->id, [
                        'options' => $task['options']
                    ]);

                }
                
            }
        }

        event(new \App\Events\UpdateTeamInfoEvent($team));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function tasksAttach(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'tasks' => 'required|array',
            'tasks.*.options' => 'array',
        ], [
            'tasks.required' => 'Список задач команды обязателен для заполнения',
            'tasks.array' => 'Список задач должен быть массивом',
            'tasks.*.options.array' => 'Дополнительная информация к задаче должна быть массивом',
        ]);

        foreach ($request->input('tasks') as $key => $task)
        {
            if( is_integer($task) )
            {
                if( !$team->tasks()->where('id', $task)->count() )
                {
                    $team->tasks()->attach($task);
                }

            }
            else if( is_array($task) )
            {

                if( !empty($task['id']) )
                {
                    if( !empty($task['options']) )
                    {
                        if( $team->tasks()->where('id', $task['id'])->count() )
                        {
                            $team->tasks()->detach($task['id']);
                        }

                        $team->tasks()->attach($task['id'], [
                            'options' => $task['options']
                        ]);

                    }
                    else
                    {

                        if( !$team->tasks()->where('id', $task['id'])->count() )
                        {
                            $team->tasks()->attach($task['id']);
                        }

                    }
                }
            }
        }

        event(new \App\Events\UpdateTeamInfoEvent($team));
    
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function tasksDetach(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'tasks' => 'required|array',
        ], [
            'tasks.required' => 'Список задач команды обязателен для заполнения',
            'tasks.array' => 'Список задач должен быть массивом'
        ]);

        $tasks = Task::where(function($q) use($request) {
            $q->where('slug', $request->input('tasks'))->orWhereIn('id', $request->input('tasks'));
        })->get();

        if( $tasks->count() )
        {
            foreach ($tasks as $ukey => $task)
            {
                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }
            }
    
            event(new \App\Events\UpdateTeamInfoEvent($team));
    
            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
        }

        return $this->sendError('Задачи для добавления отсутствуют или параметр указан неверно', $errorMessages = [], $code = 422);
    }

    public function steps(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $rows = $team->steps()->get();

        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $rows instanceof Step )
            {
                $rows = $rows->load($request->input('with'));
            }else{
                $rows = $rows->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $rows instanceof Step )
            {
                $rows = $rows->setAppends($request->input('appends'));
            }else{
                $rows = $rows->each->setAppends($request->input('appends'));
            }
        }
    
        if( $request->filled('pagination') )
        {
            $request->merge([
                'pagination' => json_decode($request->input('pagination'), true)
            ]);

            $rows = $rows->paginate(
                $perPage = ( $request->filled('pagination.perPage') ? $request->input('pagination.perPage') : 15 ),
                $pageName = ( $request->filled('pagination.pageName') ? $request->input('pagination.pageName') : 'page' ),
                $page = ( $request->filled('pagination.page') ? $request->input('pagination.page') : ( $request->filled('page') ? $request->input('page') : null ) )
            );
            return $this->sendResponse($data = new \App\Http\Resources\Step\PaginateResource($rows), $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = \App\Http\Resources\Step\IndexResource::collection($rows), $message = null, $code = 200, $isRaw = false);
    }

    public function stepUpdate(Team $team, Step $step, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'options' => 'required|array',
        ], [
            'options.required' => 'Список обязателен для заполнения',
            'options.array' => 'Список должен быть массивом'
        ]);

        $team->steps()->updateExistingPivot($step->id, [
            'options' => $request->filled('options') ? $request->input('options') : null
        ]);
    }

    public function stepsUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'steps' => 'required|array',
        ], [
            'steps.required' => 'Список этапов команды обязателен для заполнения',
            'steps.array' => 'Список этапов должен быть массивом'
        ]);

        if( $team->steps()->count() )
        {
            $team->steps()->detach();
        }

        $steps = Step::whereIn('id', $request->input('steps'))->get();

        if( $steps->count() )
        {
            foreach ($steps as $key => $step)
            {
                $team->steps()->attach($step->id, [
                    'options' => $request->filled('options') ? $request->input('options') : null
                ]);
            }
        }
        
        event(new \App\Events\UpdateTeamInfoEvent($team));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function stepsAttach(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'steps' => 'required|array',
        ], [
            'steps.required' => 'Список этапов команды обязателен для заполнения',
            'steps.array' => 'Список этапов должен быть массивом'
        ]);

        $steps = Step::whereIn('id', $request->input('steps'))->get();

        if( $steps->count() )
        {
            foreach ($steps as $ukey => $step)
            {
                if( !$team->steps()->where('id', $step->id)->count() )
                {
                    $team->steps()->attach($step->id,[
                        'options' => $request->filled('options') ? $request->input('options') : null
                    ]);
                }
            }
    
            event(new \App\Events\UpdateTeamInfoEvent($team));
    
            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
        }

        return $this->sendError('Этапы для добавления отсутствуют или параметр указан неверно', $errorMessages = [], $code = 422);
    }

    public function stepsDetach(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'steps' => 'required|array',
        ], [
            'steps.required' => 'Список этапов команды обязателен для заполнения',
            'steps.array' => 'Список этапов должен быть массивом'
        ]);

        $steps = Step::whereIn('id', $request->input('steps'))->get();

        if( $steps->count() )
        {
            foreach ($steps as $ukey => $step)
            {
                if( $team->steps()->where('id', $step->id)->count() )
                {
                    $team->steps()->detach($step);
                }
            }
    
            event(new \App\Events\UpdateTeamInfoEvent($team));
    
            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
        }

        return $this->sendError('Этапы для добавления отсутствуют или параметр указан неверно', $errorMessages = [], $code = 422);
    }

    public function membersUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'members' => 'required|array',
        ], [
            'members.required' => 'Список участников команды обязателен для заполнения',
            'members.array' => 'Список участников должен быть массивом'
        ]);

        $membersToDestroy = $team->members->filter(function($member) use ($request){
            return !collect($request->input('members'))->contains($member->id);
        })->values();

        $membersToState = $team->members->filter(function($member) use ($request){
            return collect($request->input('members'))->contains($member->id);
        })->values();

        $membersToNew = collect(collect($request->input('members'))->filter(function($id) use ($request, $team){
            return !collect($team->members->pluck('id'))->contains($id);
        })->values());
        
        if( $membersToDestroy->count() )
        {
            foreach ($membersToDestroy as $key => $member)
            {
                $team->members()->detach($member);
                event(new \App\Events\UpdateUserInfoEvent($member));
            }
            
            event(new \App\Events\UpdateTeamInfoEvent($team));

            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);

        }

        if( $membersToNew->count() )
        {
            $members = User::whereIn('id', $membersToNew->toArray())->get();

            foreach ($members as $key => $member)
            {
                $team->members()->attach($member);

                event(new \App\Events\UpdateUserInfoEvent($member));
            }

            event(new \App\Events\UpdateTeamInfoEvent($team));

            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
        }


        if( !$membersToNew->count() && !$membersToDestroy->count() )
        {
            event(new \App\Events\UpdateTeamInfoEvent($team));
            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
        }

        return $this->sendError('Участники для добавления отсутствуют или параметр указан неверно', $errorMessages = [], $code = 422);
    }

    public function membersAttach(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'members' => 'required|array',
        ], [
            'members.required' => 'Список участников команды обязателен для заполнения',
            'members.array' => 'Список участников должен быть массивом'
        ]);

        $members = User::whereIn('id', $request->input('members'))->get();

        if( $members->count() )
        {
            foreach ($members as $ukey => $member)
            {
                if( !$team->members()->where('id', $member->id)->count() )
                {
                    $team->members()->attach($member);

                    event(new \App\Events\UpdateUserInfoEvent($member));
                }
            }
    
            event(new \App\Events\UpdateTeamInfoEvent($team));
    
            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
        }

        return $this->sendError('Участники для добавления отсутствуют или параметр указан неверно', $errorMessages = [], $code = 422);
    }

    public function membersDetach(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'members' => 'required|array',
        ], [
            'members.required' => 'Список участников команды обязателен для заполнения',
            'members.array' => 'Список участников должен быть массивом'
        ]);

        $membersToDestroy = $team->members->filter(function($member) use ($request){
            return collect($request->input('members'))->contains($member->id);
        })->values();

        $membersToState = $team->members->filter(function($member) use ($request){
            return !collect($request->input('members'))->contains($member->id);
        })->values();

        if( $membersToDestroy->count() )
        {
            foreach ($membersToDestroy as $key => $member)
            {
                $team->members()->detach($member);
                event(new \App\Events\UpdateUserInfoEvent($member));
            }
            
            event(new \App\Events\UpdateTeamInfoEvent($team));

            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);

        }
        else
        {

            return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
            
        }

        return $this->sendError('Участники для добавления отсутствуют или параметр указан неверно', $errorMessages = [], $code = 422);
    }

    public function membersSetLead(Team $team, User $member, Request $request)
    {
        $authUser = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $authUser->id && !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        if( $team->leads()->count() )
        {
            $team->leads()->detach();
        }

        $team->leads()->attach($member->id);

        event(new \App\Events\UpdateUserInfoEvent($member));
        event(new \App\Events\UpdateTeamInfoEvent($team));
    
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function membersSetMentor(Team $team, User $member, Request $request)
    {
        $authUser = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $authUser->id && !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        if( $team->mentors()->count() )
        {
            $team->mentors()->detach();
        }

        $team->mentors()->attach($member->id);

        event(new \App\Events\UpdateUserInfoEvent($member));
        event(new \App\Events\UpdateTeamInfoEvent($team));
    
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function membersUnSetLead(Team $team, Request $request)
    {
        $authUser = $request->user();

        if( empty($authUser) || !empty($authUser) && !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        if( $team->leads()->count() )
        {
            $team->leads()->detach();
        }

        event(new \App\Events\UpdateTeamInfoEvent($team));
    
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function membersUnSetMentor(Team $team, Request $request)
    {
        $authUser = $request->user();

        if( empty($authUser) || !empty($authUser) && !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        if( $team->mentors()->count() )
        {
            $team->mentors()->detach();
        }

        event(new \App\Events\UpdateTeamInfoEvent($team));
    
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function membersSetParticipate(Team $team, User $member, Request $request)
    {
        $authUser = $request->user();

        if( empty($authUser) || $authUser->id <> $member->id && !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'task' => 'required|array',
            'type' => 'required',
        ], [
            'type.required' => 'Необходимо указать тип запроса',
            'task.required' => 'Необходимо указать задачу',
            'task.array' => 'Задача должна быть массивом',
        ]);

        $task = Task::where('slug', $request->input('task')['slug'])->firstOrFail();

        switch($request->input('type'))
        {
            case 'verify':

                switch ($task->slug)
                {
                    case 'member-invitation-step-4':

                        $certification = \App\Models\Certification::where('slug', 'certificate-invitation-step-4')->first();

                        if( !$member->certifications()->where('cert_id', $certification->id)->exists() )
                        {
                            $cert = $member->certifications()->create([
                                'name' => $certification->name,
                                'description' => $certification->description,
                                'cert_id' => $certification->id
                            ]);

                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Подтверждение участия в 4-м этапе',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Вы подтвердили свое участие в очном мероприятии 4 этапа конкурса “Проектная активация”.</p><p><br></p><p>Подробная информация о мероприятии доступна в приглашении <a href="' . url(route('certificate.show.index', $cert)) . '" target="_blank">по ссылке</a> и в карточке №14 на сайте конкурса в разделе <a href="' . route('team.show.step.show.task.index', [$member->team, $task->steps->first()]) . '">“Задания конкурса”</a></p><p><br></p><p>Если у вас остались вопросы, вы можете обратиться к кураторам в <a href="' . ( $team->tasks()->where('slug', 'telegram-chat-step-4')->count() ? $team->tasks()->where('slug', 'telegram-chat-step-4')->first()->pivot->options['link'] : '#' ) . '" target="_blank">Telegram канал</a> вашей кросс-функциональной команды или в чат Jivo на сайте.</p>',
                                    $button = [
                                        'text' => 'Открыть приглашение',
                                        'url' => url(route('certificate.show.index', $cert)),
                                    ],
                                    $notification_id = null,
                                ))
                            );

                        }

                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                    break;
                    case 'member-invitation':

                        $certification = \App\Models\Certification::where('slug', 'certificate-invitation-step-3')->first();

                        if( !$member->certifications()->where('cert_id', $certification->id)->exists() )
                        {
                            $cert = $member->certifications()->create([
                                'name' => $certification->name,
                                'description' => $certification->description,
                                'cert_id' => $certification->id
                            ]);

                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Подтверждение участия в 3 этапе',
                                    // $message = '<h1>Здравствуйте!</h1><p><br></p><p>Вы подтвердили свое участие в очном мероприятии 3 этапа конкурса “Проектная активация”.</p><p><br></p><p>Подробная информация о мероприятии доступна в приглашении ниже или в карточке №11 на соответствующей <a href="' . route('team.show.step.show.task.index', [$member->team, $task->steps->first()]) . '" target="_blank">странице конкурса “Задания”</a></p><p><br></p><p>Если у вас остались вопросы, вы можете обратиться к куратору в <a href="' . ( $team->tasks()->where('slug', 'telegram-chat')->count() ? $team->tasks()->where('slug', 'telegram-chat')->first()->pivot->options['link'] : '#' ) . '" target="_blank">Telegram канал</a> вашей новой команды.</p>',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Вы подтвердили свое участие в очном мероприятии 3 этапа конкурса “Проектная активация”.</p><p><br></p><p>Подробная информация о мероприятии доступна в приглашении ниже или в карточке №11 на соответствующей <a href="' . route('team.show.step.show.task.index', [$member->team, $task->steps->first()]) . '" target="_blank">странице конкурса “Задания”</a></p>',
                                    $button = [
                                        'text' => 'Открыть приглашение',
                                        'url' => url(route('certificate.show.index', $cert)),
                                    ],
                                    $notification_id = null,
                                ))
                            );

                        }

                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                    break;
                    default:
                    break;
                }

            break;
            case 'reject':

                switch ($task->slug)
                {
                    case 'member-invitation-step-4':

                        $certification = \App\Models\Certification::where('slug', 'certificate-invitation-step-4')->first();

                        if( $member->certifications()->where('cert_id', $certification->id)->count() )
                        {
                            $member->certifications()->where('cert_id', $certification->id)->delete();
                        }

                        if( $team->members()->where('id', $member->id)->exists() )
                        {

                            if( $team->leads()->where('id', $member->id)->exists() )
                            {
                                $team->leads()->detach($member->id);

                                $team->leads()->attach($team->members()->whereNotIn('id', [$member->id])->get()->random(1)->first()->id);
                            }
                            
                            $certParticipant = \App\Models\Certification::where('slug', 'certificate-semifinalist')->first();

                            if( !$member->certifications()->where('cert_id', $certParticipant->id)->exists() )
                            {
                                $cert = $member->certifications()->create([
                                    'name' => $certParticipant->name,
                                    'description' => $certParticipant->description,
                                    'cert_id' => $certParticipant->id
                                ]);
                            }

                            $team->members()->detach($member->id);

                            event(new \App\Events\UpdateUserInfoEvent($member));
                            event(new \App\Events\UpdateTeamInfoEvent($team));

                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                (new \App\Notifications\MessageNotification(
                                    $sender = null,
                                    $subject = 'Отказ от участия в 4 этапе',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Участник "' . $member->full_name. '" из команды "' . $team->name . '" отказался от участия в очном мероприятии 4 этапа</p>',
                                    $button = null,
                                    $notification_id = null,
                                ))
                            );
                        }

                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                    break;
                    case 'member-invitation':

                        $certification = \App\Models\Certification::where('slug', 'certificate-invitation-step-3')->first();

                        if( $member->certifications()->where('cert_id', $certification->id)->count() )
                        {
                            $member->certifications()->where('cert_id', $certification->id)->delete();
                        }

                        if( $team->members()->where('id', $member->id)->exists() )
                        {

                            if( $team->leads()->where('id', $member->id)->exists() )
                            {
                                $team->leads()->detach($member->id);

                                $team->leads()->attach($team->members()->whereNotIn('id', [$member->id])->get()->random(1)->first()->id);
                            }
                            
                            $certParticipant = \App\Models\Certification::where('slug', 'certificate-participant')->first();

                            if( !$member->certifications()->where('cert_id', $certParticipant->id)->exists() )
                            {
                                $cert = $member->certifications()->create([
                                    'name' => $certParticipant->name,
                                    'description' => $certParticipant->description,
                                    'cert_id' => $certParticipant->id
                                ]);
                            }

                            $team->members()->detach($member->id);

                            event(new \App\Events\UpdateUserInfoEvent($member));
                            event(new \App\Events\UpdateTeamInfoEvent($team));

                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                (new \App\Notifications\MessageNotification(
                                    $sender = null,
                                    $subject = 'Отказ от участия в 3 этапе',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Участник "' . $member->full_name. '" из команды "' . $team->name . '" отказался от участия в очном мероприятии 3 этапа</p>',
                                    $button = null,
                                    $notification_id = null,
                                ))
                            );
                        }

                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                    break;
                    default:
                    break;
                }

            break;
            default:
            break;
        }
        
        return $this->sendError('Произошла ошибка, пожалуйста, повторите попытку позднее', $errorMessages = [], $code = 422);
    }

    public function members(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $users = $team->members()->get();

        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $users instanceof User )
            {
                $users = $users->load($request->input('with'));
                
                if( collect($request->input('with'))->contains('fields') )
                {
                    $users = $users->withFields();
                }

            }else{
                $users = $users->each->load($request->input('with'));

                if( collect($request->input('with'))->contains('fields') )
                {
                    $users = $users->each->withFields();
                }
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $users instanceof User )
            {
                $users = $users->setAppends($request->input('appends'));
            }else{
                $users = $users->each->setAppends($request->input('appends'));
            }
        }

        if( $request->filled('returnAs') )
        {
            switch ($request->input('returnAs'))
            {
                case 'count':
                    return $users->count();
                break;
                default:
                break;
            }
        }

        if( $request->filled('pagination') )
        {
            $request->merge([
                'pagination' => json_decode($request->input('pagination'), true)
            ]);

            $users = $users->paginate(
                $perPage = ( $request->filled('pagination.perPage') ? $request->input('pagination.perPage') : 15 ),
                $pageName = ( $request->filled('pagination.pageName') ? $request->input('pagination.pageName') : 'page' ),
                $page = ( $request->filled('pagination.page') ? $request->input('pagination.page') : ( $request->filled('page') ? $request->input('page') : null ) )
            );
            return $this->sendResponse($data = new \App\Http\Resources\User\PaginateResource($users), $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = \App\Http\Resources\User\IndexResource::collection($users), $message = null, $code = 200, $isRaw = false);
    }

    public function previewMedia(Team $team, Request $request)
    {

        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'file' => 'required|file',
        ], [
            'file.required' => 'Необходимо загрузить файл',
            'file.file' => 'Переданный параметр должен быть файлом',
        ]);

        if( $request->filled('task') )
        {
            
            switch ($request->input('task')['slug'])
            {
                case 'team-have-idea':
                case 'team-have-passport':

                    $this->validate($request, [
                        'file' => 'required',
                    ],[
                        'file.required' => 'Необходимо прикрепить файл для загрузки',
                        'file.file' => 'Переданный параметр должен быть файлом',
                        'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    ]);

                break;
                default:
                break;
            }
        }

        if( $request->hasFile('file') && $request->file('file')->isValid() )
        {
            $UploadedFile = $request->file('file');
                
            do{
                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
            }while( Storage::disk('local')->exists('public/uploads/tmp/' . $UploadedFileNewName) );

            if( !$UploadedFile->storePubliclyAs('/uploads/tmp/', $UploadedFileNewName, 'public') )
            {
                return $this->sendError('Неудалось загрузить файл, пожалуйста повторите', $errorMessages = [], $code = 422);
            }

            $filesArrayComplete = [
                'url' => Storage::disk('public')->url('/uploads/tmp/' . $UploadedFileNewName),
                'name' => $UploadedFile->getClientOriginalName(),
                'mine' => $UploadedFile->getClientMimeType(),
                'extension' => $UploadedFile->getClientOriginalExtension(),
            ];
            return $this->sendResponse($data = $filesArrayComplete, $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
    }

    public function videoUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            // 'type' => 'required',
            'url' => 'required|url',
        ], [
            'url.required' => 'Необходимо указать ссылку',
            'url.url' => 'Переданный параметр должен быть ссылкой',
        ]);

        if( preg_match("/https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube(?:-nocookie)?\.com\S*?[^\w\s-])([\w-]{11})(?=[^\w-]|$)(?![?=&+%\w.-]*(?:['\"][^<>]*>|<\/a>))[?=&+%\w.-]*/i", $request->input('url')) )
        {
            // Youtube
            
            $VideoThumb = new \App\Libraries\Yandex\VideoThumb($request->input('url'));
            
            do{
                $fuid = \Illuminate\Support\Str::uuid();
            }while( Storage::disk('local')->exists('public/uploads/teams/' . $fuid . '.jpg') );

            $VideoThumb->fetchImage(Storage::disk('local')->path('public/uploads/teams/' . $fuid . '.jpg'));

            $team->media()->create([
                'name' => $team->name,
                'type' => 'videocard',
                'user_id' => $request->user()->id,
                'collection' => 'teamvideocard',
                'extension' => (string) Str::contains($request->input('url'), '/shorts/') ? 'youtubeshorts' : 'youtube',
                'thumbnails' => json_encode([
                    'original' => [
                        'name' => $fuid,
                        'extension' => 'jpg',
                        'mimes' => 'image/jpeg',
                        'src' => $fuid . '.jpg',
                        'user_id' => $request->user()->id,
                        'disk' => 'public',
                        'type' => 'thumbnail',
                        'tag' => 'original',
                        'collection' => 'teamvideocard',
                        'folder' => '/uploads/teams/'
                    ]
                ]),
                'src' => urldecode($request->input('url'))
            ]);

            event(new \App\Events\UpdateTeamInfoEvent($team));

            if( !$team->tasks()->where('slug', 'time-to-meet')->exists() )
            {
                $team->tasks()->attach(Task::where('slug', 'time-to-meet')->first()->id);
            }

            \App\Jobs\EmailUsersNotificationJob::dispatch(
                \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                (new \App\Notifications\MessageNotification(
                    $sender = null,
                    $subject = 'Новое видео',
                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилась новое видео от команды ' . $team->name . '</p>',
                    $button = [
                        'text' => 'Посмотреть',
                        'url' => url(route('team.show.index', $team)),
                    ],
                    $notification_id = null,
                ))
            );
            
            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

        }elseif ( preg_match("/https?:\/\/(?:[0-9A-Z-]+\.)?(?:yadi\.sk\/|yandex\.ru*?[^\w\s-])[a-z]\/(.*)/i", $request->input('url')) ) {
            
            // Яндекс диск
            
            preg_match("/https?:\/\/(?:[0-9A-Z-]+\.)?(?:yadi\.sk\/|yandex\.ru*?[^\w\s-])[a-z]\/(.*)/i", $request->input('url'), $matches);

            $YandexVideoData = $this->yandexDiskHelper->getFileInfo('https://disk.yandex.ru/i/' . $matches[1]);

            do{
                $fuid = \Illuminate\Support\Str::uuid();
            }while( Storage::disk('local')->exists('public/uploads/teams/' . $fuid . '.jpg') );

            $statusImage = $this->yandexDiskHelper->downloadRemoveFile($YandexVideoData['preview'], $fuid . '.jpg', Storage::disk('local')->path('public/uploads/tmp'));
            $thumbnails = null;

            if( $statusImage == 200 )
            {
                Storage::disk('local')->move('public/uploads/tmp/' . $fuid . '.jpg', 'public/uploads/teams/' . $fuid . '.jpg');

                $thumbnails = json_encode([
                    'original' => [
                        'name' => $fuid,
                        'extension' => 'jpg',
                        'mimes' => 'image/jpeg',
                        'src' => $fuid . '.jpg',
                        'user_id' => $request->user()->id,
                        'disk' => 'public',
                        'type' => 'thumbnail',
                        'tag' => 'original',
                        'collection' => 'teamvideocard',
                        'folder' => '/uploads/teams/'
                    ]
                ]);
            }
            
            $team->media()->create([
                'name' => $team->name,
                'type' => 'videocard',
                'user_id' => $request->user()->id,
                'collection' => 'teamvideocard',
                'extension' => 'yandex_disk',
                'thumbnails' => $thumbnails,
                'src' => urldecode($request->input('url'))
            ]);
            
            event(new \App\Events\UpdateTeamInfoEvent($team));

            if( !$team->tasks()->where('slug', 'time-to-meet')->exists() )
            {
                $team->tasks()->attach(Task::where('slug', 'time-to-meet')->first()->id);
            }

            \App\Jobs\EmailUsersNotificationJob::dispatch(
                \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                (new \App\Notifications\MessageNotification(
                    $sender = null,
                    $subject = 'Новое видео',
                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилась новое видео от команды ' . $team->name . '</p>',
                    $button = [
                        'text' => 'Посмотреть',
                        'url' => url(route('team.show.index', $team)),
                    ],
                    $notification_id = null,
                ))
            );

            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

        }elseif( preg_match("@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i", $request->input('url')) ){

            // google диск
            
            preg_match("@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i", $request->input('url'), $matches);

            // $GoogleDriveVideoData = 'https://drive.google.com/thumbnail?id=' . $matches[1] . '&authuser=0&sz=w1920-h1080-k-pd';
            $GoogleDriveVideoData = 'https://lh3.googleusercontent.com/d/' . $matches[1] . '=w1920-h1080-k-pd?authuser=0';

            do{
                $fuid = \Illuminate\Support\Str::uuid();
            }while( Storage::disk('local')->exists('public/uploads/teams/' . $fuid . '.jpg') );

            $statusImage = $this->yandexDiskHelper->downloadRemoveFile($GoogleDriveVideoData, $fuid . '.jpg', Storage::disk('local')->path('public/uploads/tmp'));
            $thumbnails = null;
            
            if( $statusImage == 200 )
            {
                Storage::disk('local')->move('public/uploads/tmp/' . $fuid . '.jpg', 'public/uploads/teams/' . $fuid . '.jpg');

                $thumbnails = json_encode([
                    'original' => [
                        'name' => $fuid,
                        'extension' => 'jpg',
                        'mimes' => 'image/jpeg',
                        'src' => $fuid . '.jpg',
                        'user_id' => $request->user()->id,
                        'disk' => 'public',
                        'type' => 'thumbnail',
                        'tag' => 'original',
                        'collection' => 'teamvideocard',
                        'folder' => '/uploads/teams/'
                    ]
                ]);
            }

            $team->media()->create([
                'name' => $team->name,
                'type' => 'videocard',
                'user_id' => $request->user()->id,
                'collection' => 'teamvideocard',
                'extension' => 'google_disk',
                'thumbnails' => $thumbnails,
                'src' => urldecode('https://drive.google.com/file/d/' . $matches[1] . '/view')
            ]);

            event(new \App\Events\UpdateTeamInfoEvent($team));

            if( !$team->tasks()->where('slug', 'time-to-meet')->exists() )
            {
                $team->tasks()->attach(Task::where('slug', 'time-to-meet')->first()->id);
            }


            \App\Jobs\EmailUsersNotificationJob::dispatch(
                \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                (new \App\Notifications\MessageNotification(
                    $sender = null,
                    $subject = 'Новое видео',
                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилась новое видео от команды ' . $team->name . '</p>',
                    $button = [
                        'text' => 'Посмотреть',
                        'url' => url(route('team.show.index', $team)),
                    ],
                    $notification_id = null,
                ))
            );

            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

        }

        throw \Illuminate\Validation\ValidationException::withMessages(['url' => 'Указана неправильная ссылка']);
    }

    public function passportsUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        if( $user->hasRole('admin') )
        {
            $team->passport->update($request->only($team->passport->getFillable()));

            if( $request->filled('score') )
            {
                if( !$team->tasks()->where('slug', 'team-have-passport')->exists() )
                {

                    $team->tasks()->attach(Task::where('slug', 'team-have-passport')->first()->id, [
                        'options' => [
                            'point' => $request->input('score')
                        ]
                    ]);

                }else{
                    
                    $team->tasks()->updateExistingPivot(Task::where('slug', 'team-have-passport')->first()->id, [
                        'options' => [
                            'point' => $request->input('score')
                        ]
                    ]);

                }
            }

            if( $request->filled('theme') )
            {
                $this->validate($request, [
                    'theme' => 'required|array',
                ], [
                    'theme.array' => 'Значение темы должно быть массивом или объектом'
                ]);

                $theme = PassportTheme::find($request->input('theme')['id']);

                if( empty($theme) )
                {
                    return $this->sendError('Указанная тема не знайдена', $errorMessages = [], $code = 422);
                }

                if( $team->passport->themes->count() )
                {
                    $team->passport->themes()->detach();
                }
                $team->passport->themes()->attach($theme->id);
            }

            if( $request->hasFile('file') )
            {
                $this->validate($request, [
                    'file' => 'required|mimes:pdf',
                ], [
                    'file.required' => 'Необходимо прикрепить файл для загрузки',
                    'file.file' => 'Переданный параметр должен быть файлом',
                    'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                ]);

                if( $request->file('file')->isValid() )
                {
                    if( $team->passport->media->count() )
                    {
                        foreach ($team->passport->media as $mkey => $media)
                        {
                            if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                            {
                                Storage::disk($media->disk)->delete($media->folder . $media->src);
                            }

                            $media->delete();
                        }
                    }

                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                    
        
                    $data = [
                        'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                        'extension' => $UploadedFile->getClientOriginalExtension(),
                        'mimes' => $UploadedFile->getMimeType(),
                        'src' => $UploadedFileNewName,
                        'parent_id' => null,
                        'user_id' => $user->id,
                        'disk' => 'public',
                        'type' => 'original',
                        'tag' => null,
                        'size' => $UploadedFile->getSize(),
                        'width' => null,
                        'height' => null,
                        'collection' => null,
                        'folder' => '/uploads/teams/'
                    ];
                    
                    if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                    {
                        $team->passport->media()->create($data);
                    }

                }
            }

            event(new \App\Events\UpdateTeamInfoEvent($team));
            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

        }else{

            if( !empty($team->passport) )
            {
                $this->validate($request, [
                    'name' => 'required',
                    'description' => 'required',
                    'theme' => 'required',
                ], [
                    'name.required' => 'Необходимо указать название паспорта',
                    'description.required' => 'Необходимо дать описание паспорта',
                    'theme.required' => 'Необходимо выбрать тематику проекта',
                ]);

                $team->passport()->update([
                    'name' => $request->filled('name') ? $request->input('name') : $team->name,
                    'description' => $request->filled('description') ? $request->input('description') : null,
                    'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
                ]);

                if( $team->passport->themes->count() )
                {
                    $team->passport->themes()->detach();
                }

                $team->passport->themes()->attach($request->input('theme')['id']);

                if( $request->hasFile('file') )
                {
                    $this->validate($request, [
                        'file' => 'required|mimes:pdf',
                    ], [
                        'file.required' => 'Необходимо прикрепить файл для загрузки',
                        'file.file' => 'Переданный параметр должен быть файлом',
                        'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    ]);
                }

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {

                    if( $team->passport->media->count() )
                    {
                        foreach ($team->passport->media as $mkey => $media)
                        {
                            if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                            {
                                Storage::disk($media->disk)->delete($media->folder . $media->src);
                            }

                            $media->delete();
                        }
                    }

                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                    
        
                    $data = [
                        'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                        'extension' => $UploadedFile->getClientOriginalExtension(),
                        'mimes' => $UploadedFile->getMimeType(),
                        'src' => $UploadedFileNewName,
                        'parent_id' => null,
                        'user_id' => $user->id,
                        'disk' => 'public',
                        'type' => 'original',
                        'tag' => null,
                        'size' => $UploadedFile->getSize(),
                        'width' => null,
                        'height' => null,
                        'collection' => null,
                        'folder' => '/uploads/teams/'
                    ];
                    
                    if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                    {
                        $team->passport->media()->create($data);
                    }

                }

                event(new \App\Events\UpdateTeamInfoEvent($team));

                if( $team->members->count() && $team->idea->status_id == 0 )
                {
                    foreach ($team->members as $key => $member)
                    {
                        if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                        {
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Ваш паспорт проекта на проверке',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваш паспорт проекта отправлен на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                    $button = [
                                        'text' => 'Перейти в кабинет команды',
                                        'url' => url(route('team.show.index', $member->team)),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
                    }

                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                        \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                        (new \App\Notifications\MessageNotification(
                            $sender = null,
                            $subject = 'Новый паспорт проекта',
                            $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появился новый паспорт проекта от команды "' . $team->name . '"</p>',
                            $button = [
                                'text' => 'Посмотреть',
                                'url' => url(route('dashboard.tasks.index')),
                            ],
                            $notification_id = null,
                        ))
                    );
                }

                return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

            }else{

                $this->validate($request, [
                    'name' => 'required',
                    'description' => 'required',
                    'theme' => 'required',
                    'file' => 'required|file|mimes:pdf',
                ], [
                    'name.required' => 'Необходимо указать название паспорта',
                    'description.required' => 'Необходимо дать описание паспорта',
                    'theme.required' => 'Необходимо выбрать тематику проекта',
                    'file.required' => 'Необходимо загрузить файл',
                    'file.file' => 'Переданный параметр должен быть файлом',
                ]);
        
        
                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    $passport = $team->passport()->create([
                        'name' => $request->filled('name') ? $request->input('name') : $team->name,
                        'description' => $request->filled('description') ? $request->input('description') : null,
                        'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
                    ]);
                    
                    $passport->themes()->attach($request->input('theme')['id']);

                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                    
        
                    $data = [
                        'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                        'extension' => $UploadedFile->getClientOriginalExtension(),
                        'mimes' => $UploadedFile->getMimeType(),
                        'src' => $UploadedFileNewName,
                        'parent_id' => null,
                        'user_id' => $user->id,
                        'disk' => 'public',
                        'type' => 'original',
                        'tag' => null,
                        'size' => $UploadedFile->getSize(),
                        'width' => null,
                        'height' => null,
                        'collection' => null,
                        'folder' => '/uploads/teams/'
                    ];
                    
                    if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                    {
                        $passport->media()->create($data);
                    }
        
                    event(new \App\Events\UpdateTeamInfoEvent($team));
        
                    // if( !$team->tasks()->where('slug', 'team-have-passport')->exists() )
                    // {
                    //     $team->tasks()->attach(Task::where('slug', 'team-have-passport')->first()->id);
                    // }
        
                    if( $team->members->count() )
                    {
                        foreach ($team->members as $key => $member)
                        {
                            if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                            {
                                \App\Jobs\EmailUsersNotificationJob::dispatch(
                                    $member,
                                    (new \App\Notifications\MessageNotification(
                                        $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                        $subject = 'Ваш паспорт проекта на проверке',
                                        $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваш паспорт проекта отправлен на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                        $button = [
                                            'text' => 'Перейти в кабинет команды',
                                            'url' => url(route('team.show.index', $member->team)),
                                        ],
                                        $notification_id = null,
                                    ))
                                );
                            }
                        }
    
                        \App\Jobs\EmailUsersNotificationJob::dispatch(
                            \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                            (new \App\Notifications\MessageNotification(
                                $sender = null,
                                $subject = 'Новый паспорт проекта',
                                $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появился новый паспорт проекта от команды "' . $team->name . '"</p>',
                                $button = [
                                    'text' => 'Посмотреть',
                                    'url' => url(route('dashboard.tasks.index')),
                                ],
                                $notification_id = null,
                            ))
                        );
                    }

                    return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
                }
            }
        }
        return $this->sendError('Произошла ошибка, пожалуйста, повторите попытку позднее', $errorMessages = [], $code = 422);
    }

    public function ideasUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        if( $user->hasRole('admin') && !empty($team->idea) )
        {
            $team->idea->update($request->only($team->idea->getFillable()));

            if( $request->filled('theme') )
            {
                $this->validate($request, [
                    'theme' => 'required|array',
                ], [
                    'theme.array' => 'Значение темы должно быть массивом или объектом'
                ]);

                $theme = IdeaTheme::find($request->input('theme')['id']);

                if( empty($theme) )
                {
                    return $this->sendError('Указанная тема не знайдена', $errorMessages = [], $code = 422);
                }

                if( $team->idea->themes->count() )
                {
                    $team->idea->themes()->detach();
                }
                $team->idea->themes()->attach($theme->id);
            }

            if( $request->hasFile('file') )
            {
                $this->validate($request, [
                    // 'file' => 'required|file|mimes:pdf',
                    'file' => 'required|file|max:2048',
                ], [
                    'file.required' => 'Необходимо прикрепить файл для загрузки',
                    'file.file' => 'Переданный параметр должен быть файлом',
                    'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                ]);

                if( $request->file('file')->isValid() )
                {

                    if( $team->idea->media->count() )
                    {
                        foreach ($team->idea->media as $mkey => $media)
                        {
                            if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                            {
                                Storage::disk($media->disk)->delete($media->folder . $media->src);
                            }

                            $media->delete();
                        }
                    }

                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                    
        
                    $data = [
                        'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                        'extension' => $UploadedFile->getClientOriginalExtension(),
                        'mimes' => $UploadedFile->getMimeType(),
                        'src' => $UploadedFileNewName,
                        'parent_id' => null,
                        'user_id' => $user->id,
                        'disk' => 'public',
                        'type' => 'original',
                        'tag' => null,
                        'size' => $UploadedFile->getSize(),
                        'width' => null,
                        'height' => null,
                        'collection' => null,
                        'folder' => '/uploads/teams/'
                    ];
                    
                    if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                    {
                        $team->idea->media()->create($data);
                    }

                }
            }

            event(new \App\Events\UpdateTeamInfoEvent($team));

            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

        }
        else
        {
            if( !empty($team->idea) )
            {
                $this->validate($request, [
                    'name' => 'required',
                    'description' => 'required',
                    'theme' => 'required',
                ], [
                    'name.required' => 'Необходимо указать название идеи',
                    'description.required' => 'Необходимо дать описание идеи',
                    'theme.required' => 'Необходимо выбрать тематику проекта',
                ]);

                $team->idea()->update([
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
                ]);

                if( $team->idea->themes->count() )
                {
                    $team->idea->themes()->detach();
                }

                $team->idea->themes()->attach($request->input('theme')['id']);

                if( $request->hasFile('file') )
                {
                    $this->validate($request, [
                        // 'file' => 'required|file|mimes:pdf',
                        'file' => 'required|file|max:2048',
                    ], [
                        'file.required' => 'Необходимо прикрепить файл для загрузки',
                        'file.file' => 'Переданный параметр должен быть файлом',
                        'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    ]);
                }

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    if( $team->idea->media->count() )
                    {
                        foreach ($team->idea->media as $mkey => $media)
                        {
                            if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                            {
                                Storage::disk($media->disk)->delete($media->folder . $media->src);
                            }

                            $media->delete();
                        }
                    }

                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                    
        
                    $data = [
                        'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                        'extension' => $UploadedFile->getClientOriginalExtension(),
                        'mimes' => $UploadedFile->getMimeType(),
                        'src' => $UploadedFileNewName,
                        'parent_id' => null,
                        'user_id' => $user->id,
                        'disk' => 'public',
                        'type' => 'original',
                        'tag' => null,
                        'size' => $UploadedFile->getSize(),
                        'width' => null,
                        'height' => null,
                        'collection' => null,
                        'folder' => '/uploads/teams/'
                    ];
                    
                    if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                    {
                        $team->idea->media()->create($data);
                    }

                }

                if( !$team->tasks()->where('slug', 'team-have-idea')->count() )
                {
                    $team->tasks()->attach(Task::where('slug', 'team-have-idea')->first()->id);
                }

                
            
                if( $team->members->count() && $team->idea->status_id == 0 )
                {
                    foreach ($team->members as $key => $member)
                    {
                        if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                        {
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Ваша идея проекта на проверке',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваша идея проекта отправлена на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                    $button = [
                                        'text' => 'Перейти в кабинет команды',
                                        'url' => url(route('team.show.index', $member->team)),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
                    }

                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                        \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                        (new \App\Notifications\MessageNotification(
                            $sender = null,
                            $subject = 'Новая идея',
                            $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилась новая идея от команды ' . $team->name . '</p>',
                            $button = [
                                'text' => 'Посмотреть',
                                'url' => url(route('dashboard.tasks.index')),
                            ],
                            $notification_id = null,
                        ))
                    );
                }
                
                event(new \App\Events\UpdateTeamInfoEvent($team));
                
                return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

            }
            else
            {
                $this->validate($request, [
                    'name' => 'required',
                    'description' => 'required',
                    'theme' => 'required',
                    // 'file' => 'required|file|mimes:pdf',
                ], [
                    'name.required' => 'Необходимо указать название идеи',
                    'description.required' => 'Необходимо дать описание идеи',
                    'theme.required' => 'Необходимо выбрать тематику проекта',
                    // 'file.required' => 'Необходимо загрузить файл',
                    // 'file.file' => 'Переданный параметр должен быть файлом',
                ]);
        
                $idea = $team->idea()->create([
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
                ]);
    
                $idea->themes()->attach($request->input('theme')['id']);

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    $this->validate($request, [
                        // 'file' => 'required|file|mimes:pdf',
                        'file' => 'required|file|max:2048',
                    ], [
                        'file.required' => 'Необходимо прикрепить файл для загрузки',
                        'file.file' => 'Переданный параметр должен быть файлом',
                        'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    ]);
                    
                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                    
        
                    $data = [
                        'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                        'extension' => $UploadedFile->getClientOriginalExtension(),
                        'mimes' => $UploadedFile->getMimeType(),
                        'src' => $UploadedFileNewName,
                        'parent_id' => null,
                        'user_id' => $user->id,
                        'disk' => 'public',
                        'type' => 'original',
                        'tag' => null,
                        'size' => $UploadedFile->getSize(),
                        'width' => null,
                        'height' => null,
                        'collection' => null,
                        'folder' => '/uploads/teams/'
                    ];
                    
                    if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                    {
                        $idea->media()->create($data);
                    }
                }

                if( !$team->tasks()->where('slug', 'team-have-idea')->count() )
                {
                    $team->tasks()->attach(Task::where('slug', 'team-have-idea')->first()->id);
                }

                if( $team->members->count() )
                {
                    foreach ($team->members as $key => $member)
                    {
                        if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                        {
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Ваша идея проекта на проверке',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваша идея проекта отправлена на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                    $button = [
                                        'text' => 'Перейти в кабинет команды',
                                        'url' => url(route('team.show.index', $member->team)),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
                    }

                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                        \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                        (new \App\Notifications\MessageNotification(
                            $sender = null,
                            $subject = 'Новая идея',
                            $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилась новая идея от команды ' . $team->name . '</p>',
                            $button = [
                                'text' => 'Посмотреть',
                                'url' => url(route('dashboard.tasks.index')),
                            ],
                            $notification_id = null,
                        ))
                    );
                }
                
                event(new \App\Events\UpdateTeamInfoEvent($team));

                return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
                
            }

        }

        return $this->sendError('Произошла ошибка, пожалуйста, повторите попытку позднее', $errorMessages = [], $code = 422);

    }

    public function projectsUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'task' => 'required|array'
        ], [
            'task.required' => 'Необходимо указать задачу',
            'task.array' => 'Задачу должна быть массивом',
        ]);

        $task = Task::where('slug', $request->input('task')['slug'])->first();

        if( $user->hasRole('admin') )
        {
            switch ($task->slug)
            {
                case 'team-have-idea':

                    $team->idea->update($request->only($team->idea->getFillable()));

                    if( $request->filled('theme') )
                    {
                        $this->validate($request, [
                            'theme' => 'required|array',
                        ], [
                            'theme.array' => 'Значение темы должно быть массивом или объектом'
                        ]);
        
                        $theme = IdeaTheme::find($request->input('theme')['id']);
        
                        if( empty($theme) )
                        {
                            return $this->sendError('Указанная тема не знайдена', $errorMessages = [], $code = 422);
                        }
        
                        if( $team->idea->themes->count() )
                        {
                            $team->idea->themes()->detach();
                        }
                        $team->idea->themes()->attach($theme->id);
                    }
        
                    if( $request->hasFile('file') )
                    {
                        $this->validate($request, [
                            'file' => 'required|file|mimes:pdf',
                        ], [
                            'file.required' => 'Необходимо прикрепить файл для загрузки',
                            'file.file' => 'Переданный параметр должен быть файлом',
                            'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                        ]);
        
                        if( $request->file('file')->isValid() )
                        {
        
                            if( $team->idea->media->count() )
                            {
                                foreach ($team->idea->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->idea->media()->create($data);
                            }
        
                        }
                    }
        
                    event(new \App\Events\UpdateTeamInfoEvent($team));
        
                    return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                break;
                case 'team-have-passport':

                    $team->passport->update($request->only($team->passport->getFillable()));

                    if( $request->filled('theme') )
                    {
                        $this->validate($request, [
                            'theme' => 'required|array',
                        ], [
                            'theme.array' => 'Значение темы должно быть массивом или объектом'
                        ]);
        
                        $theme = PassportTheme::find($request->input('theme')['id']);
        
                        if( empty($theme) )
                        {
                            return $this->sendError('Указанная тема не знайдена', $errorMessages = [], $code = 422);
                        }
        
                        if( $team->passport->themes->count() )
                        {
                            $team->passport->themes()->detach();
                        }
                        $team->passport->themes()->attach($theme->id);
                    }
        
                    if( $request->hasFile('file') )
                    {
                        $this->validate($request, [
                            'file' => 'required|mimes:pdf',
                        ], [
                            'file.required' => 'Необходимо прикрепить файл для загрузки',
                            'file.file' => 'Переданный параметр должен быть файлом',
                            'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                        ]);
        
                        if( $request->file('file')->isValid() )
                        {
                            if( $team->passport->media->count() )
                            {
                                foreach ($team->passport->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->passport->media()->create($data);
                            }
        
                        }
                    }
        
                    event(new \App\Events\UpdateTeamInfoEvent($team));

                    return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                break;
                case 'roadmap':

                    $team->roadmap->update($request->only($team->roadmap->getFillable()));
        
                    if( $request->hasFile('file') )
                    {
                        $this->validate($request, [
                            'file' => 'required|file',
                        ], [
                            'file.required' => 'Необходимо прикрепить файл для загрузки',
                            'file.file' => 'Переданный параметр должен быть файлом',
                            'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                        ]);
        
                        if( $request->file('file')->isValid() )
                        {
                            if( $team->roadmap->media->count() )
                            {
                                foreach ($team->roadmap->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->roadmap->media()->create($data);
                            }
        
                        }
                    }
        
                    event(new \App\Events\UpdateTeamInfoEvent($team));

                    return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                break;
                case 'risk-matrix':

                    $team->riskmatrix->update($request->only($team->riskmatrix->getFillable()));
        
                    if( $request->hasFile('file') )
                    {
                        $this->validate($request, [
                            'file' => 'required|file',
                        ], [
                            'file.required' => 'Необходимо прикрепить файл для загрузки',
                            'file.file' => 'Переданный параметр должен быть файлом',
                            'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                        ]);
        
                        if( $request->file('file')->isValid() )
                        {
                            if( $team->riskmatrix->media->count() )
                            {
                                foreach ($team->riskmatrix->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->riskmatrix->media()->create($data);
                            }
        
                        }
                    }
        
                    event(new \App\Events\UpdateTeamInfoEvent($team));

                    return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                break;
                case 'presentation':

                    $team->presentation->update($request->only($team->presentation->getFillable()));
        
                    if( $request->hasFile('file') )
                    {
                        $this->validate($request, [
                            'file' => 'required|file',
                        ], [
                            'file.required' => 'Необходимо прикрепить файл для загрузки',
                            'file.file' => 'Переданный параметр должен быть файлом',
                            'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                        ]);
        
                        if( $request->file('file')->isValid() )
                        {
                            if( $team->presentation->media->count() )
                            {
                                foreach ($team->presentation->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->presentation->media()->create($data);
                            }
        
                        }
                    }
        
                    event(new \App\Events\UpdateTeamInfoEvent($team));

                    return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                break;
                default:
                break;
            }
        }
        else
        {

            switch ($task->slug)
            {
                case 'team-have-idea':

                    if( !empty($team->idea) )
                    {

                        $this->validate($request, [
                            'name' => 'required',
                            'description' => 'required',
                            'theme' => 'required',
                        ], [
                            'name.required' => 'Необходимо указать название идеи',
                            'description.required' => 'Необходимо дать описание идеи',
                            'theme.required' => 'Необходимо выбрать тематику проекта',
                        ]);
        
                        $team->idea()->update([
                            'name' => $request->input('name'),
                            'description' => $request->input('description'),
                            'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
                        ]);
        
                        if( $team->idea->themes->count() )
                        {
                            $team->idea->themes()->detach();
                        }
        
                        $team->idea->themes()->attach($request->input('theme')['id']);
        
                        if( $request->hasFile('file') )
                        {
                            $this->validate($request, [
                                'file' => 'required|file|mimes:pdf',
                            ], [
                                'file.required' => 'Необходимо прикрепить файл для загрузки',
                                'file.file' => 'Переданный параметр должен быть файлом',
                                'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                            ]);
                        }
        
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
        
                            if( $team->idea->media->count() )
                            {
                                foreach ($team->idea->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->idea->media()->create($data);
                            }
        
                        }
        
                        event(new \App\Events\UpdateTeamInfoEvent($team));
        
                        if( $team->members->count() && $team->idea->status_id == 0 )
                        {
                            foreach ($team->members as $key => $member)
                            {
                                if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                                {
                                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                                        $member,
                                        (new \App\Notifications\MessageNotification(
                                            $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                            $subject = 'Ваша идея проекта на проверке',
                                            $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваша идея проекта отправлена на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                            $button = [
                                                'text' => 'Перейти в кабинет команды',
                                                'url' => url(route('team.show.index', $member->team)),
                                            ],
                                            $notification_id = null,
                                        ))
                                    );
                                }
                            }
        
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                (new \App\Notifications\MessageNotification(
                                    $sender = null,
                                    $subject = 'Новая идея',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилась новая идея от команды ' . $team->name . '</p>',
                                    $button = [
                                        'text' => 'Посмотреть',
                                        'url' => url(route('dashboard.tasks.index')),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
        
                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
                        
                    }
                    else
                    {

                        $this->validate($request, [
                            'name' => 'required',
                            'description' => 'required',
                            'theme' => 'required',
                            // 'file' => 'required|file|mimes:pdf',
                        ], [
                            'name.required' => 'Необходимо указать название идеи',
                            'description.required' => 'Необходимо дать описание идеи',
                            'theme.required' => 'Необходимо выбрать тематику проекта',
                            // 'file.required' => 'Необходимо загрузить файл',
                            // 'file.file' => 'Переданный параметр должен быть файлом',
                        ]);
                
                
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
                            $idea = $team->idea()->create([
                                'name' => $request->input('name'),
                                'description' => $request->input('description'),
                                'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0,
                                'type_id' => \App\Models\Teams\Project::CONST_TYPE_IDEA
                            ]);
                
                            $idea->themes()->attach($request->input('theme')['id']);
                
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $idea->media()->create($data);
                            }
                
                            event(new \App\Events\UpdateTeamInfoEvent($team));
                
                            if( !$team->tasks()->where('slug', 'team-have-idea')->exists() )
                            {
                                $team->tasks()->attach(Task::where('slug', 'team-have-idea')->first()->id);
                            }
        
                            if( $team->members->count() )
                            {
                                foreach ($team->members as $key => $member)
                                {
                                    if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                                    {
                                        \App\Jobs\EmailUsersNotificationJob::dispatch(
                                            $member,
                                            (new \App\Notifications\MessageNotification(
                                                $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                                $subject = 'Ваша идея проекта на проверке',
                                                $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваша идея проекта отправлена на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                                $button = [
                                                    'text' => 'Перейти в кабинет команды',
                                                    'url' => url(route('team.show.index', $member->team)),
                                                ],
                                                $notification_id = null,
                                            ))
                                        );
                                    }
                                }
        
                                \App\Jobs\EmailUsersNotificationJob::dispatch(
                                    \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    (new \App\Notifications\MessageNotification(
                                        $sender = null,
                                        $subject = 'Новая идея',
                                        $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилась новая идея от команды ' . $team->name . '</p>',
                                        $button = [
                                            'text' => 'Посмотреть',
                                            'url' => url(route('dashboard.tasks.index')),
                                        ],
                                        $notification_id = null,
                                    ))
                                );
                            }
                            
                            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
                        }

                    }

                break;
                case 'team-have-passport':

                    if( !empty($team->passport) )
                    {

                        $this->validate($request, [
                            'name' => 'required',
                            'description' => 'required',
                            'theme' => 'required',
                        ], [
                            'name.required' => 'Необходимо указать название паспорта',
                            'description.required' => 'Необходимо дать описание паспорта',
                            'theme.required' => 'Необходимо выбрать тематику проекта',
                        ]);
        
                        $team->passport()->update([
                            'name' => $request->filled('name') ? $request->input('name') : $team->name,
                            'description' => $request->filled('description') ? $request->input('description') : null,
                            'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
                        ]);
        
                        if( $team->passport->themes->count() )
                        {
                            $team->passport->themes()->detach();
                        }
        
                        $team->passport->themes()->attach($request->input('theme')['id']);
        
                        if( $request->hasFile('file') )
                        {
                            $this->validate($request, [
                                'file' => 'required|mimes:pdf',
                            ], [
                                'file.required' => 'Необходимо прикрепить файл для загрузки',
                                'file.file' => 'Переданный параметр должен быть файлом',
                                'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                            ]);
                        }
        
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
        
                            if( $team->passport->media->count() )
                            {
                                foreach ($team->passport->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->passport->media()->create($data);
                            }
        
                        }
        
                        event(new \App\Events\UpdateTeamInfoEvent($team));
        
                        if( $team->members->count() && $team->idea->status_id == 0 )
                        {
                            foreach ($team->members as $key => $member)
                            {
                                if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                                {
                                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                                        $member,
                                        (new \App\Notifications\MessageNotification(
                                            $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                            $subject = 'Ваш паспорт проекта на проверке',
                                            $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваш паспорт проекта отправлен на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                            $button = [
                                                'text' => 'Перейти в кабинет команды',
                                                'url' => url(route('team.show.index', $member->team)),
                                            ],
                                            $notification_id = null,
                                        ))
                                    );
                                }
                            }
        
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                (new \App\Notifications\MessageNotification(
                                    $sender = null,
                                    $subject = 'Новый паспорт проекта',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появился новый паспорт проекта от команды "' . $team->name . '"</p>',
                                    $button = [
                                        'text' => 'Посмотреть',
                                        'url' => url(route('dashboard.tasks.index')),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
        
                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                    }else{

                        $this->validate($request, [
                            'name' => 'required',
                            'description' => 'required',
                            'theme' => 'required',
                            'file' => 'required|file|mimes:pdf',
                        ], [
                            'name.required' => 'Необходимо указать название паспорта',
                            'description.required' => 'Необходимо дать описание паспорта',
                            'theme.required' => 'Необходимо выбрать тематику проекта',
                            'file.required' => 'Необходимо загрузить файл',
                            'file.file' => 'Переданный параметр должен быть файлом',
                        ]);
                
                
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
                            $passport = $team->passport()->create([
                                'name' => $request->filled('name') ? $request->input('name') : $team->name,
                                'description' => $request->filled('description') ? $request->input('description') : null,
                                'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0,
                                'type_id' => \App\Models\Teams\Project::CONST_TYPE_PASSPORT
                            ]);
                            
                            $passport->themes()->attach($request->input('theme')['id']);
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $passport->media()->create($data);
                            }
                
                            event(new \App\Events\UpdateTeamInfoEvent($team));
                
                            // if( !$team->tasks()->where('slug', 'team-have-passport')->exists() )
                            // {
                            //     $team->tasks()->attach(Task::where('slug', 'team-have-passport')->first()->id);
                            // }
                
                            if( $team->members->count() )
                            {
                                foreach ($team->members as $key => $member)
                                {
                                    if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                                    {
                                        \App\Jobs\EmailUsersNotificationJob::dispatch(
                                            $member,
                                            (new \App\Notifications\MessageNotification(
                                                $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                                $subject = 'Ваш паспорт проекта на проверке',
                                                $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваш паспорт проекта отправлен на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                                $button = [
                                                    'text' => 'Перейти в кабинет команды',
                                                    'url' => url(route('team.show.index', $member->team)),
                                                ],
                                                $notification_id = null,
                                            ))
                                        );
                                    }
                                }
            
                                \App\Jobs\EmailUsersNotificationJob::dispatch(
                                    \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    (new \App\Notifications\MessageNotification(
                                        $sender = null,
                                        $subject = 'Новый паспорт проекта',
                                        $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появился новый паспорт проекта от команды "' . $team->name . '"</p>',
                                        $button = [
                                            'text' => 'Посмотреть',
                                            'url' => url(route('dashboard.tasks.index')),
                                        ],
                                        $notification_id = null,
                                    ))
                                );
                            }
        
                            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
                        }

                    }

                break;
                case 'roadmap':

                    if( !empty($team->roadmap) )
                    {
                        
                        $team->roadmap()->update([
                            'name' => $request->filled('name') ? $request->input('name') : $team->name,
                            'description' => $request->filled('description') ? $request->input('description') : null,
                            'status_id' => $request->filled('status_id') ? $request->input('status_id') : 1
                        ]);

                        if( $request->hasFile('file') )
                        {
                            $this->validate($request, [
                                'file' => 'required|file',
                            ], [
                                'file.required' => 'Необходимо прикрепить файл для загрузки',
                                'file.file' => 'Переданный параметр должен быть файлом',
                                'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                            ]);
                        }
        
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
        
                            if( $team->roadmap->media->count() )
                            {
                                foreach ($team->roadmap->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->roadmap->media()->create($data);
                            }
        
                        }
        
                        event(new \App\Events\UpdateTeamInfoEvent($team));
        
                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                    }
                    else
                    {

                        $this->validate($request, [
                            'file' => 'required|file',
                        ], [
                            'file.required' => 'Необходимо загрузить файл',
                            'file.file' => 'Переданный параметр должен быть файлом',
                        ]);
                        
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
                            $roadmap = $team->roadmap()->create([
                                'name' => $request->filled('name') ? $request->input('name') : $team->name,
                                'description' => $request->filled('description') ? $request->input('description') : null,
                                'status_id' => $request->filled('status_id') ? $request->input('status_id') : 1,
                                'type_id' => \App\Models\Teams\Project::CONST_TYPE_ROADMAP
                            ]);
                            
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $roadmap->media()->create($data);
                            }
                
                            event(new \App\Events\UpdateTeamInfoEvent($team));
                
                            if( !$team->tasks()->where('slug', $task->slug)->exists() )
                            {
                                $team->tasks()->attach($task->id);
                            }
        
                            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
                        }

                    }

                break;
                case 'risk-matrix':

                    if( !empty($team->riskmatrix) )
                    {
                        
                        $team->riskmatrix()->update([
                            'name' => $request->filled('name') ? $request->input('name') : $team->name,
                            'description' => $request->filled('description') ? $request->input('description') : null,
                            'status_id' => $request->filled('status_id') ? $request->input('status_id') : 1
                        ]);

                        if( $request->hasFile('file') )
                        {
                            $this->validate($request, [
                                'file' => 'required|file',
                            ], [
                                'file.required' => 'Необходимо прикрепить файл для загрузки',
                                'file.file' => 'Переданный параметр должен быть файлом',
                                'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                            ]);
                        }
        
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
        
                            if( $team->riskmatrix->media->count() )
                            {
                                foreach ($team->riskmatrix->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->riskmatrix->media()->create($data);
                            }
        
                        }
        
                        event(new \App\Events\UpdateTeamInfoEvent($team));
        
                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                    }else{

                        $this->validate($request, [
                            'file' => 'required|file',
                        ], [
                            'file.required' => 'Необходимо загрузить файл',
                            'file.file' => 'Переданный параметр должен быть файлом',
                        ]);
                
                
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
                            $riskmatrix = $team->riskmatrix()->create([
                                'name' => $request->filled('name') ? $request->input('name') : $team->name,
                                'description' => $request->filled('description') ? $request->input('description') : null,
                                'status_id' => $request->filled('status_id') ? $request->input('status_id') : 1,
                                'type_id' => \App\Models\Teams\Project::CONST_TYPE_RISKMATRIX
                            ]);
                            
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $riskmatrix->media()->create($data);
                            }
                
                            event(new \App\Events\UpdateTeamInfoEvent($team));
                
                            if( !$team->tasks()->where('slug', $task->slug)->exists() )
                            {
                                $team->tasks()->attach($task->id);
                            }
        
                            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
                        }

                    }

                break;
                case 'presentation':

                    if( !empty($team->presentation) )
                    {
                        
                        $team->presentation()->update([
                            'name' => $request->filled('name') ? $request->input('name') : $team->name,
                            'description' => $request->filled('description') ? $request->input('description') : null,
                            'status_id' => $request->filled('status_id') ? $request->input('status_id') : 1
                        ]);

                        if( $request->hasFile('file') )
                        {
                            $this->validate($request, [
                                'file' => 'required|file',
                            ], [
                                'file.required' => 'Необходимо прикрепить файл для загрузки',
                                'file.file' => 'Переданный параметр должен быть файлом',
                                'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                            ]);
                        }
        
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
        
                            if( $team->presentation->media->count() )
                            {
                                foreach ($team->presentation->media as $mkey => $media)
                                {
                                    if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                                    {
                                        Storage::disk($media->disk)->delete($media->folder . $media->src);
                                    }
        
                                    $media->delete();
                                }
                            }
        
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $team->presentation->media()->create($data);
                            }
        
                        }
        
                        event(new \App\Events\UpdateTeamInfoEvent($team));
        
                        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);

                    }else{

                        $this->validate($request, [
                            'file' => 'required|file',
                        ], [
                            'file.required' => 'Необходимо загрузить файл',
                            'file.file' => 'Переданный параметр должен быть файлом',
                        ]);
                
                
                        if( $request->hasFile('file') && $request->file('file')->isValid() )
                        {
                            $presentation = $team->presentation()->create([
                                'name' => $request->filled('name') ? $request->input('name') : $team->name,
                                'description' => $request->filled('description') ? $request->input('description') : null,
                                'status_id' => $request->filled('status_id') ? $request->input('status_id') : 1,
                                'type_id' => \App\Models\Teams\Project::CONST_TYPE_PRESENTATION
                            ]);
                            
                            $UploadedFile = $request->file('file');
                
                            do{
                                $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                            }while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
                            
                
                            $data = [
                                'name' => pathinfo($UploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                                'extension' => $UploadedFile->getClientOriginalExtension(),
                                'mimes' => $UploadedFile->getMimeType(),
                                'src' => $UploadedFileNewName,
                                'parent_id' => null,
                                'user_id' => $user->id,
                                'disk' => 'public',
                                'type' => 'original',
                                'tag' => null,
                                'size' => $UploadedFile->getSize(),
                                'width' => null,
                                'height' => null,
                                'collection' => null,
                                'folder' => '/uploads/teams/'
                            ];
                            
                            if( $UploadedFile->storePubliclyAs($data['folder'], $data['src'], $data['disk']) )
                            {
                                $presentation->media()->create($data);
                            }
                
                            event(new \App\Events\UpdateTeamInfoEvent($team));
                
                            if( !$team->tasks()->where('slug', $task->slug)->exists() )
                            {
                                $team->tasks()->attach($task->id);
                            }
        
                            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
                        }

                    }

                break;
                default:
                break;
            }

        }

        return $this->sendError('Произошла ошибка, пожалуйста, повторите попытку позднее', $errorMessages = [], $code = 422);

    }

    public function previewAvatar(Team $team, Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,bmp',
            // 'file' => 'required|image',
        ], [
            'file.required' => 'Необходимо прикрепить файл для загрузки',
            'file.file' => 'Переданный параметр должен быть файлом',
            'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
            'file.image' => 'Файл должен быть изображением.',
        ]);
        
        $preview = $team->generatePreview($files = $request->file('file'), $sizesArray = config('teams.picture.sizes'));
        return $this->sendResponse($data = $preview, $message = null, $code = 200, $isRaw = false);
    }

    public function updateValues(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'values' => 'required',
        ], [
            'values.required' => 'Необходимо указать ценности команды'
        ]);
        
        if( !is_array($request->input('values')) )
        {
            $request->merge([
                'values' => Str::of($request->input('values'))->explode("\n")
            ]);
        }

        $team->update([
            'values' => $request->input('values')
        ]);

        event(new \App\Events\UpdateTeamInfoEvent($team));

        if( !$team->tasks()->where('slug', 'values-question')->exists() )
        {
            $team->tasks()->attach(Task::where('slug', 'values-question')->first()->id);
        }

        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
    }

    public function updateAvatar(Team $team, Request $request)
    {
        $user = $request->user();
        $task = Task::where('slug', 'photo-start')->first();
        $currentStep = Step::with(['tasks'])->where('id', Setting::where('slug', 'current-step')->first()->values)->first();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,bmp',
            // 'file' => 'required|image',
        ], [
            'file.required' => 'Необходимо прикрепить файл для загрузки',
            'file.file' => 'Переданный параметр должен быть файлом',
            'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
            'file.image' => 'Файл должен быть изображением.',
        ]);

        if( $request->hasFile('file') && $request->file('file')->isValid() )
        {
            $team->ImageRepositoryDelete($collection = 'teampicture');
            $team->ImageRepository($files = $request->file('file'), $sizesArray = config('teams.picture.sizes'), $storageDisk = 'public', $folderStorageDisk = '/uploads/teams/', $Author = $user->id);

            event(new \App\Events\UpdateTeamInfoEvent($team));

            if( $currentStep->tasks()->where('slug', $task->slug)->count() && !$team->tasks()->where('slug', $task->slug)->count() )
            {
                $team->tasks()->attach($task->id);
            }

            return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
        }

        return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
    }

    public function update(Team $team, Request $request)
    {
        $user = $request->user();

        if( ( !empty($team->lead) && $team->lead->id ) <> $user->id && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'name' => 'string',
            'description' => 'string',
            'organization_id' => 'integer',
            'values' => 'array',
        ], [
            'name.string' => 'Название команды должно быть строкой',
            'description.string' => 'Описание команды должно быть строкой',
            'values.array' => 'Список ценностей должен быть массивом',
            'organization_id.integer' => 'Поле организация, должно быть числом',
        ]);

        $team->update($request->only($team->getFillable()));

        event(new \App\Events\UpdateTeamInfoEvent($team));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if( empty($user) || !empty($user) && !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'organization_id' => 'nullable|integer',
            'values' => 'nullable|array',
        ], [
            'name.required' => 'Необходимо указать название команды',
            'name.string' => 'Название команды должно быть строкой',
            'description.string' => 'Описание команды должно быть строкой',
            'values.array' => 'Список ценностей должен быть массивом',
            'organization_id.integer' => 'Поле организация, должно быть числом',
        ]);

        $team = Team::create($request->only('name', 'description', 'organization_id', 'values'));

        event(new \App\Events\UpdateTeamInfoEvent($team));
        
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $team instanceof Team )
            {
                $team = $team->load($request->input('with'));
            }else{
                $team = $team->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $team instanceof Team )
            {
                $team = $team->setAppends($request->input('appends'));
            }else{
                $team = $team->each->setAppends($request->input('appends'));
            }
        }
        
        return $this->sendResponse($data = new \App\Http\Resources\Team\IndexResource($team), $message = null, $code = 200, $isRaw = false);
    }


}
