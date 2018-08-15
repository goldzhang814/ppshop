<?php
namespace app\admin\model;
use think\Model;
class GoodsAttr extends Model{
    public  function insertData($goods_id,$attr)
    {
        $list=[];

        foreach ($attr as $key =>$val){
            //去点数据重复的属性值
            $val=array_unique($val);
            foreach ($val as $v){
                $list[]=[
                    'goods_id'=>$goods_id,
                    'attr_id'=>$key,
                    'attr_value'=>$v
                ];
            }
        }
        if (!$list){
           return FALSE;
        }
        $this->allowField(true)->insertAll($list);
        return TRUE;

    }

    public function getGoodsAttr($goods_id){
        //$goodsAttr=GoodsAttr::where(['goods_id'=>$goods_id])->select();
        $data=[];
        $datas=[];
        $sql="select a.attr_value,b.* from shop_goods_attr a left join shop_attribute b on a.attr_id=b.id where a.goods_id=$goods_id";

        $list=$this->query($sql);

        foreach ($list as $key => $value ){
            if($value['attr_input_type']==2){
                $value['attr_values']=explode(',',$value['attr_values']);
            }
            $data[]=$value;
        }
        foreach ($data  as $v){
            $datas[$v['id']][]=$v;
        }
        return $datas;
    }
}
