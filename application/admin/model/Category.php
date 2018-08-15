<?php
namespace app\admin\model;
use think\Model;

class  Category extends Model{
    public  function del($cate_id){

            if ($cate_id<=0){
                $this->error='参数错误';
                return FALSE;
            }
            $cate_info=self::get(['parent_id'=>$cate_id]);
            if ($cate_info){
                //给error设置属性error，在控制器中获取该属性
                $this->error='存在子分类，请谨慎删除！';
                return FALSE;
            }
            self::where(['id'=>$cate_id])->delete();
    }

    public function edit(){


    }

    public function getAllTree($data,$parent_id=0,$lv=0,$isClear=true){

        static $list=[];
        if ($isClear){
            $list=[];
        }
        foreach($data as $v ){
            if ($v['parent_id']==$parent_id){
                $v['lv']=$lv;
                $list[]=$v;
                $this->getAllTree($data,$v['id'],$lv+1,false);
            }
        }
        return $list;
    }

}