<?php
// 应用公共文件
function getOpenid($code){
    $appid = Config::get('app.wei_xin_app_info.appid');
    $secret = Config::get('app.wei_xin_app_info.secret');
    $weixin =  file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code");//通过code换取网页授权access_token
    $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
    $array = get_object_vars($jsondecode);//转换成数组
    var_dump($array);exit;
    $openid = $array['openid'];//输出openid
    $access_token = $array['session_key'];
    $weixin1 =  file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid);//使用access_token获取用户信息
    $jsondecode1 = json_decode($weixin1); //对JSON格式的字符串进行编码
    $array1 = get_object_vars($jsondecode1);//转换成数组
    return $array1;
}