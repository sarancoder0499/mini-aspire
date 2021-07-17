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
        
            return response()->json([
                'status'=>true,
                'msg'=>'Registration Success',
                'token' => $accessToken,
                'user'=> $user
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
