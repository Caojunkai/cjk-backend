<?php

namespace App\Http\Controllers\Home;

use App\Events\ShippingStatusUpdated;
use App\Http\Models\User;
use App\Http\Requests\BaseRequest;
use App\Http\Controllers\Controller;
use App\Jobs\SendReminderEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use DB;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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
    //TODO 注册时邮箱验证
    public function register(Request $request){
        $rules = [
            'username' => 'required|between:4,32|alpha_dash|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|between:6,32',
        ];
        $this->validate($request, $rules);
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
        if ($user->update($params)) {
            return $this->success($user);
        }
        return $this->formatResponseMsg(500007);
    }

    public function getProfile(Request $request){
        $data = User::find(Auth::id());
        event(new ShippingStatusUpdated(666));
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
            'verify' => 'required|between:6,6',
            'password' => 'required|between:6,32'
        ];
        $this->validate($request,$rules);
        $email = $request->input('email');
        if (Redis::get($email) == $request->input('verify')){
            $user = User::where('email','=',$email)->first();
            JWTAuth::invalidate(JWTAuth::fromUser($user));
            if ($user->update(['password' => bcrypt($request->input('pwd'))])){
                return $this->login($request);
            }
            return $this->formatResponseMsg(500006);
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
        try {
            $newToken = DB::transaction(function () use ($newPwd) {
                if (DB::table('users')->where('id', Auth::id())->update(['password' => $newPwd])) {
                    return JWTAuth::parseToken()->refresh();
                }
                return false;
            });
        } catch (TokenInvalidException $e) {
            return $this->formatResponseMsg(420002);
        } catch (TokenExpiredException $e){
            return $this->formatResponseMsg(420001);
        } catch (Exception $e){
            return $this->formatResponseMsg(500006);
        }
        if ($newToken)
            return $this->success($newToken);
        return $this->formatResponseMsg(500006);
    }

}
