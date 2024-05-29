<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Instruction;
use App\Models\User;

class InstructionController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $instructions = $user->instructions;

        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $instructions instanceof Instruction )
            {
                $instructions = $instructions->load($request->input('with'));
            }else{
                $instructions = $instructions->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $instructions instanceof Instruction )
            {
                $instructions = $instructions->setAppends($request->input('appends'));
            }else{
                $instructions = $instructions->each->setAppends($request->input('appends'));
            }
        }
        
        return $this->sendResponse($data = $instructions, $message = null, $code = 200, $isRaw = false);
    }

    public function set(Instruction $instruction, Request $request)
    {
        $user = $request->user();

        if( !$user->instructions()->where('id', $instruction->id)->count() )
        {
            $user->instructions()->attach(
                $instruction->id
            );
        }

        event(new \App\Events\UpdateUserInfoEvent($user));
                
        return $this->sendResponse($data = [], $message = 'Информация изменена', $code = 200, $isRaw = false);
    }

    public function unSet(Instruction $instruction, Request $request)
    {
        $user = $request->user();

        if( $user->instructions()->where('id', $instruction->id)->count() )
        {
            $user->instructions()->detach(
                $instruction->id
            );
        }

        event(new \App\Events\UpdateUserInfoEvent($user));

        return $this->sendResponse($data = [], $message = 'Информация изменена', $code = 200, $isRaw = false);
    }
}