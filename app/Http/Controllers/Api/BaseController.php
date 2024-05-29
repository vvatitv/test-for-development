<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    public function sendResponse($data, $message = null, $code = 200, $isRaw = false)
    {
        $response = [
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ];

        if( $isRaw )
        {
            $response = $response['data'];
        }
        
        return response()
                        ->json($response, $code)
                        ->withHeaders([
                            'Cache-Control' => 'no-cache, no-store, must-revalidate',
                            'Pragma' => 'no-cache',
                            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
                        ]);
    }

    public function sendError($message = null, $errorMessageArray = [], $code = 422)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if( !empty($errorMessageArray) )
        {
            $response['data'] = $errorMessageArray;
        }
        
        return response()
                        ->json($response, $code)
                        ->withHeaders([
                            'Cache-Control' => 'no-cache, no-store, must-revalidate',
                            'Pragma' => 'no-cache',
                            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
                        ]);
    }
}
