<?php

namespace App\Http\Controllers\Api\Track;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Track;

class IndexController extends BaseController
{
    public function index(\App\Filters\TrackFilters $filters, Request $request)
    {
        $rows = Track::query()->filter($filters)->distinct()->get();
        
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $rows instanceof Track )
            {
                $rows = $rows->load($request->input('with'));
            }else{
                $rows = $rows->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $rows instanceof Track )
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
            return $this->sendResponse($data = $rows, $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = $rows, $message = null, $code = 200, $isRaw = false);
    }
}
