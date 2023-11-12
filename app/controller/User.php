<?php

namespace app\controller;

use Firebase\JWT\JWT;
use think\Request;
use app\BaseController;
use app\model\User as UserModel;

class User extends BaseController
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

        $u = UserModel::where('username',$username)->find();

        if($u){
            return $this->result(400,"用户已存在",null);
        }

        $user = new UserModel;

        $user->username = $username;
        $user->password = $password;

        $res = $user->save();

        if (!$res) {
            return $this->result(400, "注册成功", null);
        }
        return $this->result(200, "注册成功", null);
    }

    function login(Request $request)
    {
        $username = $request->post("username");
        $password = $request->post("password");

        $user = UserModel::where("username", $username)->find();

        if (!$user) {
            return $this->result(400, "用户不存在", null);
        }

        if (password_verify($password, $user->password)) {
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
        return $this->result(400, "登录失败", null);
    }

    function resetPwd(Request $request)
    {
        $username = $request->post("username");
        $old_password = $request->post("old_password");
        $new_password = $request->post("new_password");

        $user = UserModel::where("username", $username)->find();
        if (!$user) {
            return $this->result(400, "用户不存在", null);
        }

        if (!password_verify($old_password, $user->password)) {
            return $this->result(400, "旧密码错误", null);
        }

        $user->password = $new_password;

        $res = $user->save();

        if (!$res) {
            return $this->result(400, "修改密码失败", null);
        }
        return $this->result(200, "修改密码成功", null);
    }

    function freeze(Request $request)
    {
        $username = $request->post("username");

        $user = UserModel::where("username", $username)->find();

        $user->freeze = '1';

        $res = $user->save();

        if (!$res) {
            return $this->result(400, "冻结用户失败", null);
        }
        return $this->result(200, "冻结用户成功", null);
    }

    function setBalance(Request $request)
    {
        $username = $request->post("username");
        $balance = $request->post("balance");

        $user = UserModel::where("username", $username)->find();

        $user->balance = $balance;

        $res = $user->save();

        if (!$res) {
            return $this->result(400, "设置密码失败", null);
        }
        return $this->result(200, "设置余额成功", null);

    }

    function getById($id)
    {
        $user = UserModel::where('id', $id)->field('id,username,freeze,balance,credit')->find();

        return $this->result(200, "获取数据成功", $user);
    }

    function getPage(Request $request)
    {   
        $page = $request->param("page",1);
        $pageSize = $request->param("page_size",10);

        $list = UserModel::field('id,username,freeze,balance,credit')->paginate([
            'page'=>$page,
            'list_rows'=>$pageSize
        ]);

        return $this->result(200,"获取数据成功",$list);
    }


    function deleteById($id){
        $res = UserModel::where('id',$id)->delete();

        if(!$res){
            return $this->result(400,"删除失败",null);
        }
            return $this->result(200,"删除成功",null);
    }

    function edit(Request $request){
        $username = $request->post("username");
        $freeze = $request->post("freeze");
        $balance = $request->post("balance");
        $credit = $request->post("credit");

        $user = UserModel::where('username',$username)->find();

        $user->freeze = $freeze;
        $user->balance = $balance;
        $user->credit = $credit;

        $res = $user->save();

        if(!$res){
            return $this->result(400,"编辑失败",null);
        }
        return $this->result(200,"编辑用户成功",null);
    }

    function transfer(Request $request){
        $u_id_from = $request->post("u_id_from");

        $u_id_to = $request->post("u_id_to");

        $amount = $request->post("amount");


        $user_from = UserModel::where("id",$u_id_from)->find();

        if($amount >=$user_from->balance){
            return $this->result(400,"余额不足",null);
        }

        $user_to = UserModel::where("id",$u_id_to)->find();

        UserModel::startTrans();

        try{
            $user_from->balance = ($user_from->balance)*1-$amount;

            $user_to->balance = ($user_from->balance)*1+$amount;

            $user_from->save();

            $user_to->save();

            UserModel::commit();    

            return $this->result(200,"转账成功",null);
        }catch(\Exception $e){
            UserModel::rollback();

            return $this->result(400,"转账失败",null);
        }
    }


}