<?php
/**
 * Created by PhpStorm.
 * User: G_S
 * Date: 2020/3/6
 * Time: 11:33
 */
namespace app\logic;
use think\facade\Db;
class WeiXinApp{
    public function Userlogin($code){
        $userinfo = getOpenid($code);
        $data['openid'] = $userinfo['openid'];
        try{
            $uid = Db::name('user')->insertGetId($data);
        }catch (\Exception $e){
            $uid = Db::table('user')->where('openid', $data['openid'])->value('id');
        }
        $result['_3rd_session'] = $_3rd_session = $this->_3rd_session();
        session($_3rd_session,serialize($userinfo));
        session('uid',$uid);
        //之前使用redis做存储
        //$redis = use_redis();
        //$redis->setex($_3rd_session,3600,serialize($userinfo));
        return $result;
    }

    /**
     * @更新用户信息
     * @param $data
     * @param $_3rd_session
     */
    public function UserInfoUpdate($data,$session_key){
        $openid = unserialize($session_key)['openid'];
        Db::table('user')->where('openid',$openid)->save($data);
    }

    /**
     * @更新用户地图经纬度
     * @param $data
     * @param $session_key
     */
    public function UserMapUpdate($data,$session_key){
        $redis = use_redis();
        $uid = session('uid');
        $latitude = 'latitude_'.$uid;
        $longitude = 'longitude_'.$uid;
        $time = 'create_time_'.$uid;
        $redis->hSet('map',$latitude,$data['latitude']);
        $redis->hSet('map',$longitude,$data['longitude']);
        $redis->hSet('map',$time,time());
        return true;
    }

    /**
     * @获取用户地图经纬度
     * @param $data
     * @param $session_key
     */
    public function UserMapGet($session_key){
        $redis = use_redis();
        $uid = session('uid');
        $latitude = 'latitude_'.$uid;
        $longitude = 'longitude_'.$uid;
        $data['latitude'] = $redis->hGet('map',$latitude);
        $data['longitude'] = $redis->hGet('map',$longitude);
        return $data;
    }


    /**
     * @生成_3rd_session随机数
     * @param int $len
     * @return bool|string
     */
    public function _3rd_session($len = 16)
    {
        $fp = @fopen('/dev/urandom', 'rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        } else {
            trigger_error('Can not open /dev/urandom.');
        }
        // convert from binary to string
        $result = base64_encode($result);
        // remove none url chars
        $result = strtr($result, '+/', '-_');
        return substr($result, 0, $len);
    }
}