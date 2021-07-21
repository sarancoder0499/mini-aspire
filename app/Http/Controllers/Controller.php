<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
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
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Send Error Response
     *
     * @method sendErrorResponse
     *
     * @param string $code, $msg
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendErrorResponse(string $code, string $msg): JsonResponse
    {
        $code = Config('constants.STATUS_CODE.' . $code);
        return response()->json([
            "status" => false,
            "code" => $code,
            "message" => $msg,
        ], $code);
    }

    /**
     * Send Success Response
     *
     * @method sendSuccessResponse
     *
     * @param string $msg
     *
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSuccessResponse(string $msg, array $data = []): JsonResponse
    {
        $code = Config('constants.STATUS_CODE.OK');
        $output = [
            "status" => true,
            "code" => $code,
            "message" => $msg,
        ];
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $output[$key] = $value;
            }
        }
        return response()->json($output, $code);
    }
}
