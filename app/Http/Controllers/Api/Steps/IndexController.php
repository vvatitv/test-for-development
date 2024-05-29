<?php

namespace App\Http\Controllers\Api\Steps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Step;

class IndexController extends BaseController
{
    public function index(\App\Filters\StepFilters $filters, Request $request)
    {
        $rows = Step::query()->filter($filters)->distinct()->get();
        
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $rows instanceof Step )
            {
                $rows = $rows->load($request->input('with'));
            }else{
                $rows = $rows->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $rows instanceof Step )
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
            return $this->sendResponse($data = new \App\Http\Resources\Step\PaginateResource($rows), $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = \App\Http\Resources\Step\IndexResource::collection($rows), $message = null, $code = 200, $isRaw = false);
    }

    public function show(Step $step, Request $request)
    {
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $step instanceof Step )
            {
                $step = $step->load($request->input('with'));
            }else{
                $step = $step->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $step instanceof Step )
            {
                $step = $step->setAppends($request->input('appends'));
            }else{
                $step = $step->each->setAppends($request->input('appends'));
            }
        }

        return $this->sendResponse($data = new \App\Http\Resources\Step\IndexResource($step), $message = null, $code = 200, $isRaw = false);
    }


    public function update(Step $step, Request $request)
    {
        $authUser = $request->user();

        if( empty($authUser) || !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $step->update($request->only($step->getFillable()));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }
}
