<?php
namespace app\admin\model;

use think\Model;

class Attribute extends Model {
    public function editAttr()
    {
        $data=input();
        $flag=Attribute::update($data);
        if ($flag===FALSE){
            $this->error='更新数据失败';
            return FALSE;
        }
        return TRUE;
    }
    public function index(){
        $data=Attribute::alias('a')->field('a.*,b.type_name')
            ->join('type b','a.type_id=b.id','left')->select();

        return $data;
    }
    //根据type_id获取属性信息
    public function getTypeAttr($type_id){
        $list=[];
        $data=Attribute::all(['type_id'=>$type_id]);
        //格式化数据当录入方式为select选择时需要循环输出每一个选项
        foreach ($data as $key=>$v){
            $v=$v->toArray();
            if ($v['attr_input_type']==2){
                $v['attr_values']=explode(',',$v['attr_values']);
            }
            $list[]=$v;
        }
        return $list;
    }



}