<?php
namespace app\admin\Controller;
use think\Controller;
use think\captcha\Captcha;
class Login extends Controller{
    //生成MD5密码
    public function makeMd5(){
        return md5(input('key'));
    }
    //显示登录页面及验证
    public function index(){
        if(request()->isGet()){
            return $this -> fetch();
        }
        $model=model('Admin');
        $result=$model->login();
        if ($result===false){
                $this->error($model->getError());
        }
        $this->success('登录成功','admin/index/index');

    }
    public function makeCode(){
        $conf=['length'=>4];
        $obj=new Captcha($conf);
        ob_clean();
        return $obj->entry();

        //dump($obj->entry());
    }
}