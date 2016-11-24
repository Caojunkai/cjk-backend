<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class JWTAuthenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$token = $this->auth->setRequest($request)->getToken()){
            return $this->respond('tymon.jwt.absent',[
                'errcode' => '400041',
                'errmsg' => 'hello world'
            ], 400);
//            return $this->response->json([
//                'errcode' => '400041',
//                'errmsg' => 'hello world'
//            ],400);
        }
        return $next($request);
    }
}
