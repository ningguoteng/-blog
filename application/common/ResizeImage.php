<?php
/**
 * User: ngt<www.ninguoteng.com>
 * Date: 2019-03-22  上午01:02:42
 * @param 等比例压缩图片
 * @param $im        原图片路径加名字
 * @param $maxwidth  设置图片的最大宽度
 * @param $maxheight 设置图片的最大高度
 * @param $name      要保存的图片的路径加名称
 * @param $filetype  图片类型
 * @return
 *
 */
namespace app\common;

class ResizeImage{

    public static function resizeImage($im,$maxwidth,$name,$maxheight='',$filetype=''){

        //校验图片是否旋转
//        $exif = \exif_read_data($im, 'IFD0');
//        if(!empty($exif['Orientation'])){
//            $imgage = getimagesize($im); //得到原始大图片
//            switch ($imgage[2]) { // 图像类型判断
//                case 1:
//                    $im = imagecreatefromgif($im);
//                    break;
//                case 2:
//                    $im = imagecreatefromjpeg($im);
//                    break;
//                case 3:
//                    $im = imagecreatefrompng($im);
//                    break;
//            }
//            switch ($exif['Orientation']){
//                case 8:
//                    $im = imagerotate($im,90,0);
//                    break;
//                case 3:
//                    $im = imagerotate($im,180,0);
//                    break;
//                case 6:
//                    $im = imagerotate($im,-90,0);
//                    break;
//
//            }
//        }


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
        $pic_width = imagesx($im);
        $pic_height = imagesy($im);
        if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight))
        {
            if($maxwidth && $pic_width>$maxwidth)
            {
                $widthratio = $maxwidth/$pic_width;
                $resizewidth_tag = true;
            }

            if($maxheight && $pic_height>$maxheight)
            {
                $heightratio = $maxheight/$pic_height;
                $resizeheight_tag = true;
            }

            if($resizewidth_tag && $resizeheight_tag)
            {
                if($widthratio<$heightratio)
                    $ratio = $widthratio;
                else
                    $ratio = $heightratio;
            }

            if($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;

            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;

            if(function_exists("imagecopyresampled"))
            {
                $newim = imagecreatetruecolor($newwidth,$newheight);//PHP系统函数
                imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);//PHP系统函数
            }
            else
            {
                $newim = imagecreate($newwidth,$newheight);
                imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
            }

            imagejpeg($newim,$name);
            imagedestroy($newim);
        }
        else
        {
            imagejpeg($im,$name);
        }

    }

    public static function getThumbUrl($originalUrl)
    {
        if(empty($originalUrl)){
            return '';
        }

        $sign='thumb';
        $tmp=explode('/',$originalUrl);
        $arr=array();
        foreach($tmp as $k=>$v){
            if($k>2){
                if($sign){
                    $arr[]=$sign;
                }
            }
            $arr[]=$v;
        }
        //要判断文件是否存在
        $thumbUrl=implode('/',$arr);
        //统一APP和后台上传图片url格式
        $suffix=substr($thumbUrl,0,1);
        if($suffix=='/'){
            $thumbUrl='.'.$thumbUrl;
        }

        if(file_exists($thumbUrl)){
            $thumbUrl = str_replace("./Uploads", "/Uploads", $thumbUrl);
            return $thumbUrl;
        }else{
            $originalUrl = str_replace("./Uploads", "/Uploads", $originalUrl);
            return $originalUrl;
        }

    }


    /**
     * 处理图片
     * @date:
     * @author: ngt <www.ningguoteng.com>
     * @param: $news_imgs 图片id 逗号分隔
     * @return:
     */
    public static  function getImageUrl($news_imgs,$show=0){
        $thumb = array();
        if (!empty($news_imgs)) {
            //判断第一个是否是",",鬼知道为什么前面加了一个","
            $tmp=explode(',',$news_imgs);
            foreach($tmp as $tv=>$tt){
                if($tt==''){
                    unset($tmp[$tv]);
                }
                $thumb[] = array(
                    'thumb_url' => self::getThumbUrl($tt)
                );

            }
        }

        return $thumb;
    }


    /*
     *
     * 水印处理
     *
     * */

    public static function imageSy($image_url){

        $warter = M("watermark")->where(array('id'=>1))->find();
        if($warter['watermark_switch'] ==1){
            //开启
            if($warter['watermark_type'] ==1){
                //图片
                if($warter['watermark_img'] && $warter['watermark_location_id']){
                    $image = new \Think\Image();
                    $image->open($image_url)->water('.'.$warter['watermark_img'],$warter['watermark_location_id'])->save($image_url);

                }
            }else{
                //文字
                if($warter['watermark_word'] && $warter['watermark_location_id'] && $warter['watermark_word_color'] && $warter['watermark_word_size_id'] ){
                    $size = M("watermark_word_size")->where(array('watermark_word_size_id'=>$warter['watermark_word_size_id']))->getField("watermark_word_size");
                    $size = str_replace('px','',$size);

                    $image = new \Think\Image();
                    // 在图片右下角添加水印文字 ThinkPHP 并保存为new.jpg
                    $image->open($image_url)->text($warter['watermark_word'],'./Uploads/1.ttf',$size,$warter['watermark_word_color'],$warter['watermark_location_id'],'',$warter['angle'])->save($image_url);
                }

            }
        }
    }



}