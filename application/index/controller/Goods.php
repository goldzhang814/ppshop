<?php
namespace app\index\controller;
use think\Db;

class Goods extends Common
{
    public function index()
    {
        $id=input('id');
        $goodsModel=model('Goods');
        $data=$goodsModel->getGoodsInfo();
        $this->assign('data',$data);

        //dump($data->toArray());
        //获取商品属性

        $sql="SELECT a.*,b.attr_name,b.attr_type FROM shop_goods_attr  a LEFT JOIN shop_attribute  b on a.attr_id=b.id where a.goods_id=$id";
        $attr=Db::query($sql);
        //dump($attr);

        //区分唯一属性和单选属性
        $unique=$single=[];
        foreach($attr as $k=>$v){
            if ($v['attr_type']==1){
                $unique[]=$v;
            }else{
                $single[$v['attr_id']][]=$v;
            }
        }

        //dump($unique);
        //dump($single);

        $this->assign('single',$single);
        $this->assign('unique',$unique);

        //获取商品的图片
        $imgs=db('goods_img')->where(['goods_id'=>$id])->select();
        $this->assign('imgs',$imgs);
        //导航栏是在public/header里的,只有首页是显示的,其他的页面都是隐藏的,需要一个标识来决定导航栏是否显示
       // $this->assign('menu_is_show',1);
        return $this->fetch();
    }
}