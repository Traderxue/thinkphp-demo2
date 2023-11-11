<?php
    namespace app\middleware;

    class CorsMiddleware{
        public function handle($request, \Closure $next)
        {
            // 设置允许跨域的域名，* 表示允许任何域名访问
            $origin = '*';
    
            // 设置允许的请求方法
            $methods = 'GET, POST, PUT, DELETE';
    
            // 设置允许的请求头字段
            $headers = 'Origin, X-Requested-With, Content-Type, Accept';
    
            // 设置响应头
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Methods: $methods");
            header("Access-Control-Allow-Headers: $headers");
    
            // 继续处理请求
            return $next($request);
        }  
    }