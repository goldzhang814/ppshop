<?php
namespace app\index\controller;
class User extends Common
{
    public function regist(){
        if (request()->isGet()){
            return $this->fetch();
        }
        $model=model('User');
        $flag=$model->addUser();
        if ($flag===FALSE){
            $this->error($model->getError());
        }
        return $this->success('注册完成,ok!','login');
    }

    public function login(){
        if (input('dest')){
            $dest=input('dest');
        }else{
            $dest='index/index';
        }

        if ($this->request->isGet()){
            return $this->fetch();
        }
        $model=model('User');
        $flag=$model->login();
        if ($flag===FALSE){
          return  $this->error($model->getError(),'login');
        }
        return $this->redirect('index/'.$dest);
    }

    public function loginOut(){
        cookie('_userinfo',null);
        return $this->redirect('index/index/index');
    }
}