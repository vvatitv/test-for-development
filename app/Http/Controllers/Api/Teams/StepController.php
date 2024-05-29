<?php

namespace App\Http\Controllers\Api\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Step;
use App\Models\Team;

class StepController extends BaseController
{
    public function update(Request $request)
    {
        $user = $request->user();

        if( !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teams' => 'required|array',
            'steps' => 'required|array',
        ], [
            'teams.required' => 'Список команд обязателен для заполнения',
            'teams.array' => 'Список команд должен быть массивом',
            'steps.required' => 'Список этапов команды обязателен для заполнения',
            'steps.array' => 'Список этапов должен быть массивом'
        ]);

        $teams = Team::whereIn('id', $request->input('teams'))->get();
        $steps = Step::whereIn('id', $request->input('steps'))->get();

        if( !$teams->count() )
        {
            return $this->sendError('Выбранные команды отсутствуют', $errorMessages = [], $code = 422);
        }

        if( !$steps->count() )
        {
            return $this->sendError('Выбранные этапы отсутствуют', $errorMessages = [], $code = 422);
        }

        foreach ($teams as $key => $team)
        {
            if( $team->steps()->count() )
            {
                $team->steps()->detach();
            }

            $team->steps()->attach($steps->pluck('id'));

            event(new \App\Events\UpdateTeamInfoEvent($team));
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function attach(Request $request)
    {
        $user = $request->user();

        if( !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teams' => 'required|array',
            'steps' => 'required|array',
        ], [
            'teams.required' => 'Список команд обязателен для заполнения',
            'teams.array' => 'Список команд должен быть массивом',
            'steps.required' => 'Список этапов команды обязателен для заполнения',
            'steps.array' => 'Список этапов должен быть массивом'
        ]);

        $teams = Team::whereIn('id', $request->input('teams'))->get();
        $steps = Step::whereIn('id', $request->input('steps'))->get();

        if( !$teams->count() )
        {
            return $this->sendError('Выбранные команды отсутствуют', $errorMessages = [], $code = 422);
        }

        if( !$steps->count() )
        {
            return $this->sendError('Выбранные этапы отсутствуют', $errorMessages = [], $code = 422);
        }

        foreach ($teams as $key => $team)
        {
            foreach ($steps as $skey => $step)
            {
                if( !$team->steps()->where('id', $step->id)->count() )
                {
                    $team->steps()->attach($step->id);
                    event(new \App\Events\UpdateTeamInfoEvent($team));
                }
            }
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function detach(Request $request)
    {
        $user = $request->user();

        if( !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $this->validate($request, [
            'teams' => 'required|array',
            'steps' => 'required|array',
        ], [
            'teams.required' => 'Список команд обязателен для заполнения',
            'teams.array' => 'Список команд должен быть массивом',
            'steps.required' => 'Список этапов команды обязателен для заполнения',
            'steps.array' => 'Список этапов должен быть массивом'
        ]);

        $teams = Team::whereIn('id', $request->input('teams'))->get();
        $steps = Step::whereIn('id', $request->input('steps'))->get();

        if( !$teams->count() )
        {
            return $this->sendError('Выбранные команды отсутствуют', $errorMessages = [], $code = 422);
        }

        if( !$steps->count() )
        {
            return $this->sendError('Выбранные этапы отсутствуют', $errorMessages = [], $code = 422);
        }

        foreach ($teams as $key => $team)
        {
            foreach ($steps as $skey => $step)
            {
                if( $team->steps()->where('id', $step->id)->count() )
                {
                    $team->steps()->detach($step->id);
                    event(new \App\Events\UpdateTeamInfoEvent($team));
                }
            }
        }

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }
}