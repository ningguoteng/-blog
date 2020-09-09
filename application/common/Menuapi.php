<?php
namespace app\common;
use think\Db;

class Menuapi
{


    public static function getAuthList($pid=0){
        $where = ['status'=>1];
        $where['pid'] = $pid;
        $list = Db::name("Auth_rule")
            ->where($where)
            ->order('sort desc,id asc')
            ->select();
        foreach ($list as $key=>$v){
            $children = self::getAuthList($v['id']);
            if($children){
                $list[$key]['children'] = $children;
            }
        }
        return $list;
    }
    public static function getAuthListAll($pid=0){
        static $authListAll = array();
        $where = ['status'=>1];
        $where['pid'] = $pid;
        $list = Db::name("Auth_rule")
            ->where($where)
            ->order('sort desc,id asc')
            ->select();
        if($list){
            $authListAll = array_merge($authListAll,$list);
            foreach ($list as $key=>$v){
                self::getAuthListAll($v['id']);
            }
        }
        return $authListAll;
    }



}