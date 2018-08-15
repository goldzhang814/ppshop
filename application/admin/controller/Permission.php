<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Permission extends Common
{
    public function add(){
        $model=model('Permission');
        if ($this->request->isGet()){
            $list=$model->add();
            $this->assign('list',$list);
            return $this->fetch();
        }
        $model->insert(input());

        return $this->success('ok','index');
    }

    public function index(){
        $da=Db::name('permission')->select();
        $data=$this->getAllTree($da);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function edit(){
        $id=input('id');
        $data=Db::name('permission')->find($id);
        if ($this->request->isGet()){
            //$data=Db::name('permission')->find($id);
            $this->assign('data',$data);
            //回写所有分类

            // dump($id);die();
            $dat=Db::name('permission')->select();
            $tree=$this->getAllTree($dat);
            $this->assign('list',$tree);
            return $this->fetch();
        }

        //需要验证所修改的新分类不是其原来的子分类
        //$data=Db::name('permission')->find($id);


        $flag=Db::name('permission')->update(input());
        if(!$flag){
            $this->error('修改失败','edit');
        }
        $this->success('保存成功!','index');
    }

    public function delete(){
        //$flag=Db::name('category')->delete(input('id'));
        $id=input('id',0);
        $model=model('Permission');
        $flag=$model->del($id);
        if($flag===false){
            $this->error($model->getError());

        }
        $this->success('删除成功','index');
    }
}
