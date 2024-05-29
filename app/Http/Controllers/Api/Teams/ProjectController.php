<?php

namespace App\Http\Controllers\Api\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Team;

class ProjectController extends BaseController
{
    public function teamtracktakesurveyUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

		$team->teamtracktakesurvey->update($request->only($team->teamtracktakesurvey->getFillable()));

        event(new \App\Events\UpdateTeamInfoEvent($team));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
	}

    public function teamtrackselectioncasepart2Update(Team $team, Request $request)
    {
        $user = $request->user();

        if( !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

		$team->teamtrackselectioncasepart2->update($request->only($team->teamtrackselectioncasepart2->getFillable()));

        event(new \App\Events\UpdateTeamInfoEvent($team));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
	}

    public function teamtakequestUpdate(Team $team, Request $request)
    {
        $user = $request->user();

        if( !$user->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

		$team->teamtakequest->update($request->only($team->teamtakequest->getFillable()));

        event(new \App\Events\UpdateTeamInfoEvent($team));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
	}
}
