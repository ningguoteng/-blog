<?php

namespace app\admin\controller;
use think\App;
use think\Controller;
use think\Db;
use think\facade\Config;
use think\facade\Request;

class Admin extends Controller {
    public $admin_id = 0;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->admin_id = session("admin_id");
        if(empty($this->admin_id)){
            $this->redirect("login/index");
        }
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
