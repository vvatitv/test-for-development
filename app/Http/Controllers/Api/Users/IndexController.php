<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use App\Models\Users\Field;

class IndexController extends BaseController
{

    public function index(\App\Filters\UserFilters $filters, Request $request)
    {
        $users = User::query()->filter($filters)->distinct()->get();

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
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    return $usort->id;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    return $usort->id;
                                });
                            break;
                        }
                    break;
                    case 'status':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    return $usort->email_verified_at;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    return $usort->email_verified_at;
                                });
                            break;
                        }
                    break;
                    case 'full_name':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){

                                    $u = $usort->toArray();

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

                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){

                                    $u = $usort->toArray();

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

                                });
                            break;
                        }
                    break;
                    case 'first_name':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['first_name']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['first_name']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'first_name')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'first_name')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['first_name']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['first_name']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'first_name')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'first_name')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'last_name':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['last_name']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['last_name']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'last_name')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'last_name')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['last_name']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['last_name']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'last_name')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'last_name')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'middle_name':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['middle_name']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['middle_name']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'middle_name')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'middle_name')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['middle_name']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['middle_name']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'middle_name')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'middle_name')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'sex':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['sex']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['sex']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'sex')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'sex')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['sex']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['sex']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'sex')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'sex')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'birth_date':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['birth_date']) )
                                    {
                                        return $u['birth_date']->pivot->value;
                                    }

                                    if( $usort->fields()->where('slug', 'birth_date')->exists() )
                                    {
                                        return $usort->fields()->where('slug', 'birth_date')->first()->pivot->value;
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['birth_date']) )
                                    {
                                        return $u['birth_date']->pivot->value;
                                    }

                                    if( $usort->fields()->where('slug', 'birth_date')->exists() )
                                    {
                                        return $usort->fields()->where('slug', 'birth_date')->first()->pivot->value;
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'job_position':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['job_position']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['job_position']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'job_position')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'job_position')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['job_position']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['job_position']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'job_position')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'job_position')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'job_experience':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['job_experience']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['job_experience']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'job_experience')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'job_experience')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['job_experience']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['job_experience']->pivot->value);
                                    }

                                    if( $usort->fields()->where('slug', 'job_experience')->exists() )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->fields()->where('slug', 'job_experience')->first()->pivot->value);
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'telephone':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['telephone']) )
                                    {
                                        return $u['telephone']->pivot->value;
                                    }

                                    if( $usort->fields()->where('slug', 'telephone')->exists() )
                                    {
                                        return $usort->fields()->where('slug', 'telephone')->first()->pivot->value;
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['telephone']) )
                                    {
                                        return $u['telephone']->pivot->value;
                                    }

                                    if( $usort->fields()->where('slug', 'telephone')->exists() )
                                    {
                                        return $usort->fields()->where('slug', 'telephone')->first()->pivot->value;
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'organization':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['organization']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['organization']['name']);
                                    }

                                    if( !empty($usort->organization) )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->organization->name);
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();
                                    
                                    if( !empty($u['organization']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['organization']['name']);
                                    }

                                    if( !empty($usort->organization) )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->organization->name);
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'team':
                        switch ($filterArr['type'])
                        {
                            case 'desc':
                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();

                                    if( !empty($u['team']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['team']['name']);
                                    }

                                    if( !empty($usort->team) )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->team->name);
                                    }

                                    return 0;
                                });
                            break;
                            default:
                                $users = $users->sortBy(function($usort) use ($request, $filterArr){
                                    $u = $usort->toArray();
                                    
                                    if( !empty($u['team']) )
                                    {
                                        return \Illuminate\Support\Str::upper($u['team']['name']);
                                    }

                                    if( !empty($usort->team) )
                                    {
                                        return \Illuminate\Support\Str::upper($usort->team->name);
                                    }

                                    return 0;
                                });
                            break;
                        }
                    break;
                    case 'quizzes-score':

                        switch ($filterArr['type'])
                        {
                            case 'desc':

                                $users = $users->sortByDesc(function($usort) use ($request, $filterArr){

                                    $u = $usort->toArray();
                                    
                                    if( !isset($filterArr['params']) )
                                    {
                                        return 0;
                                    }
                                    
                                    if( empty($filterArr['params']['hash']) )
                                    {
                                        return 0;
                                    }

                                    $quizHash = $filterArr['params']['hash'];

                                    if( !empty($u['quizzes']) )
                                    {

                                        $quizzes = collect($u['quizzes'])->where('status_id', 200)->where('quizze.hash', $quizHash)->first();

                                        if( empty($quizzes) )
                                        {
                                            return 0;
                                        }

                                        return collect($quizzes['answers'])->sum('pivot.point');
                                    }

                                    return 0;

                                });

                            break;
                            default:

                                $users = $users->sortBy(function($usort) use ($request, $filterArr){

                                    $u = $usort->toArray();
                                    
                                    if( !isset($filterArr['params']) )
                                    {
                                        return 0;
                                    }
                                    
                                    if( empty($filterArr['params']['hash']) )
                                    {
                                        return 0;
                                    }

                                    
                                    $quizHash = $filterArr['params']['hash'];

                                    if( !empty($u['quizzes']) )
                                    {

                                        $quizzes = collect($u['quizzes'])->where('status_id', 200)->where('quizze.hash', $quizHash)->first();

                                        if( empty($quizzes) )
                                        {
                                            return 0;
                                        }

                                        return collect($quizzes['answers'])->sum('pivot.point');
                                    }

                                    return 0;

                                });

                            break;
                        }

                    break;
                    case 'quizzes-score-w-number-passes':
                    break;
                    default:
                    break;
                }
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

    public function show(User $user, Request $request)
    {
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            $user = $user->load($request->input('with'));
                
            if( collect($request->input('with'))->contains('fields') )
            {
                $user = $user->withFields();
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            $user = $user->setAppends($request->input('appends'));
        }
        
        return $this->sendResponse($data = new \App\Http\Resources\User\IndexResource($user), $message = null, $code = 200, $isRaw = false);
    }

    public function sendNotifications(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'users' => 'required|array',
            'subject' => 'required|string',
            'message' => 'required|string',
        ], [
            'users.required' => 'Необходимо указать список пользователей',
            'users.array' => 'Список пользователей необходимо передать ввиде массива',
            'subject.required' => 'Необходимо указать заголовок письма',
            'message.required' => 'Необходимо указать текст письма',
        ]);

        $users = User::whereIn('id', $request->input('users'))->with(['fields'])->get()->each->withFields();

        foreach ($users as $key => $user)
        {
            // $user->notify((new \App\Notifications\MessageNotification(
            //     $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
            //     $subject = $request->input('subject'),
            //     $message = $request->input('message'),
            //     $button = $request->input('button'),
            //     $notification_id = null,
            // )));
            
            \App\Jobs\EmailUsersNotificationJob::dispatch(
                $user,
                (new \App\Notifications\MessageNotification(
                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                    $subject = $request->input('subject'),
                    $message = $request->input('message'),
                    $button = $request->input('button'),
                    $notification_id = null,
                ))
            );
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function notificationSubscription(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'users' => 'required|array'
        ]);

        $users = User::whereIn('id', $request->input('users'))->get();

        foreach ($users as $key => $user)
        {
            $user->update([
                'unsubscription' => 0
            ]);
            event(new \App\Events\UpdateUserInfoEvent($user));
        }
    }

    public function notificationUnsubscription(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'users' => 'required|array'
        ], [
            'users.required' => 'Необходимо указать список пользователей',
            'users.array' => 'Список пользователей должен быть массивом'
        ]);

        $users = User::whereIn('id', $request->input('users'))->get();

        foreach ($users as $key => $user)
        {
            $user->update([
                'unsubscription' => 1
            ]);
            event(new \App\Events\UpdateUserInfoEvent($user));
        }
    }

    public function approved(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'users' => 'required|array'
        ], [
            'users.required' => 'Необходимо указать список пользователей',
            'users.array' => 'Список пользователей должен быть массивом'
        ]);

        $users = User::whereIn('id', $request->input('users'))->get();

        foreach ($users as $key => $user)
        {
            $user->markEmailAsVerified();
            event(new \App\Events\UpdateUserInfoEvent($user));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }
    
    public function update(User $user, Request $request)
    {
        $authUser = $request->user();

        if( empty($authUser) || !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'organization_id' => 'integer',
            'email' => 'email',
        ], [
            'email.email' => 'Поле E-mail, должно быть адресом электронной почты',
            'organization_id.integer' => 'Поле организация, должно быть числом',
        ]);

        $fields = Field::all();

        if( $request->filled('full_name') )
        {
            $full_name = \Illuminate\Support\Str::of($request->input('full_name'))->explode(' ');

            $request->merge([
                'last_name' => !empty($full_name[0]) ? $full_name[0] : null,
                'first_name' => !empty($full_name[1]) ? $full_name[1] : null,
                'middle_name' => !empty($full_name[2]) ? $full_name[2] : null,
            ]);
        }
        
        if( $fields->count() )
        {
            foreach ($fields as $key => $field)
            {
                if( $request->filled($field->slug) )
                {
                    if( $user->fields()->where('slug', $field->slug)->count() )
                    {
                        $user->fields()->detach($field->id);
                    }

                    if( !empty($request->input($field->slug)) )
                    {
                        $user->fields()->attach($field->id, [
                            'value' => $request->input($field->slug),
                            'points' => !empty($field->options) && !empty($field->options['points']) ? ( $field->options['points']['group'] == true ? ( $user->fields()->whereIn('field_id', $fields->where('group_id', $field->group_id)->pluck('id'))->count() > 0 ? 0 : $field->options['points']['value'] ) : $field->options['points']['value'] ) : 0,
                            'is_show' => 0
                        ]);
                    }
                }
            }
        }

        $user->update($request->only($user->getFillable()));

        event(new \App\Events\UpdateUserInfoEvent($user));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function destroyArray(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'users' => 'required|array'
        ]);

        $users = User::whereIn('id', $request->input('users'))->get();

        foreach ($users as $key => $user)
        {

            if( $user->teams->count() )
            {
                foreach ($user->teams as $team)
                {
                    $team->members()->detach($user);
                    event(new \App\Events\UpdateTeamInfoEvent($team));
                }
            }

            $user->delete();

            event(new \App\Events\UpdateUserInfoEvent($user));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function destroy(User $user, Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        if( $user->teams->count() )
        {
            foreach ($user->teams as $team)
            {
                $team->members()->detach($user);
                event(new \App\Events\UpdateTeamInfoEvent($team));
            }
        }

        $user->delete();
        event(new \App\Events\UpdateUserInfoEvent($user));

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function impersonateJoin(User $user, Request $request)
    {
        $AuthUser = $request->user();

        if( empty($AuthUser) || !$AuthUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }
        
        $AuthUser->impersonate($user);

        return redirect()->route('home');
        // return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function impersonateLeave(User $user, Request $request)
    {
        $AuthUser = $request->user();

        if( empty($AuthUser) )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $AuthUser->leaveImpersonation();

        return redirect()->route('home');
        // return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }
}
