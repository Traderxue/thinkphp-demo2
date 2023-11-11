<?php
namespace app\controller;

use app\BaseController;
use Firebase\JWT\JWT;
use think\Request;
use app\model\Admin as AdminModel;

class Admin extends BaseController
{
    function result($code, $msg, $data)
    {
        return json([
            "code" => $code,
            "msg" => $msg,
            "data" => $data
        ]);
    }

    function register(Request $request)
    {
        $username = $request->post("username");
        $password = password_hash($request->post("password"), PASSWORD_DEFAULT);

        $a = AdminModel::where("username", $username)->find();

        if ($a) {
            return $this->result(400, "注册失败,用户已存在", null);
        }

        $admin = new AdminModel;

        $admin->username = $username;
        $admin->password = $password;

        $res = $admin->save();
        if ($res) {
            return $this->result(200, "注册成功", null);
        } else {
            return $this->result(400, "注册失败", null);
        }
    }

    function login(Request $request)
    {
        $username = $request->post("username");
        $password = $request->post("password");

        $admin = AdminModel::where("username", $username)->find();

        if (!$admin) {
            return $this->result(400, "登录失败,用户不存在", null);
        }
        if (password_verify($password, $admin->password)) {
            $secretKey = '123456789'; // 用于签名令牌的密钥，请更改为安全的密钥

            $payload = array(
                // "iss" => "http://127.0.0.1:8000",  // JWT的签发者
                // "aud" => "http://127.0.0.1:9528/",  // JWT的接收者可以省略
                "iat" => time(),
                // token 的创建时间
                "nbf" => time(),
                // token 的生效时间
                "exp" => time() + 3600 * 10 * 10 * 10,
                // token 的过期时间
                "data" => [
                    // 包含的用户信息等数据
                    "username" => $username,
                ]
            );
            // 使用密钥进行签名
            $token = JWT::encode($payload, $secretKey, 'HS256');

            return $this->result(200, "登录成功", $token);
        }
        return $this->result(400,"登录失败,密码错误",null);
    }

    function resetPwd(Request $request)
    {
        $username = $request->post("username");
        $old_password = $request->post("old_password");
        $new_password = $request->post("new_password");

        $admin = AdminModel::where("username", $username)->find();

        if (!$admin) {
            return $this->result(400, "修改密码失败", null);
        }

        if (!password_verify($old_password, $admin->password)) {
            return $this->result(400, "旧密码错误", null);
        }
        $admin->password = password_hash($new_password, PASSWORD_DEFAULT);
        $res = $admin->save();

        if (!$res) {
            return $this->result(400, "修改密码失败", null);
        }
        return $this->result(200, "修改密码成功", null);
    }

    function deleteById($id){
        $res = AdminModel::where('id',$id)->delete();

        if(!$res){
            return $this->result(400,"删除用户失败",null);
        }
        return $this->result(200,"删除用户成功",null);
    }
}
