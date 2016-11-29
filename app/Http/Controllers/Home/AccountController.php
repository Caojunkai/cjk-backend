<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\User;
use App\Http\Requests\BaseRequest;
use App\Http\Controllers\Controller;
use App\Jobs\SendReminderEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AccountController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth',[
            'except'=>[
                'register',
                'login',
                'logout',
                'pwdReset',
                'pwdResetByEmail',
            ]
        ]);
    }

    public function register(BaseRequest $request){
        $use_gravatar = true;
        $params = $request->only('username', 'email', 'password');
        $params['password'] = bcrypt($params['password']);

        $user = new User($params);
        // 注册时默认使用Gravatar头像
        $user->useGravatar($use_gravatar, $params);
        if ($user->save()){
            return $this->login($request);
        }
        return $this->formatResponseMsg(500001);
    }

    public function login(Request $request){
        $rules = [
            'email'    => 'required|email|exists:users',
            'password' => 'required|between:6,32',
            'username' => 'between:4,32|alpha_dash|exists:users',
        ];
        $this->validate($request, $rules);
        $requestParams = $request->only('email','password');
        try {
            if (!$token = JWTAuth::attempt($requestParams)) {
                return $this->formatResponseMsg(200001);
            }
            $user = User::with('configs')->find(Auth::id());
            if (!$user)
                return $this->formatResponseMsg(500001);
            $user->jwt_token = [
                'access_token ' => $token,
                'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
            ];
            return $this->success($user);
        } catch (JWTException $e) {
            return $this->formatResponseMsg(500002);
        }
    }

    public function logout(Request $request){
        try{
            JWTAuth::parseToken()->invalidate();
        }catch (TokenBlacklistedException $e){
            return $this->formatResponseMsg(500005);
        }catch (JWTException $e){

        }
        return $this->success();
    }


    public function updateProfile(Request $request)
    {
        //TODO
        $rules = [
            'name'     => 'min:2|max:32',
            'age'      => 'numeric|between:1,100',
            'gender'   => 'in:' . implode(',', [
                    GENDER_UNSPECIFIED,
                    GENDER_SECRECY,
                    GENDER_MALE,
                    GENDER_FEMALE
                ]),
            'birthday' => 'date_format:Y-m-d|before:today'
        ];
        $this->validate($request, $rules);
        $params = $request->except('username', 'email', 'mobile', 'password', 'use_gravatar');
        $use_gravatar = in_array($request->input('use_gravatar'), ['true', 'on', '1']);
        $user = User::find(Auth::id());
        $user->useGravatar($use_gravatar, $params);
        dd($user->withJson());
        if ($user->update($params)) {
            return $this->success($user);
        }
        return $this->failure();
    }

    public function getProfile(Request $request){
        $data = User::find(Auth::id());
        return $this->success($data);
    }

    public function pwdReset(Request $request){
        $rules = [
            'email'    => 'required|email|exists:users',
        ];
        $this->validate($request,$rules);
        $this->dispatch(new SendReminderEmail($request->input('email')));
        return $this->success();
    }

    public function pwdResetByEmail(Request $request){
        $rules = [
            'email' => 'required|email|exists:users',
            'verify' => 'required|between:6,6'
        ];
        $this->validate($request,$rules);
        $email = $request->input('email');
        if (Redis::get($email) == $request->input('verify')){
            $user = User::where('email','=',$email)->first();
            $token = JWTAuth::fromUser($user);
            return $this->success($token);
        }
        return $this->formatResponseMsg(400003);
    }

    public function pwdModify(Request $request){
        $oldPwd = $request->input('oldPwd');
        $newPwd = $request->input('newPwd');
        $rules = [
            'oldPwd' => 'required|between:6,32',
            'newPwd' => 'required|between:6,32'
        ];
        $this->validate($request,$rules);
        $credentials = [
            'id' => Auth::id(),
            'password' => $oldPwd
        ];
        if (!Auth::attempt($credentials,true)){
            return $this->formatResponseMsg(400004);
        }
        $newPwd = bcrypt($newPwd);
        $user = User::find(Auth::id());
        if ($user->update(['password' => $newPwd])){
            return $this->success($user);
        }
        return $this->formatResponseMsg(500006);

    }

}
