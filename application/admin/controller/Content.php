<?php

namespace app\admin\controller;
use app\common\Upload;
use think\App;
use think\Db;
use think\facade\Config;
use think\facade\Request;

class Content  extends Admin {


    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    public function index() {

        $list = DB::name('content')
            ->where('status',1)
            ->order('id desc')
            ->paginate(5);
        $this->assign('list',$list);

        // 获取分页显示
        $page = $list->render();
        $this->assign('page', $page);
        $title = '宁忻阳博客';
        $this->assign('title',$title);
        return $this->fetch();

    }


    public function add() {

        $isPost = Request::isPost();
        if($isPost){
            $param = Request::param();
            if(empty($param)){
                 return $this->response(102,'添加失败');
            }


            if($param['id']){
                //编辑
                $id =  DB::name('content')->where('id',$param['id'])->update($param);
            }else{
                //添加
                $id = DB::name('content')->insertGetId($param);
            }

            if($id !==false){
                 return $this->response(200,'添加成功');
            }else{
                 return $this->response(101,'添加失败');
            }
        }else{

            $id = Request::param('id');
            $info = [];
            if($id){
                //编辑
                $info = DB::name('content')->where('id',$id)->find();

            }
            $this->assign('info',$info);
            $title = '宁忻阳博客';
            $this->assign('title',$title);
            return $this->fetch();
        }
    }
    public function del(){
        $id = Request::param('id');
        $del = DB::name('content')->where('id',$id)->update(array('status'=>2));
        if($del){
            return  $this->response(200,'删除成功');
        }else{
            return  $this->response(101,'删除失败');
        }

    }



    public function uploads(){
        $file = request()->file('file');
        if($file){
            $url = Upload::upload_file($file);
            return $url;
        }
    }

}
