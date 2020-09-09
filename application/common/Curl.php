<?php

// +----------------------------------------------------------------------
// | Laravel - A PHP Framework For Web Artisans
// +----------------------------------------------------------------------
// | Author:ngt <www.ningguoteng.com>
// +----------------------------------------------------------------------
// | Date: 2019/4/17 10:37 AM
// +----------------------------------------------------------------------


namespace app\common;
class Curl{

        public static function getInfo($http_url,$name,$type,$where,$limit='',$column=''){
            $request_arr = [
                'method'=>'pmd.shop.admin.getinfo',
                'name'=>$name,
                'type'=>$type,
                'where'=>$where,
                'limit'=>$limit,
                'column'=>$column
            ];
            $url = $http_url.'/rest.php/rest';
            return self::postCurl($url,$request_arr);
        }

        public static function postCurl($url='',$param){
            $header = [];
            $curl=curl_init();// 启动一个CURL会话
            curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
            curl_setopt ( $curl, CURLOPT_POST, 1);
            curl_setopt ( $curl, CURLOPT_HEADER, 0 );
            curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );//成功返回数据
            curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header); // 设置HTTP头
            curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false);//是否检测服务器的证书是否由正规浏览器认证过的授权CA颁发的
            curl_setopt ( $curl, CURLOPT_POSTFIELDS, $param ); // Post提交的数据包
            $result = curl_exec ( $curl ); // 执行操作
            if(curl_errno($curl)){//出错则显示错误信息
                print curl_error($curl);
            }

            curl_close ( $curl ); // 关闭CURL会话
            $response = json_decode($result,true);
            return $response;
        }


    public static function getCurl( $url, $cookie='', $type=true) {//type与url为必传、若无cookie则传空字符串

        if (empty($url)) {
            return false;
        }

        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        if($type){  //判断请求协议http或https
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        if(!empty($cookie))curl_setopt($ch,CURLOPT_COOKIE,$cookie);  //设置cookie
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }
}