<?php
namespace app\admin\model;
use think\captcha\Captcha;
use think\Db;
use think\Model;
class Admin extends Model{
//模型类的名字与表名一致，
    public function login(){
/*        $obj=new Captcha();
        if (!$obj->check(input('captcha'))){
            $this->error='验证码错误';
            return FALSE;
        }*/
        $username=input('username');
        $password=md5(input('password'));
        $userInfo=Admin::get(['username'=>$username,'password'=>$password]);
        if (!$userInfo){
            $this->error='登陆错误';
            return FALSE;
        }

        session('_user',$userInfo->getAttr('id'));
        //$this->redirect('admin/index/index');
    }

    public function addUser(){
        $data=input();
        //dump($data);die();
        $rule=[
            'username'=>'unique:admin',
        ];
        $obj=new \think\Validate($rule);
        if(!$obj->check($data)){
            $this->error=$obj->getError();
            return FALSE;
        }
        $data['password']=md5($data['password']);
        Admin::startTrans();
        try{
            Admin::allowField(true)->save($data);

            $admin_id=$this->getLastInsId();

            Db::name('admin_role')->insert(['admin_id'=>$admin_id,'role_id'=>$data['role_id']]);



            Admin::commit();

        }catch (\Exception $e){
            Admin::rollback();

        }
        return TRUE;
    }

    public function getIndex(){

        $sql="SELECT
                    a.id,
                    a.username,
                    d.role_name
              FROM
                    shop_admin a
              LEFT JOIN (
                SELECT
                      b.admin_id,
                      c.role_name
                FROM
                      shop_admin_role b
                LEFT JOIN shop_role c ON b.role_id = c.id
              ) d ON a.id = d.admin_id ";

        $data= $this->query($sql);
        return $data;
    }
}