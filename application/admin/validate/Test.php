<?php
namespace app\admin\validate;
use think\Validate;

class Test extends Validate{
    //设置验证规则，可以自定义验证方法，然后实现方法
    protected $rule=[
        'goods_name|商品名称'=>'require',
        'cate_id|分类' =>'require|gt:0',
        'shop_price'=>'require|gt:0',
        'market_price'=>'require|checkMarketPrice'
    ];

    //可以设置错误提示信息，
    protected $message=[
        'cate_id.gt'=>'没有选择分类，请重新选择！',
        'shop_price.gt'=>'价格不能小于0！',
        'market_price.checkMarketPrice'=>'市场价格不能小于本店售价'
    ];

    public function checkMarketPrice($value,$rule,$data){
        if($value>$data['shop_price']){
            return TRUE;
        }
        return FALSE;
    }

}
?>