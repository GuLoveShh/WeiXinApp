<?php
use \think\facade\Config;
use \think\facade\Request;
// 应用公共文件
function getOpenid($code){
    $appid = Config('app.wei_xin_app_info.appid');
    $secret = Config::get('app.wei_xin_app_info.secret');
    $weixin =  file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code");//通过code换取网页授权access_token
    $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
    $array = get_object_vars($jsondecode);//转换成数组
    return $array;
}

/**
 * @返回数据
 * @param $code
 * @param $msg
 * @param string $data
 * @param string $data1
 */
function re_json($code,$msg,$data = '',$data1 = ''){
    $arr = array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
        'data1' => $data1
    );
    common_log($code);
    return json($arr);
}

/**
 * @使用redis
 * @return redis
 */
function use_redis(){
    $redis = new \redis();
    $redis->connect('127.0.0.1', 6379, 30);
    return $redis;
}

/**
 * @统计日常日志
 */
function common_log($json_data='-'){
    $uid = session('uid');
    $request = $_POST == true ? (json_encode($_POST,320)) : ($_GET == true ? (json_encode($_GET,320)) : '-');
    $urls = $_SERVER['REQUEST_URI'];
    $url_data = explode("/",$urls);
    $controller = $url_data[count($url_data)-3]?:'-';
    $action = $url_data[count($url_data)-2]?:'-';
    $method = $url_data[count($url_data)-1]?:'-';
    $runtime = round((microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']),4);
    $ip = Request::ip();
    $apic = date("Y-m-d H:i:s",time()).'|'.$ip.'|'.$uid.'|'.$controller.'|'.$action.'|'.$method.'|'.$request.'|'.$json_data.'|'.$_SERVER['REQUEST_TIME_FLOAT'].'|'.microtime(true).'|'.$runtime."\n";
    $file_name = Config('app.log_path').'common/'.date('Y_m_d').'.log';
    file_put_contents($file_name,$apic,FILE_APPEND | LOCK_EX);
}