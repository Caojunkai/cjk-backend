<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth',[
            'except'=>[
                'register',
                'login',
                'logout',
                'passwordReset',
                'index'
            ]
        ]);
    }

    public function index(){
        echo 23333;
    }

    public function register(Request $request){
        $rules = [
            'username' => 'required|between:4,32|alpha_dash|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|between:6,32',
        ];
        $this->validate($request,$rules);
        $params = $request->only('username','email','password');
        $params['password'] = bcrypt($params['password']);
        $user = new User($params);
        var_dump($user->save());
    }
}
