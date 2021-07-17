<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;

class LoginController extends Controller
{

    /**
     * Attempt login with the credentials.
     *
     * @method loginUser
     * 
     * @param LoginRequest Request
     * 
     * @return \Illuminate\Http\Response
     */
     
     /**
        * @OA\Post(
        ** path="/api/login",
        *   tags={"Authentication"},
        *   summary="- Sign In",
        *   operationId="login",
        *
        *   @OA\Parameter(
        *      name="email",
        *      in="query",
        *      required=true,
        *      @OA\Schema(
        *           type="string",
        *      )
        *   ),
        *   @OA\Parameter(
        *      name="password",
        *      in="query",
        *      required=true,
        *      @OA\Schema(
        *          type="string"
        *      )
        *   ),
        *   @OA\Response(
        *      response=200,
        *      description="Login Successful",
        *      @OA\MediaType(
        *           mediaType="application/json",
        *      )
        *   ),
        *   @OA\Response(
        *    response=222,
        *    description="Validation Error Messages",
        *    @OA\JsonContent(
        *       @OA\Property(property="status", type="string", example="false"),
        *       @OA\Property(property="result", type="string", example="[]"),
        *    )
        *  ),
        *)
    **/
    public function loginUser(LoginRequest $request)
    {
        try {
            
            if (!auth()->attempt($request->all())) {
                return response(["msg" => "Invalid Credentials"],Config('constants.STATUS_CODE.UNPROCESSABLE_ENTITY'));
            }

            $tokenResult = auth()->user()->createToken('access_token');
            $accessToken = $tokenResult->accessToken;
            $token = $tokenResult->token;
            $token->expires_at = now()->addDays(config('constants.TOKEN_EXPIRY'));
            $token->save();

            return response()->json([
                'status'=>true,
                'msg'=>'Login Successful',
                'user'=>auth()->user(),
                'token' => $accessToken,
            ],Config('constants.STATUS_CODE.OK'));

        }catch(Exception $e)
        {
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage(),
            ],Config('constants.STATUS_CODE.INTERNAL_SERVER_ERROR'));
        }
    }
}
