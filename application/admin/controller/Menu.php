<?php

namespace app\admin\controller;
use app\common\Menuapi;
use think\App;
use think\Db;
use think\facade\Request;

class Menu  extends Admin {


    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    public function index() {

        $where = ['status'=>1];
        $where['pid'] = 0;
        $list = Db::name("Auth_rule")
            ->where($where)
            ->order('sort desc,id asc')
            ->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $this->assign('page', $page);
        if(isset($list)){
            $list = $list->toArray();
            foreach ($list['data'] as $key=>$v){
                $list['data'][$key]['children'] = Db::name("Auth_rule")
                    ->where('pid',$v['id'])
                    ->where('status',1)
                    ->select();
            }
        }
        $this->assign('list',$list['data']);
        return $this->fetch();
    }
    /*
     * 添加
     */
    public function add() {

        $isPost = Request::isPost();
        if($isPost){
            $param = Request::param();
            if(empty($param)){
                return $this->response(102,'添加失败');
            }
                //添加
            $id = DB::name('auth_rule')->insertGetId($param);

            if($id !==false){
                return $this->response(200,'添加成功');
            }else{
                return $this->response(101,'添加失败');
            }
        }else{
            $auth_rule = Db::name('auth_rule')
                ->where('status',1)
                ->where('pid',0)
                ->select();
            $this->assign('auth_rule',$auth_rule);
            return $this->fetch();
        }
    }
    /*
     * 详情
     */
    public function details(){
        $id = Request::param('id');
        if(empty($id)){
            return $this->response(102,'暂无数据');
        }
        $info = DB::name('auth_rule')->where('id',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    /*
     * 编辑页面
     */
    public function edit(){
        $id = Request::param('id');
        if(empty($id)){
            return $this->response(102,'暂无数据');
        }
        $info = DB::name('auth_rule')->where('id',$id)->find();
        $this->assign('info',$info);
        $auth_rule = Db::name('auth_rule')
            ->where('status',1)
            ->where('pid',0)
            ->select();
        $this->assign('auth_rule',$auth_rule);
        return $this->fetch();
    }
    public function isedit(){
        $param = Request::param();
        if(empty($param)){
            return $this->response(102,'编辑失败');
        }
        $id =  DB::name('auth_rule')
            ->where('id',$param['id'])
            ->update($param);
        if($id){
            return $this->response(200,'编辑成功');
        }else{
            return $this->response(101,'编辑失败');
        }

    }

    public function del(){
        $id = Request::param('id');
        $del = DB::name('auth_rule')->where('id',$id)->update(array('status'=>0));
        if($del){
            return $this->response(200,'删除成功');
        }else{
            return $this->response(101,'删除失败');
        }

    }
    /*
     * 角色列表
     */
    public  function role(){
        $list = DB::name('auth_grade')
            ->where('status',1)
            ->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 添加角色
     */
    public  function add_role(){
        $isPost = Request::isPost();
        if($isPost){
            $param = Request::param();

            if(empty($param)){
                return $this->response(102,'添加失败');
            }
            $param['add_time'] = time();
            $param['status'] = 1;
            //添加
            $id = DB::name('auth_grade')->insertGetId($param);

            if($id !==false){
                return $this->response(200,'添加成功');
            }else{
                return $this->response(101,'添加失败');
            }
        }else{
            $list = Menuapi::getAuthList();
            $this->assign('list',$list);
        }
        return $this->fetch();

    }
    /*
     * 角色权限
     */
    public  function edit_role(){
        $id = Request::param('id');
        if(empty($id)){
            return $this->response(102,'暂无数据');
        }
        $info = DB::name('auth_grade')->where('id',$id)->find();
        $this->assign('info',$info);
        $list = Menuapi::getAuthList();
        $this->assign('list',$list);
        return $this->fetch();

    }
    /*
     * 角色权限编辑
     */
    public  function is_edit_role(){
        $param = Request::param();
        if(empty($param)){
            return $this->response(102,'编辑失败');
        }
        $id =  DB::name('auth_grade')
            ->where('id',$param['id'])
            ->update($param);
        if($id){
            return $this->response(200,'编辑成功');
        }else{
            return $this->response(101,'编辑失败');
        }
    }
    /*
     * 角色删除
     */
    public function del_role(){
        $id = Request::param('id');
        $del = DB::name('auth_grade')
            ->where('id',$id)
            ->update(array('status'=>0));
        if($del){
            return $this->response(200,'删除成功');
        }else{
            return $this->response(101,'删除失败');
        }
    }
    public function admin(){
       $list =  DB::name('admin')
           ->where('status',1)
           ->select();
       foreach ($list as $k=>$val){
           $list[$k]['role_name'] = DB::name('auth_grade')
               ->where('id',$val['role_id'])
               ->value('grade_name');
       }
       unset($list[0]);
       $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 添加管理员
     */
    public function add_admin(){

        $isPost = Request::isPost();
        if($isPost){
            $param = Request::param();
            $admin = DB::name('admin')
                ->where('status',1)
                ->where('name',$param['name'])
                ->count();
            $param['password'] = md5($param['password']);
            if(empty($param['name'])){
                return $this->response(105,'请添加账号');
            }
            if($admin){
                return $this->response(104,'已有该账号');
            }
            if(empty($param['role_id'])){
                return $this->response(103,'请选择角色');
            }

            if(empty($param)){
                return $this->response(102,'添加失败');
            }
            $param['add_time'] = time();
            $param['status'] = 1;
            //添加
            $id = DB::name('admin')->insertGetId($param);

            if($id !==false){
                return $this->response(200,'添加成功');
            }else{
                return $this->response(101,'添加失败');
            }
        }else{
            $auth_grade = DB::name('auth_grade')
                ->where('status',1)
                ->select();
            $this->assign('auth_grade',$auth_grade);
        }
        return $this->fetch();

    }
    /*
     * 管理员数据
     */
    public function edit_admin(){
        $id = Request::param('id');
        if(empty($id)){
            return $this->response(102,'暂无数据');
        }
        $info = DB::name('admin')->where('id',$id)->find();
        $this->assign('info',$info);
        $auth_grade = DB::name('auth_grade')
            ->where('status',1)
            ->select();
        $this->assign('auth_grade',$auth_grade);
        return $this->fetch();
    }
    public function is_edit_admin(){
        $param = Request::param();
        if(empty($param)){
            return $this->response(102,'编辑失败');
        }

        $data = array();
        $data['name'] = $param['name'];
        $data['role_id'] = $param['role_id'];
        if($param['password']){
            $data['password'] = md5($data['password']);
        }
        $id =  DB::name('admin')
            ->where('id',$param['id'])
            ->update($data);
        if($id){
            return $this->response(200,'编辑成功');
        }else{
            return $this->response(101,'编辑失败');
        }
    }
    public function del_admin(){
        $id = Request::param('id');
        $del = DB::name('admin')->where('id',$id)->update(array('status'=>0));
        if($del){
            return $this->response(200,'删除成功');
        }else{
            return $this->response(101,'删除失败');
        }
    }
}
