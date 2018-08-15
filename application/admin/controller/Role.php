<?php
namespace app\admin\controller;
use think\Db;
class Role extends Common{

    public function add(){
        if ($this->request->isGet()){

            return $this->fetch();
        }
        Db::name('role')->insert(['role_name'=>input('role_name')]);
        return $this->success('ok','index');
    }
    public function index(){
        $data=Db::name('role')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function delete(){
        $flag=Db::name('role')->where(['id'=>input('id')])->delete();
        if (!$flag){
            $this->error('删除失败,请重试!','index');
        }
        return $this->success('ok!');
    }
    public function edit(){
        $id=input('id');
        if($this->request->isGet()){
            $data=Db::name('role')->find(['id'=>$id]);
            $this->assign('data',$data);
            return $this->fetch();
        }
        $data=input('role_name');
        $flag=Db::name('role')->where(['id'=>$id])->update(['role_name'=>$data]);
        if (!$flag){
            $this->error('修改失败,请重试!');
        }
        return $this->success('ok!','index');
    }

    public function disfetch(){
        $role_id=input('id');
        if ($this->request->isGet()){
            $menu=[];
            //获取当前角色已经具备的权限
            $hasRule=\db('role_permission')->field('permission_id')->where(['role_id'=>$role_id])->select();
            foreach ($hasRule as $value){
                $menu[]=$value['permission_id'];
            }
            $menus=implode(',',$menu);
            //dump($menus);die();
            $this->assign('menus',$menus);

            //获取所有的权限列表
            $rules=model('Role')->getRules();

            $this->assign('rules',$rules);
            return $this->fetch();
        }

        $rules_list=input('rule/a');
        //dump($rules_list);die();
        $list=[];
        foreach($rules_list as $key => $value){
            $list[]=['role_id'=>$role_id,'permission_id'=>$value];
        }
        //dump($list);die();
        if ($list){
            db('role_permission')->insertAll($list);
        }
       //dump(db('role_permission')->getLastSql());die;
        return $this->success('ok','index');

    }
}