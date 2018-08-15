<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Category extends Common {

      public function delete(){
          //$flag=Db::name('category')->delete(input('id'));
          $cate_id=input('id',0);
          $model=model('Category');
          $flag=$model->del($cate_id);
          if($flag===false){
              $this->error($model->getError());

          }
          $this->success('删除成功','index');
      }
      public function edit(){
          $data=Db::name('category')->find(input('id'));
          $this->assign('data',$data);
          //回写所有分类
          $id=input('param.id');
         // dump($id);die();
          $dat=Db::name('category')->select();
          $tree=$this->getAllCate($dat,$id);
          $this->assign('tree',$tree);
          return $this->fetch();
      }

      public function saveEdit(){


          //需要验证所修改的新分类不是其原来的子分类
          $data=Db::name('category')->find(input('id'));

          $flag=Db::name('category')->update($data);
          if(!$flag){
              $this->error('修改失败','edit');
          }
          $this->success('成功','index');
      }
      public function add(){
          if($this->request->isGet()){
              $data=Db::name('category')->select();
              $tree=$this->getAllCate($data);
              $this->assign('tree',$tree);
              return $this->fetch();
          }

          $flag=Db::name('Category')->insert(input());
          if ($flag===false){
              $this->error('写入失败');
          }
          $this->success('写入成功');
      }
      public function index(){
          $data=Db::name('category')->select();

          $tree=$this->getAllCate($data);

          $this->assign('tree',$tree);
          return $this->fetch();

      }

}