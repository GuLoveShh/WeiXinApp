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
            Db::table('user')->save($data);
        }catch (\Exception $e){}
        $_3rd_session = $this->_3rd_session();
        session($_3rd_session,serialize($userinfo));
        return $_3rd_session;
    }

    /**
     * @更新用户信息
     * @param $data
     * @param $_3rd_session
     */
    public function UserInfoUpdate($data,$_3rd_session){
        $openid = unserialize(session($_3rd_session))['openid'];
        Db::table('user')->where('openid',$openid)->save($data);
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