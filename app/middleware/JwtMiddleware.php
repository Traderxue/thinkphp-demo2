<?php
namespace app\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\Request;
use think\Response;

class JwtMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $token = $request->header('token'); // 从请求头获取令牌

        if (!$token) {
            return Response::create(['message' => '未提供token'], 'json', 401);
        }
        try {
            $key = new Key('123456789', 'HS256');
            $decoded = JWT::decode($token, $key);

            // 将解码后的令牌数据放入请求中以供后续控制器使用
            $username = $decoded->data->username;

            return $next($request);
        } catch (\Exception $e) {
            return Response::create(['message' => '令牌无效或过期'], 'json', 401);
        }
    }
}
