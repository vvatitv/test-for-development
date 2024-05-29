<?php

namespace App\Http\Controllers\Api\Steps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\Models\Step;
use App\Models\Task;

class TaskController extends BaseController
{
    public function index(Request $request)
    {
        $rows = Task::all();
        
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

    public function update(Task $task, Request $request)
    {
        $authUser = $request->user();

        if( empty($authUser) || !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $task->update($request->only($task->getFillable()));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }

    public function show(Task $task, Request $request)
    {
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $task instanceof Task )
            {
                $task = $task->load($request->input('with'));
            }else{
                $task = $task->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $task instanceof Task )
            {
                $task = $task->setAppends($request->input('appends'));
            }else{
                $task = $task->each->setAppends($request->input('appends'));
            }
        }

        return $this->sendResponse($data = new \App\Http\Resources\Task\IndexResource($task), $message = null, $code = 200, $isRaw = false);
    }
}