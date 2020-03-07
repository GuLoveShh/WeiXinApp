<?php
use \think\facade\Config;
// 应用公共文件
function getOpenid($code){
    $appid = Config('app.wei_xin_app_info.appid');
    $secret = Config::get('app.wei_xin_app_info.secret');
    $weixin =  file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code");//通过code换取网页授权access_token
    $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
    $array = get_object_vars($jsondecode);//转换成数组
    return $array;
}

function json($code,$msg,$data = '',$data1 = ''){
    header("Content-type:text/html;charset=utf-8");
    $arr = array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
        'data1' => $data1
    );
    $json_data = urldecode(json_encode($arr,JSON_UNESCAPED_SLASHES));
    echo $json_data;
    exit;
}