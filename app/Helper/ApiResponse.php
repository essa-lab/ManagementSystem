<?php

namespace App\Helper;

use Illuminate\Contracts\Pagination\Paginator;

class ApiResponse 
{

    public static function sendResponse( $message,$data = null, $statusCode = 200)
{
    $response = ['message' => $message];

    if (!is_null($data)) {
        $response['data'] = $data;
    }

    return response()->json($response, $statusCode);
}
    public static function sendError($message,$statusCode=404){
        return response()->json([
            'message'=>$message
        ],$statusCode);
    }

    public static function sendPaginatedResponse( $data , $statusCode = 200)
    {
       
    
        return response()->json($data, $statusCode);
    }
}
