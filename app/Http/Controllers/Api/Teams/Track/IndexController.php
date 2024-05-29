<?php

namespace App\Http\Controllers\Api\Teams\Track;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Support\Facades\Storage;
use App\Models\Team;
use App\Models\User;
use App\Models\Task;
use App\Models\Track;
use App\Models\Briefcase;
use App\Models\Media;

class IndexController extends BaseController
{
    public function store(Team $team, Request $request)
    {
        $user = $request->user();

        if( empty($user) )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }
        
        $this->validate($request, [
            'task' => ['required', 'array'],
        ], [
            'task.required' => 'Необходимо указать задание',
            'task.array' => 'Задание должно быть массивом',
        ]);

        $task = Task::with(['steps', 'tracks'])->where('hash', $request->input('task.hash'))->first();

        if( !empty($task->options) && !empty($task->options['passage']) )
        {
            switch ($task->options['passage'])
            {
                case 'teamlead':

                    if( !empty($user) && !empty($team->lead) && $user->id <> $team->lead->id )
                    {
                        return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
                    }

                break;
                default:
                break;
            }
        }

        switch ($task->slug)
        {
            case 'join-in-tracks':

                $this->validate($request, [
                    'tracks' => ['required', 'array'],
                ], [
                    'tracks.required' => 'Для отправки задания необходимо выбрать хотя бы один трек',
                ]);

                $tracks = Track::whereIn('slug', collect($request->input('tracks'))->pluck('slug'))->get();

                if( $tracks->count() )
                {
                    foreach ($tracks as $track)
                    {
                        if( !$team->tracks()->where('id', $track->id)->count() )
                        {
                            $team->tracks()->attach($track->id);
                        }
                    }
        
                    if( $team->tasks()->where('id', $task->id)->count() )
                    {
                        $team->tasks()->detach($task->id);
                    }
        
                    $team->tasks()->attach($task->id);
                }
        
                event(new \App\Events\UpdateTeamInfoEvent($team));
            
                return $this->sendResponse($data = null, $message = 'Задание успешно отправлено', $code = 200, $isRaw = false);

            break;
            case 'team-track-selection-case':

                $this->validate($request, [
                    'briefcase' => ['required', 'array'],
                ], [
                    'briefcase.required' => 'Для отправки задания необходимо выбрать кейс',
                ]);

                $briefcase = Briefcase::where('id', $request->input('briefcase.id'))->first();

                if( empty($briefcase) )
                {
                    return $this->sendError('Указанное кейс отсутствует', $errorMessages = [], $code = 422);
                }

                if( $team->briefcases()->where('id', $briefcase->id)->count() )
                {
                    $team->briefcases()->detach($briefcase->id);
                }
                $team->briefcases()->attach($briefcase->id);

                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }
                $team->tasks()->attach($task->id);

                event(new \App\Events\UpdateTeamInfoEvent($team));
            
                return $this->sendResponse($data = null, $message = 'Задание успешно отправлено', $code = 200, $isRaw = false);
                
            break;
            case 'team-track-selection-case-part-2':

                $this->validate($request, [
                    'file' => 'required|file|mimes:pdf|max:20480',
                ], [
                    'file.required' => 'Необходимо загрузить файл',
                    'file.file' => 'Переданный параметр должен быть файлом',
                    'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                ]);
                
                $project = $team->projects()->create([
                    'name' => $request->filled('name') ? $request->input('name') : $team->name,
                    'description' => $request->filled('description') ? $request->input('description') : null,
                    'status_id' => 0,
                    'type_id' => \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_SELECTION_CASE_PART2
                ]);

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    
                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }
                    while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
        
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
                        $project->media()->create($data);
                    }
                }

                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }

                $team->tasks()->attach($task->id);
                
                event(new \App\Events\UpdateTeamInfoEvent($team));
            
                if( $team->members->count() )
                {
                    foreach ($team->members as $member)
                    {
                        if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                        {
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Проектное решение. Ваше задание на проверке',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваше задание «' . ( $task->steps->count() ? '№' . $task->steps->first()->pivot->ordering : '') . ' ' . $task->name . '» трека «' . ( $task->tracks->count() ? $task->tracks->first()->name : 'Проектная культура' ) . '» отправлено на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                    $button = [
                                        'text' => 'Перейти в кабинет команды',
                                        'url' => url(route('team.show.index', $team)),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
                    }
                }

                \App\Jobs\EmailUsersNotificationJob::dispatch(
                    \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                    (new \App\Notifications\MessageNotification(
                        $sender = null,
                        $subject = 'Проектное решение. Новое решение кейса',
                        $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилось новое решение кейса в рамках трека «' . ( $task->tracks->count() ? $task->tracks->first()->name : 'Проектная культура' ) . '» от команды "<strong>' . $team->name . '</strong>"</p>',
                        $button = [
                            'text' => 'Посмотреть',
                            'url' => url(route('dashboard.tasks.index')),
                        ],
                        $notification_id = null,
                    ))
                );

                return $this->sendResponse($data = null, $message = 'Задание успешно отправлено', $code = 200, $isRaw = false);
                
            break;
            case 'team-track-take-survey':

                $this->validate($request, [
                    'file' => 'required|file|mimes:pdf|max:20480',
                ], [
                    'file.required' => 'Необходимо загрузить файл',
                    'file.file' => 'Переданный параметр должен быть файлом',
                    'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                ]);

                $project = $team->projects()->create([
                    'name' => $request->filled('name') ? $request->input('name') : $team->name,
                    'description' => $request->filled('description') ? $request->input('description') : null,
                    'status_id' => 0,
                    'type_id' => \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_TAKE_SURVEY
                ]);

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    
                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }
                    while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
        
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
                        $project->media()->create($data);
                    }
                }

                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }

                $team->tasks()->attach($task->id);
                
                event(new \App\Events\UpdateTeamInfoEvent($team));
            
                if( $team->members->count() )
                {
                    foreach ($team->members as $member)
                    {
                        if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                        {
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Проектная культура. Ваше задание на проверке',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваше задание «' . ( $task->steps->count() ? '№' . $task->steps->first()->pivot->ordering : '') . ' ' . $task->name . '» трека «' . ( $task->tracks->count() ? $task->tracks->first()->name : 'Проектная культура' ) . '» отправлено на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                    $button = [
                                        'text' => 'Перейти в кабинет команды',
                                        'url' => url(route('team.show.index', $team)),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
                    }
                }

                \App\Jobs\EmailUsersNotificationJob::dispatch(
                    \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                    (new \App\Notifications\MessageNotification(
                        $sender = null,
                        $subject = 'Проектная культура. Новое подтверждение анкеты',
                        $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появился новый ответ на задание «' . ( $task->steps->count() ? '№' . $task->steps->first()->pivot->ordering : '') . ' ' . $task->name . '» в рамках трека «' . ( $task->tracks->count() ? $task->tracks->first()->name : 'Проектная культура' ) . '» от команды "<strong>' . $team->name . '</strong>"</p>',
                        $button = [
                            'text' => 'Посмотреть',
                            'url' => url(route('dashboard.tasks.index')),
                        ],
                        $notification_id = null,
                    ))
                );

                return $this->sendResponse($data = null, $message = 'Задание успешно отправлено', $code = 200, $isRaw = false);

            break;
            case 'team-track-project':

                $this->validate($request, [
                    'theme' => 'required',
                    'file' => 'required|file|mimes:pdf|max:20480',
                    'url' => 'required|url',
                ], [
                    'theme.required' => 'Необходимо выбрать тематику проекта',
                    'file.required' => 'Необходимо загрузить файл',
                    'file.file' => 'Переданный параметр должен быть файлом',
                    'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    'url.required' => 'Необходимо указать ссылку',
                    'url.url' => 'Переданный параметр должен быть ссылкой',
                ]);

                $idea = $team->tracksIdea()->create([
                    'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
                ]);

                $idea->themes()->attach($request->input('theme')['id']);

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    
                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }
                    while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
        
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

                if( $request->filled('url') )
                {
                    $idea
                    ->media()
                    ->create([
                        'name' => $team->name,
                        'type' => 'video',
                        'user_id' => $user->id,
                        'collection' => $task->slug,
                        'extension' => null,
                        'thumbnails' => null,
                        'src' => urldecode($request->input('url'))
                    ]);
                }

                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }
                $team->tasks()->attach($task->id);

                event(new \App\Events\UpdateTeamInfoEvent($team));

                if( $team->members->count() )
                {
                    foreach ($team->members as $member)
                    {
                        if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                        {
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Проектный опыт. Ваше задание на проверке',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваше задание «' . ( $task->steps->count() ? '№' . $task->steps->first()->pivot->ordering : '') . ' ' . $task->name . '» трека «' . ( $task->tracks->count() ? $task->tracks->first()->name : 'Проектный опыт' ) . '» отправлено на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                    $button = [
                                        'text' => 'Перейти в кабинет команды',
                                        'url' => url(route('team.show.index', $team)),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
                    }
                }

                \App\Jobs\EmailUsersNotificationJob::dispatch(
                    \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                    (new \App\Notifications\MessageNotification(
                        $sender = null,
                        $subject = 'Проектный опыт. Новый ответ на задание',
                        $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появился новый ответ на задание «' . ( $task->steps->count() ? '№' . $task->steps->first()->pivot->ordering : '') . ' ' . $task->name . '» в рамках трека «' . ( $task->tracks->count() ? $task->tracks->first()->name : 'Проектный опыт' ) . '» от команды "<strong>' . $team->name . '</strong>"</p>',
                        $button = [
                            'text' => 'Посмотреть',
                            'url' => url(route('dashboard.tasks.index')),
                        ],
                        $notification_id = null,
                    ))
                );

                return $this->sendResponse($data = [], $message = 'Задание успешно отправлено на проверку', $code = 200, $isRaw = false);

            break;
            case 'team-track-presentation':

                $this->validate($request, [
                    'file' => 'required|file|mimes:pdf|max:20480',
                ], [
                    'file.required' => 'Необходимо загрузить файл',
                    'file.file' => 'Переданный параметр должен быть файлом',
                    'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                ]);
                
                $project = $team->projects()->create([
                    'name' => $request->filled('name') ? $request->input('name') : $team->name,
                    'description' => $request->filled('description') ? $request->input('description') : null,
                    'status_id' => 0,
                    'type_id' => \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_PRESENTATION
                ]);

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    
                    $UploadedFile = $request->file('file');
        
                    do{
                        $UploadedFileNewName = \Illuminate\Support\Str::uuid() . '.' . $UploadedFile->getClientOriginalExtension();
                    }
                    while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
        
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
                        $project->media()->create($data);
                    }
                }

                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }

                $team->tasks()->attach($task->id);
                
                event(new \App\Events\UpdateTeamInfoEvent($team));
            
                if( $team->members->count() )
                {
                    foreach ($team->members as $member)
                    {
                        if( !$member->mentorIn()->where('team_id', $team->id)->count() )
                        {
                            \App\Jobs\EmailUsersNotificationJob::dispatch(
                                $member,
                                (new \App\Notifications\MessageNotification(
                                    $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                    $subject = 'Проектное решение. Ваше задание на проверке',
                                    $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваше задание «' . ( $task->steps->count() ? '№' . $task->steps->first()->pivot->ordering : '') . ' ' . $task->name . '» трека «' . ( $task->tracks->count() ? $task->tracks->first()->name : 'Проектная культура' ) . '» отправлено на модерацию. В течение нескольких дней мы вернемся с обратной связью.</p><p><br></p><p>Если у вас возникнут вопросы или сложности с выполнением задания, вы можете обратиться за консультацией в чат поддержки Jivo на сайте или на почту <a href="mailto:activation@mos.ru">activation@mos.ru</a>.</p>',
                                    $button = [
                                        'text' => 'Перейти в кабинет команды',
                                        'url' => url(route('team.show.index', $team)),
                                    ],
                                    $notification_id = null,
                                ))
                            );
                        }
                    }
                }

                \App\Jobs\EmailUsersNotificationJob::dispatch(
                    \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                    (new \App\Notifications\MessageNotification(
                        $sender = null,
                        $subject = 'Проектное решение. Новая презентация',
                        $message = '<h1>Здравствуйте!</h1><p><br></p><p>На сайте конкурса появилась новая презентация в рамках трека «' . ( $task->tracks->count() ? $task->tracks->first()->name : 'Проектная культура' ) . '» от команды "<strong>' . $team->name . '</strong>"</p>',
                        $button = [
                            'text' => 'Посмотреть',
                            'url' => url(route('dashboard.tasks.index')),
                        ],
                        $notification_id = null,
                    ))
                );

                return $this->sendResponse($data = null, $message = 'Задание успешно отправлено', $code = 200, $isRaw = false);
                
            break;
            default:
            break;
        }

        return $this->sendError('Указанное задание отсутствует', $errorMessages = [], $code = 422);
    }

    public function update(Team $team, Request $request)
    {
        $user = $request->user();

        if( empty($user) )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }
        
        $this->validate($request, [
            'task' => ['required', 'array'],
        ], [
            'task.required' => 'Необходимо указать задание',
            'task.array' => 'Задание должно быть массивом',
        ]);

        $task = Task::where('hash', $request->input('task.hash'))->first();

        if( !empty($task->options) && !empty($task->options['passage']) )
        {
            switch ($task->options['passage'])
            {
                case 'teamlead':

                    if( !empty($user) && !empty($team->lead) && $user->id <> $team->lead->id )
                    {
                        return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
                    }

                break;
                default:
                break;
            }
        }
        
        switch ($task->slug)
        {
            case 'team-track-project':

                $this->validate($request, [
                    'theme' => 'required',
                    'url' => 'required|url',
                ], [
                    'theme.required' => 'Необходимо выбрать тематику проекта',
                    'url.required' => 'Необходимо указать ссылку',
                    'url.url' => 'Переданный параметр должен быть ссылкой',
                ]);

                $team->tracksIdea()->update([
                    'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
                ]);

                if( $team->tracksIdea->themes->count() )
                {
                    $team->tracksIdea->themes()->detach();
                }

                $team->tracksIdea->themes()->attach($request->input('theme')['id']);

                if( !$team->tracksIdea->media->whereNull('collection')->count() )
                {
                    $this->validate($request, [
                        'file' => 'required|file|mimes:pdf|max:20480',
                    ], [
                        'file.required' => 'Необходимо загрузить файл',
                        'file.file' => 'Переданный параметр должен быть файлом',
                        'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    ]);
                }

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    if( $team->tracksIdea->media->whereNull('collection')->count() )
                    {
                        foreach ($team->tracksIdea->media->whereNull('collection')->get() as $mkey => $media)
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
                    }
                    while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
        
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
                        $team->tracksIdea->media()->create($data);
                    }
                }

                if( $request->filled('url') )
                {
                    if( $team->tracksIdea->media()->where('collection', $task->slug)->count() )
                    {
                        foreach ($team->tracksIdea->media()->where('collection', $task->slug)->get() as $mkey => $media)
                        {
                            if( Storage::disk($media->disk)->exists($media->folder . $media->src) )
                            {
                                Storage::disk($media->disk)->delete($media->folder . $media->src);
                            }

                            $media->delete();
                        }
                    }

                    $team->tracksIdea
                    ->media()
                    ->create([
                        'name' => $team->name,
                        'type' => 'video',
                        'user_id' => $user->id,
                        'collection' => $task->slug,
                        'extension' => null,
                        'thumbnails' => null,
                        'src' => urldecode($request->input('url'))
                    ]);
                }

                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }
                $team->tasks()->attach($task->id);

                event(new \App\Events\UpdateTeamInfoEvent($team));

                return $this->sendResponse($data = [], $message = 'Задание успешно отправлено на проверку', $code = 200, $isRaw = false);

            break;
            case 'team-track-take-survey':

                if( $team->projects()->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_TAKE_SURVEY)->count() )
                {
                    $project = $team->projects()->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_TAKE_SURVEY)->first();

                    $project->update([
                        'status_id' => 0,
                    ]);
                }
                else
                {
                    $project = $team->projects()->create([
                        'name' => $request->filled('name') ? $request->input('name') : $team->name,
                        'description' => $request->filled('description') ? $request->input('description') : null,
                        'status_id' => 0,
                        'type_id' => \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_TAKE_SURVEY
                    ]);
                }

                if( !$project->media->count() )
                {

                    $this->validate($request, [
                        'file' => 'required|file|mimes:pdf|max:20480',
                    ], [
                        'file.required' => 'Необходимо загрузить файл',
                        'file.file' => 'Переданный параметр должен быть файлом',
                        'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    ]);
                }

                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    
                    if( $project->media->count() )
                    {
                        foreach ($project->media as $mkey => $media)
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
                    }
                    while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
        
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
                        $project->media()->create($data);
                    }
                }
                
                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }
                
                $team->tasks()->attach($task->id);

                event(new \App\Events\UpdateTeamInfoEvent($team));
            
                return $this->sendResponse($data = null, $message = 'Задание успешно отправлено', $code = 200, $isRaw = false);

            break;
            case 'team-track-selection-case-part-2':

                if( $team->projects()->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_SELECTION_CASE_PART2)->count() )
                {
                    $project = $team->projects()->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_SELECTION_CASE_PART2)->first();
                    
                    $project->update([
                        'status_id' => 0,
                    ]);
                }
                else
                {
                    $project = $team->projects()->create([
                        'name' => $request->filled('name') ? $request->input('name') : $team->name,
                        'description' => $request->filled('description') ? $request->input('description') : null,
                        'status_id' => 0,
                        'type_id' => \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_SELECTION_CASE_PART2
                    ]);
                }

                if( !$project->media->count() )
                {

                    $this->validate($request, [
                        'file' => 'required|file|mimes:pdf|max:20480',
                    ], [
                        'file.required' => 'Необходимо загрузить файл',
                        'file.file' => 'Переданный параметр должен быть файлом',
                        'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    ]);
                }
                
                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    
                    if( $project->media->count() )
                    {
                        foreach ($project->media as $mkey => $media)
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
                    }
                    while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
        
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
                        $project->media()->create($data);
                    }
                }

                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }

                $team->tasks()->attach($task->id);

                event(new \App\Events\UpdateTeamInfoEvent($team));

                return $this->sendResponse($data = [], $message = 'Задание успешно отправлено на проверку', $code = 200, $isRaw = false);

            break;
            case 'team-track-presentation':

                if( $team->projects()->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_PRESENTATION)->count() )
                {
                    $project = $team->projects()->where('type_id', \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_PRESENTATION)->first();
                    
                    $project->update([
                        'status_id' => 0,
                    ]);
                }
                else
                {
                    $project = $team->projects()->create([
                        'name' => $request->filled('name') ? $request->input('name') : $team->name,
                        'description' => $request->filled('description') ? $request->input('description') : null,
                        'status_id' => 0,
                        'type_id' => \App\Models\Teams\Project::CONST_TYPE_TEAM_TRACK_PRESENTATION
                    ]);
                }

                if( !$project->media->count() )
                {

                    $this->validate($request, [
                        'file' => 'required|file|mimes:pdf|max:20480',
                    ], [
                        'file.required' => 'Необходимо загрузить файл',
                        'file.file' => 'Переданный параметр должен быть файлом',
                        'file.mimes' => 'Файл должен быть одного из следующих типов: :values',
                    ]);
                }
                
                if( $request->hasFile('file') && $request->file('file')->isValid() )
                {
                    
                    if( $project->media->count() )
                    {
                        foreach ($project->media as $mkey => $media)
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
                    }
                    while( Media::where('src', $UploadedFileNewName)->first() instanceof Media );
        
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
                        $project->media()->create($data);
                    }
                }

                if( $team->tasks()->where('id', $task->id)->count() )
                {
                    $team->tasks()->detach($task->id);
                }

                $team->tasks()->attach($task->id);

                event(new \App\Events\UpdateTeamInfoEvent($team));

                return $this->sendResponse($data = [], $message = 'Задание успешно отправлено на проверку', $code = 200, $isRaw = false);

            break;
            default:
            break;
        }

        return $this->sendError('Указанное задание отсутствует', $errorMessages = [], $code = 422);
    }
}
