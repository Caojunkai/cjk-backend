<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function formatValidationErrors(Validator $validator)
    {
        $result = [
            'code'    => -1,
            'msg' => 'Validation Failed',
            'errors'  => [],
        ];
        $errors = $validator->errors()->messages();
        foreach ($errors as $field => $error) {
            foreach ($error as $key => $value) {
                array_push($result['errors'], [
                    'field'   => $field,
                    'message' => $value
                ]);
            }
        }
        return $result;
    }

    public function failure($errors,$status = 520){
        $result = [
            'code' => -1
        ];
        if ($errors){
            $result['error'] = $errors;
        }
        return response($result,$status);
    }


    public function formatResponseMsg($code = 666666){
        $msg = trans('code.'.$code);
        if (!is_array($msg)){
            return response(['code'=> -1,'msg'=>'未知错误'],520);
        }
        $result = [
            'code' => -1,
            'msg'  => $msg['msg']
        ];
        return response($result,$msg['status']);
    }

    public function success($data = ''){
        $result = [
            'code' => 0,
        ];
        if ($data)
            $result['msg'] = $data;
        return response($result);
    }

    public function refreshToken(Request $request){
        try {
            $newToken = JWTAuth::parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            return $this->formatResponseMsg(410001);
        } catch (JWTException $e) {
            return $this->formatResponseMsg(410002);
        }
        return $newToken;
    }

}
