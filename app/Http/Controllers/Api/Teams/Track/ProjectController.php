<?php

namespace App\Http\Controllers\Api\Teams\Track;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Team;
use App\Models\User;
use App\Models\Task;
use App\Models\Teams\Project;

class ProjectController extends BaseController
{
    public function takeSurveyApproved(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtracktakesurveys' => 'required|array'
        ]);

        $ideas = Project::whereIn('id', $request->input('teamtracktakesurveys'))
                        ->orWhereIn('slug', $request->input('teamtracktakesurveys'))
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
                                $subject = 'Проектная культура. Ваше задание одобрено',
                                $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваше задание «№9 Задания треков» трека «Проектная культура» принято модератором.</p><p><br></p><p>Промежуточные результаты и списки организаций для выездов будут определены <strong>до 1 июля</strong> в <a href="https://t.me/zdravyemysli" target="_blank">телеграм-канале «Здравые мысли»</a> и отправлены по почте.</p><p><br></p><p>Финальные результаты трека и информация о победителях будут опубликованы <strong>до 10 июля</strong>.</p>',
                                $button = null,
                                $notification_id = null,
                            ))
                        );
                    }
                }
            }

            if( !$idea->team->tasks()->where('slug', 'team-track-take-survey')->exists() )
            {
                $idea->team->tasks()->attach(Task::where('slug', 'team-track-take-survey')->first()->id);
            }

        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function takeSurveyReject(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtracktakesurveys' => 'required|array'
        ]);

        $ideas = Project::whereIn('id', $request->input('teamtracktakesurveys'))
                        ->orWhereIn('slug', $request->input('teamtracktakesurveys'))
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
            
            if( $idea->team->tasks()->where('slug', 'team-track-take-survey')->exists() )
            {
                $idea->team->tasks()->detach(Task::where('slug', 'team-track-take-survey')->first()->id);
            }
            
            event(new \App\Events\UpdateTeamInfoEvent($idea->team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function takeSurveyCancel(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtracktakesurveys' => 'required|array'
        ]);

        $rows = Project::whereIn('id', $request->input('teamtracktakesurveys'))
                        ->orWhereIn('slug', $request->input('teamtracktakesurveys'))
                        ->get();

        foreach ($rows as $key => $row)
        {
            $row->update([
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
                            if( !$member->mentorIn()->where('team_id', $row->team->id)->count() )
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
                            if( !empty($row->team->lead) )
                            {
                                if( !$row->team->lead->mentorIn()->where('team_id', $row->team->id)->count() )
                                {
                                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                                        $row->team->lead,
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
                            if( $row->team->members->count() )
                            {
                                foreach ($row->team->members as $key => $member)
                                {
                                    if( !$member->mentorIn()->where('team_id', $row->team->id)->count() )
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
            
            if( $row->team->tasks()->where('slug', 'team-track-take-survey')->exists() )
            {
                $row->team->tasks()->detach(Task::where('slug', 'team-track-take-survey')->first()->id);
            }

            event(new \App\Events\UpdateTeamInfoEvent($row->team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function takeSurveyUpdate(Request $request)
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

        $team->teamtracktakesurvey->update($request->only($team->teamtracktakesurvey->getFillable()));

        event(new \App\Events\UpdateTeamInfoEvent($team));

        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
    }

    public function selectionCasePart2Approved(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtrackselectioncasepart2s' => 'required|array'
        ]);

        $ideas = Project::whereIn('id', $request->input('teamtrackselectioncasepart2s'))
                        ->orWhereIn('slug', $request->input('teamtrackselectioncasepart2s'))
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
                                $subject = 'Проектное решение. Ваше задание одобрено',
                                $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваше задание «№11 Задания треков» трека «Проектное решение» принято модератором.</p><p><br></p><p>Предзащита вашего проекта пройдет в онлайн-формате. Информация о финальной дате и времени предзащиты будет доступна <a href="https://clck.ru/3AFHhN" target="_blank">по ссылке</a>. Ссылка на встречу будет направлена за день до выступления.</p>',
                                $button = null,
                                $notification_id = null,
                            ))
                        );
                    }
                }
            }

            if( !$idea->team->tasks()->where('slug', 'team-track-selection-case-part-2')->exists() )
            {
                $idea->team->tasks()->attach(Task::where('slug', 'team-track-selection-case-part-2')->first()->id);
            }

        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function selectionCasePart2Reject(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtrackselectioncasepart2s' => 'required|array'
        ]);

        $ideas = Project::whereIn('id', $request->input('teamtrackselectioncasepart2s'))
                        ->orWhereIn('slug', $request->input('teamtrackselectioncasepart2s'))
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
            
            if( $idea->team->tasks()->where('slug', 'team-track-selection-case-part-2')->exists() )
            {
                $idea->team->tasks()->detach(Task::where('slug', 'team-track-selection-case-part-2')->first()->id);
            }
            
            event(new \App\Events\UpdateTeamInfoEvent($idea->team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function selectionCasePart2Cancel(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtrackselectioncasepart2s' => 'required|array'
        ]);

        $rows = Project::whereIn('id', $request->input('teamtrackselectioncasepart2s'))
                        ->orWhereIn('slug', $request->input('teamtrackselectioncasepart2s'))
                        ->get();

        foreach ($rows as $key => $row)
        {
            $row->update([
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
                            if( !$member->mentorIn()->where('team_id', $row->team->id)->count() )
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
                }
                else
                {
                    switch ($request->input('notification.type'))
                    {
                        case 'lead':
                            if( !empty($row->team->lead) )
                            {
                                if( !$row->team->lead->mentorIn()->where('team_id', $row->team->id)->count() )
                                {
                                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                                        $row->team->lead,
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
                            if( $row->team->members->count() )
                            {
                                foreach ($row->team->members as $key => $member)
                                {
                                    if( !$member->mentorIn()->where('team_id', $row->team->id)->count() )
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
            
            if( $row->team->tasks()->where('slug', 'team-track-selection-case-part-2')->exists() )
            {
                $row->team->tasks()->detach(Task::where('slug', 'team-track-selection-case-part-2')->first()->id);
            }

            event(new \App\Events\UpdateTeamInfoEvent($row->team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function selectionCasePart2Update(Request $request)
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

        $request->request->remove('team');

        $team->teamtrackselectioncasepart2->update($request->only($team->teamtrackselectioncasepart2->getFillable()));

        event(new \App\Events\UpdateTeamInfoEvent($team));

        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
    }

    public function presentationApproved(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtrackpresentations' => 'required|array'
        ]);

        $ideas = Project::whereIn('id', $request->input('teamtrackpresentations'))
                        ->orWhereIn('slug', $request->input('teamtrackpresentations'))
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
                                $subject = 'Проектное решение. Ваше задание одобрено',
                                $message = '<h1>Здравствуйте!</h1><p><br></p><p>Ваше задание «№15 Задания треков» трека «Проектное решение» принято модератором.</p><p><br></p><p>Промежуточные результаты и списки организаций для выездов будут определены <strong>до 1 июля</strong> в <a href="https://t.me/zdravyemysli" target="_blank">телеграм-канале «Здравые мысли»</a> и отправлены по почте.</p><p><br></p><p>Финальные результаты трека и информация о победителях будут опубликованы <strong>до 10 июля</strong>.</p>',
                                $button = null,
                                $notification_id = null,
                            ))
                        );
                    }
                }
            }

            if( !$idea->team->tasks()->where('slug', 'team-track-presentation')->exists() )
            {
                $idea->team->tasks()->attach(Task::where('slug', 'team-track-presentation')->first()->id);
            }

        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function presentationReject(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtrackpresentations' => 'required|array'
        ]);

        $ideas = Project::whereIn('id', $request->input('teamtrackpresentations'))
                        ->orWhereIn('slug', $request->input('teamtrackpresentations'))
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
            
            if( $idea->team->tasks()->where('slug', 'team-track-presentation')->exists() )
            {
                $idea->team->tasks()->detach(Task::where('slug', 'team-track-presentation')->first()->id);
            }
            
            event(new \App\Events\UpdateTeamInfoEvent($idea->team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function presentationCancel(Request $request)
    {
        $authUser = $request->user();

        if( !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teamtrackpresentations' => 'required|array'
        ]);

        $rows = Project::whereIn('id', $request->input('teamtrackpresentations'))
                        ->orWhereIn('slug', $request->input('teamtrackpresentations'))
                        ->get();

        foreach ($rows as $key => $row)
        {
            $row->update([
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
                            if( !$member->mentorIn()->where('team_id', $row->team->id)->count() )
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
                            if( !empty($row->team->lead) )
                            {
                                if( !$row->team->lead->mentorIn()->where('team_id', $row->team->id)->count() )
                                {
                                    \App\Jobs\EmailUsersNotificationJob::dispatch(
                                        $row->team->lead,
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
                            if( $row->team->members->count() )
                            {
                                foreach ($row->team->members as $key => $member)
                                {
                                    if( !$member->mentorIn()->where('team_id', $row->team->id)->count() )
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
            
            if( $row->team->tasks()->where('slug', 'team-track-presentation')->exists() )
            {
                $row->team->tasks()->detach(Task::where('slug', 'team-track-presentation')->first()->id);
            }

            event(new \App\Events\UpdateTeamInfoEvent($row->team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function presentationUpdate(Request $request)
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

        $team->teamtrackpresentation->update($request->only($team->teamtrackpresentation->getFillable()));

        event(new \App\Events\UpdateTeamInfoEvent($team));

        return $this->sendResponse($data = [], $message = 'Информация обновлена', $code = 200, $isRaw = false);
    }
}