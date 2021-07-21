<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(
 *    title="Mini Aspire Application API",
 *    version="1.0.0",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendErrorResponse($code,$msg)
    {
        $code = Config('constants.STATUS_CODE.'.$code);
        return response()->json([
            "status" => false,
            "code" => $code,
            "message" => $msg,
        ],$code);
    }

    public function sendSuccessResponse($msg,$data = [])
    {
        $code = Config('constants.STATUS_CODE.OK');
        $output = [
            "status" => true,
            "code" => $code,
            "message" => $msg,
        ];
        if(count($data) > 0 )
        {
            foreach($data as $key => $value)
            {
                $output[$key] = $value;
            }
        }
        
        return response()->json($output,$code);
    }
}
