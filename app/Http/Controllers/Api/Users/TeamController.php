<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use App\Models\Team;

class TeamController extends BaseController
{
    public function setDefault(User $user, Team $team, Request $request)
    {
        $authUser = $request->user();
        
        if( $user->id <> $authUser->id && !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        if( $user->currentTeam->count() )
        {
            $user->currentTeam()->detach();
        }

        $user->currentTeam()->attach($team->id);

        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }
}
