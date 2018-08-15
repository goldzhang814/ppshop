<?php
namespace app\admin\model;
use think\Model;

class Common extends Model{
    public function getAllTree($data,$parent_id=0,$lv=0){

        static $list=[];
        foreach($data as $v ){
            if ($v['parent_id']==$parent_id){
                $v['lv']=$lv;
                $list[]=$v;
                $this->getAllTree($data,$v['id'],$lv+1);
            }
        }
        return $list;
    }

}