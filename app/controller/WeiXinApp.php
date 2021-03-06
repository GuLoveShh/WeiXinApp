<?php
/**
 * 上传图片接口
 * Created by PhpStorm.
 * User: G_S
 * Date: 2020/3/5
 * Time: 16:47
 */
namespace app\controller;
use app\logic;
use app\Request;
use think\facade\Session;
class WeiXinApp {
    public function __construct()
    {
//        $redis = use_redis();
//        $this->session_key =  $redis->get($this->_3rd_session);
        $this->session_id = Session::getId();
        $this->_3rd_session = input('post._3rd_session');
        $this->session_value =  session($this->_3rd_session);
        $this->WeiXinApp = new logic\WeiXinApp();
    }

    public function index(){
        return re_json(1,'success');
    }

    /**
     * @判断用户是否关注和登录
     */
    public function isLoginAttent(){
        $code = input('post.code');
        if(!empty($code)){
            $data = $this->WeiXinApp->Userlogin($code);
            $data['session_id'] = $this->session_id;
            return re_json(1,'success',$data);
        }else{
            return re_json(101,'NOT_CODE');
        }
    }

    /**
     * @用户信息更新
     */
    public function userInfoUpdate(){
        $userinfo = input('post.userinfo');
        if(!empty($userinfo)){
            $this->WeiXinApp->UserInfoUpdate($userinfo,$this->session_value);
        }
        return re_json(1,'success');
    }

    /**
     * @用户经纬度更新
     */
    public function userMapUpdate(){
        $map_info = input('post.map_info');
        if(!empty($map_info)){
            $this->WeiXinApp->UserMapUpdate($map_info,$this->session_value);
        }
        return re_json(1,'success');
    }

    /**
     * @获取用户经纬度
     */
    public function userMapGet(){
        $data = $this->WeiXinApp->UserMapGet($this->session_value);
        return re_json(1,'success',$data);
    }

    public function imgupload(){
        $imgurl="https://love.huelong.com/live_record/img";
        // 允许上传的图片后缀
        $allowedExts = array("gif", "jpeg", "jpg", "png");
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);     // 获取文件后缀名
        if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) && ($_FILES["file"]["size"] < 20480000) && in_array($extension, $allowedExts)) {
            $imgpath=$_GET['imgpath'];  //获取传来的图片分类，用于在服务器上分类存放
            $code = $_FILES['file'];//获取小程序传来的图片
            $uploaded_file=$_FILES['file']['tmp_name'];
            $user_path='/home/admin/php/live_record/img/'.$imgpath;  //放到服务器下指定的文件夹
            if(!file_exists($user_path)){
                mkdir($user_path,0777);
            }
            $date=date('Ymd'); //得到当前时间
            $newfilename=$date.'.'.$extension; //得到一个新的文件名,可根据自己需求设定，sham用的时间加上图片文件大小来命名
            $move_to_file=$user_path."/".$newfilename;
            $file_true_name=$imgurl.$imgpath."/".$newfilename;
            //echo $file_true_name;
            $filename = json_encode($file_true_name);//把数据转换为JSON数据.
            // echo $move_to_file;
            move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file));
            //下面的代码是用来生成缩略图的
            $thump = $user_path."/thumb/";   //这个缩略图文件夹地址自己设置，这个是在原图文件夹里面增加1个子目录thumb用于存放缩略图
            if(file_exists($thump)){
            }else{
                mkdir($thump,0777);
            }
            $imgs = $newfilename;
            $imgss=$user_path."/".$imgs;
            $img_arr = getimagesize($imgss);
            $pecent = $img_arr[0]/$img_arr[1];
            $width = 200;    //这里是缩略图的尺寸，自行设置
            $height = 200/$pecent;
            //下面是根据不同图片后缀，执行不同的图片生成代码
            if($_FILES["file"]["type"] == "image/png"){

                $img_in = imagecreatefrompng($imgss);

            }elseif($_FILES["file"]["type"] == "image/jpg" || $_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/pjpeg"){

                $img_in = imagecreatefromjpeg($imgss);

            }elseif($_FILES["file"]["type"] == "image/gif"){

                $img_in = imagecreatefromgif($imgss);

            }



            $img_out = imagecreatetruecolor($width, $height);

            imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, $width, $height, $img_arr[0], $img_arr[1]);

            imagejpeg($img_out,$thump.$imgs,100);

            imagedestroy($img_out);

            imagedestroy($img_in);

            //这里最后输出缩略图的网址，让小程序读取到，用于放入input用来传到数据库中

            echo $imgurl.$imgpath."/thumb/".$newfilename;

        }else

        {

            echo "上传错误";

        }
    }
}