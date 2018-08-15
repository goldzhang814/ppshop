<?php
namespace app\index\model;
use think\Model;
class Goods extends Model
{
    public function getHot()
    {
        $data=$this->where(['is_hot'=>1])->select();

        return $data;
    }
    public function getGoodsInfo(){
        $id=input('id');
        $data=$this->where(['id'=>$id])->find();

        return $data;
    }
}