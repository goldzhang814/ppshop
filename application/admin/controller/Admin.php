<?php
namespace app\admin\controller;
use think\Db;
class Admin extends Common{
    public function add(){
        if ($this->request->isGet()){
            $role=db('role')->where('id','gt',1)->select();
            $this->assign('role',$role);
            return $this->fetch();
        }

        $adminModel=model('Admin');
        $flag=$adminModel->addUser();
        if(!$flag){
            $this->error($adminModel->getError());
        }
        $this->success('ok','index');
    }

    public function index(){
        $data=model('Admin')->getIndex();
        $this->assign('data',$data);
        return $this->fetch();


    }

    public function delete(){
        $id=input('id');

        $a=Db::name('admin')->where(['id'=>$id])->delete();
        $b=Db::name('adminRole')->where(['admin_id'=>$id])->delete();

        if ( $a && $b ){
           return $this->success('ok!','index');
        }
        return $this->error('删除失败!','index');

    }

    public function edit(){
        $admin_id=input('id');
        $model=Db::name('admin');
        if($admin_id<=1){
            $this->error('参数错误!');
        }

        if ($this->request->isGet()){
            $role=db('role')->where('id','gt',1)->select();
            $this->assign('role',$role);

            $info=$model->query("
        select a.*,b.role_id from shop_admin a left join shop_admin_role b on 
        a.id=b.admin_id where a.id=$admin_id");

        //执行原生sql返回2维数组
        $this->assign('info',$info[0]);
        return $this->fetch();
        }

        $data=input();
        if ($data['password']){
            $data['password']=md5($data['password']);

        }else{
            unset($data['password']);
        }
        $role_id=$data['role_id'];
        unset($data['role_id']);

        $map=['id'=>$data['id']];
        $model->where($map)->update($data);

        Db::name('admin_role')->where($map)->update(['role_id'=>$role_id]);

        return $this->success('ok!','index');
    }
}