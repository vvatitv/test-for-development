<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Setting;

class IndexController extends BaseController
{
    public function index(Request $request)
    {
        $rows = Setting::all();
        
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $rows instanceof Setting )
            {
                $rows = $rows->load($request->input('with'));
            }else{
                $rows = $rows->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $rows instanceof Setting )
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
            return $this->sendResponse($data = new \App\Http\Resources\Setting\PaginateResource($rows), $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = \App\Http\Resources\Setting\IndexResource::collection($rows), $message = null, $code = 200, $isRaw = false);
    }

    public function show(Setting $setting, Request $request)
    {
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $setting instanceof Setting )
            {
                $setting = $setting->load($request->input('with'));
            }else{
                $setting = $setting->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $setting instanceof Setting )
            {
                $setting = $setting->setAppends($request->input('appends'));
            }else{
                $setting = $setting->each->setAppends($request->input('appends'));
            }
        }

        return $this->sendResponse($data = new \App\Http\Resources\Setting\IndexResource($setting), $message = null, $code = 200, $isRaw = false);
    }

    public function update(Setting $setting, Request $request)
    {
        $authUser = $request->user();

        if( empty($authUser) || !$authUser->hasRole('admin') )
        {
            return $this->sendError('Don\'t have permission', $errorMessages = [], $code = 422);
        }

        $setting->update($request->only($setting->getFillable()));
        
        return $this->sendResponse($data = null, $message = 'completed', $code = 200, $isRaw = false);
    }
}