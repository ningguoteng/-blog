<?php

namespace app\admin\controller;
use think\App;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\facade\Session;

class Login  extends Controller {


    public function index() {
        header('Access-Control-Allow-Origin:*');
        $request = Request::post();
        if(!empty($request)){
            $user_info = DB::name('admin')
                ->where(array('name'=>$request['name'],'password'=>md5($request['password'])))
                ->find();
            if($user_info){
                session('admin_id',$user_info['id']);
                session('admin_info',$user_info);
                return $this->response(200,'登录成功');
            }else{
                return $this->response(101,'用户名密码错误');
            }

        }else{
            return $this->fetch();
        }
    }
    public function logout(){
        if(isset($_SESSION['admin_id'])){
            unset($_SESSION['user']);
            unset($_SESSION['admin_info']);
        }
        $this->redirect("login/index");
    }
    public function response($errorcode,$error,$data=array()){

        if($errorcode!=200){
            return array('errorcode'=>$errorcode,'error'=>$error);
            //exit(json_encode(array('errorcode'=>$errorcode,'error'=>$error)));
        }else{
            return array('errorcode'=>$errorcode,'error'=>$error,'data'=>$data);
            //exit(json_encode(array('errorcode'=>$errorcode,'error'=>$error,'data'=>$data)));
        }
        exit;
    }
}
