<?php
namespace app\index\controller;

use app\common\ResizeImage;
use think\Controller;
use think\Db;
use think\facade\Request;

class Index extends Controller
{
    public function index()
    {

        $list = DB::name('content')
            ->where('status',1)
            ->order('time asc')
            ->select();


        foreach ($list as $key=>$v){

            $end = end(explode('.',$v['img']));
            $list[$key]['end'] = $end;

            if($end != 'mp4'){
                $list[$key]['thumb'] = ResizeImage::getThumbUrl($v['img']);
                $im = '.'.$v['img'];
                $imgage = getimagesize($im); //得到原始大图片
                switch ($imgage[2]) { // 图像类型判断
                    case 1:
                        $im = imagecreatefromgif($im);
                        break;
                    case 2:
                        $im = imagecreatefromjpeg($im);
                        break;
                    case 3:
                        $im = imagecreatefrompng($im);
                        break;
                }
                $width = imagesx($im);
                $height = imagesy($im);

                $list[$key]['size'] = $width.'x'.$height;
            }



            $list[$key]['day'] =  ceil((strtotime($v['time'])-strtotime('2020-02-28 09:22'))/86400);

        }
        $this->assign('list',$list);

        return $this->fetch();
    }


}
