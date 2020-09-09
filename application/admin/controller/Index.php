<?php

namespace app\admin\controller;

use app\common\Menuapi;
use think\App;
use think\Db;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;

class Index extends Admin {

    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    public function index() {

        $session = Session::get();
        $rule_ids = DB::name('auth_grade')
            ->where('id',$session['admin_info']['role_id'])
            ->value('rule_ids');
        $list = Db::name("Auth_rule")
            ->where('id','in',$rule_ids)
            ->where('pid',0)
            ->where('status',1)
            ->order('sort desc,id asc')
            ->select();
        if(isset($list)){
            foreach ($list as $key=>$v){
                $list[$key]['children'] = Db::name("Auth_rule")
                    ->where('id','in',$rule_ids)
                    ->where('pid',$v['id'])
                    ->where('pid','>',0)
                    ->where('status',1)
                    ->select();
            }
        }
        $this->assign('admin',$session['admin_info']);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function index_v1() {
        return $this->fetch();
    }



}
