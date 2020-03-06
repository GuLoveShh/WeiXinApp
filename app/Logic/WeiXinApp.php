<?php
/**
 * Created by PhpStorm.
 * User: G_S
 * Date: 2020/3/6
 * Time: 11:33
 */
namespace app\Logic;
class WeiXinApp{
    public function Userlogin($code){
        $userinfo = getOpenid($code);
        $openid = $userinfo['openid'];
        $count = $this->user->where("openid = '$openid'")->count();
        if(!empty($openid)){
            if($count<1){
                $data['openid'] = $userinfo['openid'];
                $data['nickname'] = $userinfo['nickname'];
                $data['sex'] = $userinfo['sex']==1?0:1;
                $id = $this->user->add($data);
                $headimgurl = $userinfo['headimgurl'];
                $img = curl_file_get_contents($headimgurl);
                file_put_contents(C('PATH_UPLOAD')."user/".$id.".jpg", $img);
                $data1['imgurl'] = "user/".$id.".jpg";
                $this->user->where("id=$id")->save($data1);
            }else{
                $id = $this->user->where("openid = '$openid'")->getField('id');
            }
        }
        return $id;
    }
}