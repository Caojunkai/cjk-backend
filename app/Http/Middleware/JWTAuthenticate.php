<?php

namespace App\Http\Middleware;

use App\Events\LogEvent;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
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
            return $this->respond(new LogEvent($request),400002);
        }
        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return $this->respond(new LogEvent($request),500003);
        } catch (JWTException $e) {
            return $this->respond(new LogEvent($request), 500004);
        }
        if (!$user)
            return $this->respond(new LogEvent($request),400001);
        $this->events->fire('tymon.jwt.valid', $user);
        return $next($request);
    }


    public function respond($event, $error, $status = 0, $payload = [])
    {

        $response = $this->events->fire($event, $payload, true);
        $msg = trans('code.'.$error);
        if (!is_array($msg))
            $msg = trans('code.666666');
        $result = [
            'code' => -1,
            'msg'  => trans('code.'.$error)['msg']
        ];
        return $response ?: $this->response->json($result, $msg['status']);
    }
}
