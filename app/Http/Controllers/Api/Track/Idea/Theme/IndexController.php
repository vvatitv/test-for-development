<?php

namespace App\Http\Controllers\Api\Track\Idea\Theme;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Teams\Track\Idea;
use App\Models\Teams\Track\IdeaTheme;

class IndexController extends BaseController
{
    public function index(\App\Filters\TeamTrackIdeaThemeFilters $filters, Request $request)
    {
        $themes = IdeaTheme::query()->filter($filters)
                ->distinct()
                ->get();

        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $themes instanceof Idea )
            {
                $themes = $themes->load($request->input('with'));
            }else{
                $themes = $themes->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $themes instanceof Idea )
            {
                $themes = $themes->setAppends($request->input('appends'));
            }else{
                $themes = $themes->each->setAppends($request->input('appends'));
            }
        }

        if( $request->filled('returnAs') )
        {
            switch ($request->input('returnAs'))
            {
                case 'count':
                    return $themes->count();
                break;
                default:
                break;
            }
        }

        if( $request->filled('pagination') )
        {
            $request->merge([
                'pagination' => json_decode($request->input('pagination'), true)
            ]);

            $themes = $themes->paginate(
                $perPage = ( $request->filled('pagination.perPage') ? $request->input('pagination.perPage') : 15 ),
                $pageName = ( $request->filled('pagination.pageName') ? $request->input('pagination.pageName') : 'page' ),
                $page = ( $request->filled('pagination.page') ? $request->input('pagination.page') : ( $request->filled('page') ? $request->input('page') : null ) )
            );

            return $this->sendResponse($data = $themes, $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = $themes, $message = null, $code = 200, $isRaw = false);
    }
}