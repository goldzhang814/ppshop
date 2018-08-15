<?php
namespace app\index\model;
use think\Model;

class User extends Model
{

    public function addUser(){
        $user=input();
        if (User::get(['username'=>$user['username']])){
            $this->error="用户名重复!";
            return FALSE;
        }
        //对用户输入的密码进行二次加密

        $user['salt']=rand(100000,999999);
        $user['password']=md6($user['password'],$user['salt']);

        User::isUpdate(FALSE)->save($user);
    }

    public function login(){
        $user =User::get(['username'=>input('username')]);
        if (!$user){
            $this->error="用户名错误!";
            return FALSE;
        }
        $user=$user->toArray();
        if ($user['password']!=md6(input('password'),$user['salt'])){
            $this->error='密码错误!';
            return FALSE;
        }
        //七天免登录
        $time = input('remember')?3600*24*7:0;
        //cookie在保存重要数据的时候需要加密 ,防止抓包,并且要可解

        $string=encrypt(json_encode($user));
        //dump($string);die();
        cookie('_userinfo',$string,$time);
        return TRUE;

    }
}