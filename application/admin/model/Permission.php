<?php
namespace app\admin\model;
use think\Model;

class Permission extends Model{
    public function add(){
        $data=Permission::all();
        $common=new Common();
        $list=$common->getAllTree($data);
        return $list;
    }

    public  function del($id){

/*        if ($id<=0){
            $this->error='参数错误';
            return FALSE;
        }*/
        $info=self::get(['parent_id'=>$id]);
        if ($info){
            //给error设置属性error，在控制器中获取该属性
            $this->error='存在子分类，请谨慎删除！';
            return FALSE;
        }
        self::where(['id'=>$id])->delete();
    }
}