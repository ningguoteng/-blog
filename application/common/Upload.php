<?php
namespace app\common;
class Upload
{
    public static function upload_file($file)
    {
        $infos = $file->getInfo();
        $son =  substr(strrchr($infos['name'], '.'), 1);
        $urls = './Uploads/img/';
        $url = '/Uploads/img/';
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>'jpg,png,gif,jpeg,js,css,mp4'])->move($urls,'');
        if($info){


            $name = $info->getSaveName();
            $name = explode('.', $name);
            $end = end($name);

            if($end != 'mp4'){
                //缩略图
                $name = './Uploads/img/thumb/'.$info->getSaveName();
                ResizeImage::resizeImage($urls.$info->getSaveName(),250,$name);
            }else{
                //截取第一帧
                $time = 1; 		//默认截取第一秒第一帧
                $size = '250*500';
                $str = "/home/wwwroot/nxy.ningguoteng.com/ffmpeg -i ".$urls.$info->getSaveName()." -y -f mjpeg -ss ".$time." -t 0.001 -s $size ".$urls.$info->getSaveName().'.jpg';
                exec($str);
            }

           

            // 成功上传后 获取上传信息
            // 输出 jpg
//            echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            return $url.$info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
//            echo $info->getFilename();
        }else{
            // 上传失败获取错误信息
            return $file->getError();
        }

    }

}