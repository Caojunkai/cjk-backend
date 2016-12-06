<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

    public function pagination($data = ''){
        $result = [
            'code' => 0,
            'pagination' => null,
            'data' => null
        ];
        if ($data){
            $data = $data->toArray();
            $result['data'] = $data['data'];
            unset($data['data']);
            unset($data['prev_page_url']);
            unset($data['next_page_url']);
            $result['pagination'] = $data;
        }
        return response($result);
    }

}
