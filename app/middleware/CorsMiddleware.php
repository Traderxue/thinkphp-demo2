<?php

namespace app\middleware;

class CorsMiddleware
{
    public function handle($request, \Closure $next)
    {
        // 设置允许跨域的域名，* 表示允许任何域名访问
        $origin = '*';

        // 设置允许的请求方法
        $methods = 'GET, POST, PUT, DELETE';

        // 设置允许的请求头字段
        $headers = 'Origin, X-Requested-With, Content-Type, Accept';

        // 设置响应头
        // header("Access-Control-Allow-Origin: $origin");
        // header("Access-Control-Allow-Methods: $methods");
        // header("Access-Control-Allow-Headers: $headers");

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");

        // 对预检请求进行处理
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Credentials: true");
            header('Access-Control-Max-Age: 86400'); // 预检请求的有效期，单位秒
            header("Content-Length: 0");
            header("Content-Type: text/plain");
            exit();
        }

        // 继续处理请求
        return $next($request);
    }
}
