<?php

namespace App\Http\Controllers\Api\Teams\Track\Idea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Team;
use App\Models\User;
use App\Models\Task;
use App\Models\Track;
use App\Models\Teams\Track\Idea;
use App\Models\Teams\Track\IdeaTheme;

class IndexController extends BaseController
{

    public function update(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'team' => 'required'
        ]);

        $team = Team::where('slug', $request->input('team'))->first();

        if( empty($team) )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $team->tracksIdea->update($request->only($team->tracksIdea->getFillable()));

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

            if( $team->tracksIdea->themes->count() )
            {
                $team->tracksIdea->themes()->detach();
            }
            $team->tracksIdea->themes()->attach($theme->id);
        }

        event(new \App\Events\UpdateTeamInfoEvent($team));

        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
    }

    public function approved(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'tracks_ideas' => 'required|array'
        ]);

        $ideas = Idea::whereIn('id', $request->input('tracks_ideas'))
                        ->orWhereIn('slug', $request->input('tracks_ideas'))
                        ->get();

        foreach ($ideas as $idea)
        {
            $currentStatus = $idea->status_id;

            $idea->update([
                'status_id' => $request->filled('status_id') ? $request->input('status_id') : 1
            ]);

            if( $request->filled('notification') )
            {
                $this->validate($request, [
                    'notification' => 'array',
                    'notification.subject' => 'required|string',
                    'notification.message' => 'required|string',
                    'notification.type' => 'required',
                ], [
                    'notification.array' => 'Параметры для уведомления, должны быть массивом',
                    'notification.subject.required' => 'Необходимо указать заголовок письма',
                    'notification.message.required' => 'Необходимо указать текст письма',
                    'notification.type.required' => 'Необходимо указать тип письма, кому именно отправляется письмо',
                ]);
                
                if( is_array($request->input('notification.type')) )
                {
                    $users = User::whereIn('id', $request->input('notification.type'))->get();

                    if( $users->count() )
                    {
                        foreach ($users as $key => $member)
                        {
                            if( !$member->mentorIn()->where('team_id', $idea->team->id)->count() )
                            {
                                \App\Jobs\EmailUsersNotificationJob::dispatch(
                                    $member,
                                    (new \App\Notifications\MessageNotification(
                                        $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                        $subject = $request->input('notification.subject'),
                                        $message = $request->input('notification.message'),
                                        $notification_id = null,
                                    ))
                                );
                            }
                        }
                    }
                }
                else
                {
                    switch ($request->input('notification.type'))
                    {
                        case 'lead':
                            if( !empty($idea->team->lead) )
                            {
                                if( !$idea->team->lead->mentorIn()->where('team_id', $idea->team->id)->count() )
                                {
                                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                                        $idea->team->lead,
                                        (new \App\Notifications\MessageNotification(
                                            $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                            $subject = $request->input('notification.subject'),
                                            $message = $request->input('notification.message'),
                                            $button = $request->input('notification.button'),
                                            $notification_id = null,
                                        ))
                                    );
                                }
                            }
                        break;
                        case 'members':
                        default:
                            if( $idea->team->members->count() )
                            {
                                foreach ($idea->team->members as $key => $member)
                                {
                                    if( !$member->mentorIn()->where('team_id', $idea->team->id)->count() )
                                    {
                                        \App\Jobs\EmailUsersNotificationJob::dispatch(
                                            $member,
                                            (new \App\Notifications\MessageNotification(
                                                $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                                $subject = $request->input('notification.subject'),
                                                $message = $request->input('notification.message'),
                                                $button = $request->input('notification.button'),
                                                $notification_id = null,
                                            ))
                                        );
                                    }
                                }
                            }
                        break;
                    }
                }
            }

            event(new \App\Events\UpdateTeamInfoEvent($idea->team));

            if( $idea->team->members->count() && $idea->status_id == 1 )
            {
                foreach ($idea->team->members as $member)
                {
                    if( !$member->mentorIn()->where('team_id', $idea->team->id)->count() )
                    {
                        \App\Jobs\EmailUsersNotificationJob::dispatch(
                            $member,
                            (new \App\Notifications\MessageNotification(
                                $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                $subject = 'Проектный опыт. Ваше задание одобрено',
                                $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваше задание «№8 Задания треков» трека «Проектный опыт» принято модератором.</p><p><br></p><p>Результаты оценки проектов участников трека и информация о победителях будут опубликованы <strong>1 июля</strong> в <a href="https://t.me/zdravyemysli" target="_blank">телеграм-канале «Здравые мысли»</a> и отправлены по почте.</p>',
                                $button = null,
                                $notification_id = null,
                            ))
                        );
                    }
                }
            }

            if( !$idea->team->tasks()->where('slug', 'team-track-project')->exists() )
            {
                $idea->team->tasks()->attach(Task::where('slug', 'team-track-project')->first()->id);
            }

        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function reject(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'tracks_ideas' => 'required|array'
        ]);

        $ideas = Idea::whereIn('id', $request->input('tracks_ideas'))
                        ->orWhereIn('slug', $request->input('tracks_ideas'))
                        ->get();

        foreach ($ideas as $key => $idea)
        {
            $idea->update([
                'status_id' => $request->filled('status_id') ? $request->input('status_id') : 2
            ]);

            if( $request->filled('notification') )
            {
                $this->validate($request, [
                    'notification' => 'array',
                    'notification.subject' => 'required|string',
                    'notification.message' => 'required|string',
                    'notification.type' => 'required',
                ], [
                    'notification.array' => 'Параметры для уведомления, должны быть массивом',
                    'notification.subject.required' => 'Необходимо указать заголовок письма',
                    'notification.message.required' => 'Необходимо указать текст письма',
                    'notification.type.required' => 'Необходимо указать тип письма, кому именно отправляется письмо',
                ]);
                
                if( is_array($request->input('notification.type')) )
                {
                    $users = User::whereIn('id', $request->input('notification.type'))->get();

                    if( $users->count() )
                    {
                        foreach ($users as $key => $member)
                        {
                            if( !$member->mentorIn()->where('team_id', $idea->team->id)->count() )
                            {
                                \App\Jobs\EmailUsersNotificationJob::dispatch(
                                    $member,
                                    (new \App\Notifications\MessageNotification(
                                        $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                        $subject = $request->input('notification.subject'),
                                        $message = $request->input('notification.message'),
                                        $button = $request->input('notification.button'),
                                        $notification_id = null,
                                    ))
                                );
                            }
                        }
                    }
                }else{
                    switch ($request->input('notification.type'))
                    {
                        case 'lead':
                            if( !empty($idea->team->lead) )
                            {
                                if( !$idea->team->lead->mentorIn()->where('team_id', $idea->team->id)->count() )
                                {
                                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                                        $idea->team->lead,
                                        (new \App\Notifications\MessageNotification(
                                            $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                            $subject = $request->input('notification.subject'),
                                            $message = $request->input('notification.message'),
                                            $button = $request->input('notification.button'),
                                            $notification_id = null,
                                        ))
                                    );
                                }
                            }
                        break;
                        case 'members':
                        default:
                            if( $idea->team->members->count() )
                            {
                                foreach ($idea->team->members as $key => $member)
                                {
                                    if( !$member->mentorIn()->where('team_id', $idea->team->id)->count() )
                                    {
                                        \App\Jobs\EmailUsersNotificationJob::dispatch(
                                            $member,
                                            (new \App\Notifications\MessageNotification(
                                                $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                                $subject = $request->input('notification.subject'),
                                                $message = $request->input('notification.message'),
                                                $button = $request->input('notification.button'),
                                                $notification_id = null,
                                            ))
                                        );
                                    }
                                }
                            }
                        break;
                    }
                }
            }
            
            if( $idea->team->tasks()->where('slug', 'team-track-project')->exists() )
            {
                $idea->team->tasks()->detach(Task::where('slug', 'team-track-project')->first()->id);
            }
            
            event(new \App\Events\UpdateTeamInfoEvent($idea->team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function cancel(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'tracks_ideas' => 'required|array'
        ]);

        $ideas = Idea::whereIn('id', $request->input('tracks_ideas'))
                        ->orWhereIn('slug', $request->input('tracks_ideas'))
                        ->get();

        foreach ($ideas as $key => $idea)
        {
            $idea->update([
                'status_id' => $request->filled('status_id') ? $request->input('status_id') : 0
            ]);
            
            if( $request->filled('notification') )
            {
                $this->validate($request, [
                    'notification' => 'array',
                    'notification.subject' => 'required|string',
                    'notification.message' => 'required|string',
                    'notification.type' => 'required',
                ], [
                    'notification.array' => 'Параметры для уведомления, должны быть массивом',
                    'notification.subject.required' => 'Необходимо указать заголовок письма',
                    'notification.message.required' => 'Необходимо указать текст письма',
                    'notification.type.required' => 'Необходимо указать тип письма, кому именно отправляется письмо',
                ]);
                
                if( is_array($request->input('notification.type')) )
                {
                    $users = User::whereIn('id', $request->input('notification.type'))->get();

                    if( $users->count() )
                    {
                        foreach ($users as $key => $member)
                        {
                            if( !$member->mentorIn()->where('team_id', $idea->team->id)->count() )
                            {
                                \App\Jobs\EmailUsersNotificationJob::dispatch(
                                    $member,
                                    (new \App\Notifications\MessageNotification(
                                        $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                        $subject = $request->input('notification.subject'),
                                        $message = $request->input('notification.message'),
                                        $button = $request->input('notification.button'),
                                        $notification_id = null,
                                    ))
                                );
                            }
                        }
                    }
                }else{
                    switch ($request->input('notification.type'))
                    {
                        case 'lead':
                            if( !empty($idea->team->lead) )
                            {
                                if( !$idea->team->lead->mentorIn()->where('team_id', $idea->team->id)->count() )
                                {
                                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                                        $idea->team->lead,
                                        (new \App\Notifications\MessageNotification(
                                            $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                            $subject = $request->input('notification.subject'),
                                            $message = $request->input('notification.message'),
                                            $button = $request->input('notification.button'),
                                            $notification_id = null,
                                        ))
                                    );
                                }
                            }
                        break;
                        case 'members':
                        default:
                            if( $idea->team->members->count() )
                            {
                                foreach ($idea->team->members as $key => $member)
                                {
                                    if( !$member->mentorIn()->where('team_id', $idea->team->id)->count() )
                                    {
                                        \App\Jobs\EmailUsersNotificationJob::dispatch(
                                            $member,
                                            (new \App\Notifications\MessageNotification(
                                                $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                                                $subject = $request->input('notification.subject'),
                                                $message = $request->input('notification.message'),
                                                $button = $request->input('notification.button'),
                                                $notification_id = null,
                                            ))
                                        );
                                    }
                                }
                            }
                        break;
                    }
                }
            }
            
            if( $idea->team->tasks()->where('slug', 'team-track-project')->exists() )
            {
                $idea->team->tasks()->detach(Task::where('slug', 'team-track-project')->first()->id);
            }

            event(new \App\Events\UpdateTeamInfoEvent($idea->team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }
}
