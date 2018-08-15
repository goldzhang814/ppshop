<?php
namespace app\admin\controller;
use think\Db;

class Type extends Common
{
    //type add
    public function add(){

        if ($this->request->isGet()){
            return $this->fetch();
        }
        Db::name('type')->insert(['type_name'=>input('type_name')]);
        $this->success('ok','index');
    }
    //type list
    public function index(){
        $data=Db::name('type')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
    //type delete
    public function del(){
        $id=input('id');
        $flag=Db::name('type')->where(['id'=>$id])->delete();
        if (!$flag){
            $this->error('删除失败','index');
            return FALSE;
        }
        $this->success('删除成功！','index');

    }
    //type edit
    public function edit(){
        if ($this->request->isGet()){
            $data=Db::name('type')->where(['id'=>input('id')])->find();
            $this->assign('data',$data);
            return $this->fetch();

        }
    }


}


