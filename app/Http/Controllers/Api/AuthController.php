<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Validator;

class AuthController extends BaseController
{
    public function DoLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return $this->sendError('Please fill all mandatory fields.',$validator->messages()->toArray());
        }
        $UserAvailability = User::where('email',$request->email)->count();
        if($UserAvailability == 0){
            return $this->sendError('This email is not registered. Please sign-up to continue further.');
        }
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){
            return $this->sendError('Invalid Credentials');
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        // $token = $tokenResult->token;
        $result = [
            'user_data'=>$user->toArray(),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ];
        return $this->sendResponse($this->setData($result), 'Login successfully.');
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->sendResponse([], 'Successfully logged out');
    }
    public function DoRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            // 'email' => 'required|string|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string|confirmed',
        ]);
        if($validator->fails()){
            return $this->sendError('Please fill all mandatory fields.',$validator->messages()->toArray());
        }
        $user = User::create([
		    'name' => $request->name,
	        'email' => $request->email,
	        'password' => bcrypt($request->password),
		]);
        // $user->notify(
        //     new EmailVerificationToken($token)
        // );
        $tokenResult = $user->createToken('Personal Access Token');
        $response = [
            'user_data' => $this->setData($user->toArray()),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ];
        return $this->sendResponse($this->setData($response), 'Register Successfully!');
    }

}
