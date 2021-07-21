<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Service\UserService;
use Illuminate\Support\Str;
use Exception;


class RegisterController extends Controller
{

    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }
   
    /**
     * Register new User
     *
     * @method registerUser
     * 
     * @param RegisterRequest Request
     * 
     * @return \Illuminate\Http\Response
     */

     /**
        * @OA\Post(
        ** path="/api/register",
        *   tags={"Authentication"},
        *   summary="- Register",
        *   operationId="register",
        *
        *   @OA\Parameter(
        *      name="name",
        *      in="query",
        *      required=true,
        *      @OA\Schema(
        *           type="string",
        *      )
        *   ),
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
        *      description="Success",
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

    public function registerUser(RegisterRequest $request)
    {
        try{
            
            $request['password'] = bcrypt($request['password']);
            $request['remember_token'] = Str::random(10);
            
            $user = $this->service->store($request->all());

            $tokenResult = $user->createToken('access_token');
            $accessToken = $tokenResult->accessToken;
            $token = $tokenResult->token;
            $token->expires_at = now()->addDays(config('constants.TOKEN_EXPIRY'));
            $token->save();
        
            $data = [ "user" => auth()->user(), "token" => $accessToken ];
            return $this->sendSuccessResponse("Registration Success",$data);

        }catch(Exception $e)
        {
            return $this->sendErrorResponse('INTERNAL_SERVER_ERROR',$e->getMessage());
        }
    }
}
