<?php
use \think\facade\Config;
use \think\Request;
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
    $urls = $_SERVER['PHP_SELF'];
    $url_data = explode("/",$urls);
    $controller = $url_data[count($url_data)-3]?:'-';
    $action = $url_data[count($url_data)-2]?:'-';
    $method = $url_data[count($url_data)-1]?:'-';
    $runtime = round((microtime(true)-$GLOBALS['_beginTime']),4);
    $request = Request::instance();
    $ip = $request->ip();
    $apic = date("Y-m-d H:i:s",time()).'|'.$ip.'|'.$uid.'|'.$controller.'|'.$action.'|'.$method.'|'.$request.'|'.$json_data.'|'.$GLOBALS['_beginTime'].'|'.microtime(true).'|'.$runtime."\n";
    $file_name = Config('app.log_path').'common/'.date('Y_m_d').'.log';
    file_put_contents($file_name,$apic,FILE_APPEND | LOCK_EX);
}