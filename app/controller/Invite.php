<?php
 namespace app\controller;

 use think\Request;
 use app\BaseController;
 use app\model\Invite as InviteModel;

 class Invite extends BaseController{
    function result($code,$msg,$data){
        return json([
            "code"=>$code,
            "msg"=>$msg,
            "data"=>$data
        ]);
    }

    function getInviteCode($u_id){
        $code = InviteModel::where('u_id',$u_id)->find();
        return $this->result(200,"获取邀请码成功",$code);
    }

    function setCode(Request $request){
        $code = $request->post('code');
        $u_id = $request->post('u_id');

        $c = new InviteModel;

        $c->code = $code;
        $c->u_id = $u_id;

        $res = $c->save();
        if(!$res){
            return $this->result(400,"设置邀请码失败",null);
        }
        return $this->result(200,"设置邀请码成功",null);
    }

    function getPage(Request $request){
        $page =$request->param("page",1);
        $pageSize = $request->param("page_size",10);

        $list = InviteModel::paginate([
            'page'=>$page,
            'list_rows'=>$pageSize
        ]);
        return $this->result(200,"获取数据成功",$list);
    }
 }