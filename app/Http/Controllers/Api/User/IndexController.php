<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Users\Field;

class IndexController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();

        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( collect($request->input('with'))->contains('all') )
            {
                $withArray = collect($request->input('with'))->forget(collect($request->input('with'))->search('all'))->values()->merge(
                    [
                        'roles',
                        'permissions',
                        'organization',
                        'fields',
                        'certifications',
                        'certifications.type',
                        'tests',
                        'badges',
                        'badges.type',
                        'votes',
                        'stars',
                        'instructions',
                        'quizzes',
                        'quizzes.quizze',
                        'quizzes.questions',
                        'quizzes.answers',
                        'teams',
                        'teams.badges',
                        'teams.leads',
                        'teams.leads.badges',
                        'teams.leads.votes',
                        'teams.members',
                        'teams.members.fields',
                        'teams.members.badges',
                        'teams.members.votes',
                        'teams.members.organization',
                        'teams.organization',
                        'teams.media',
                        'teams.tasks',
                        'teams.idea',
                        'teams.idea.media',
                        'teams.idea.votes',
                        'teams.passport',
                        'teams.passport.media',
                        'teams.roadmap',
                        'teams.roadmap.media',
                        'teams.riskmatrix',
                        'teams.riskmatrix.media',
                        'teams.steps'
                    ]
                );
                
                $request->merge([
                    'with' => $withArray->values()->toArray()
                ]);
            }
            
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

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,bmp',
        ], [
            'file.required' => 'Необходимо прикрепить файл для загрузки',
            'file.file' => 'Переданный параметр должен быть файлом',
            'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
        ]);

        if( $request->hasFile('file') && $request->file('file')->isValid() )
        {
            $user->ImageRepositoryDelete($collection = 'userpicture');
            $user->ImageRepository($files = $request->file('file'), $sizesArray = config('users.picture.sizes'), $storageDisk = 'public', $folderStorageDisk = '/uploads/users/', $Author = $user->id);

            event(new \App\Events\UpdateUserInfoEvent($user));

            return $this->sendResponse($data = [], $message = 'Информация изменена', $code = 200, $isRaw = false);
        }

        return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
    }

    public function notificationSubscription(Request $request)
    {
        $user = $request->user();

        if( empty($user) )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $user->update([
            'unsubscription' => 0
        ]);

        event(new \App\Events\UpdateUserInfoEvent($user));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function notificationUnsubscription(Request $request)
    {
        $user = $request->user();

        if( empty($user) )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $user->update([
            'unsubscription' => 1
        ]);

        event(new \App\Events\UpdateUserInfoEvent($user));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        if( empty($user) )
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
}
