<?php
namespace app\index\model;
use think\Model;

class Cart extends Model
{
    public function addCart($goods_id,$goods_amount,$attr_ids)
    {
        //这里要区分用户是否登录,加入用用户没有登录,就添加到session中,如果登录,则用户的数据库购物车表
        $userInfo=get_user_info();
        if ($userInfo) {
            $id = $userInfo['id'];
            //用户如果登录.要看数据库是否已经存在该商品,如有则直接加载数据库中
            $flag = $this->field(['id', 'goods_count'])->where(['goods_id' => $goods_id, 'goods_attr_ids' => $attr_ids])->find();
            if ($flag) {
                $count = $flag['goods_count'] + $goods_amount;
                $sign = $this->update(true)->save(['goods_count' => $count]);
                if (!$sign) {
                    $this->error = '加入购物车失败';
                    return FALSE;
                }

            } else {
                $sign = $this->save(['user_id'=>$id,'goods_id' => $goods_id, 'goods_count' => $goods_amount, 'goods_attr_ids' => $attr_ids]);
                if (!$sign) {
                    $this->error = '加入购物车失败';
                    return FALSE;
                }
            }
        }
        //用户没有登录,将商品添加到cookie
        else{
        //从cookie中获取购物车中的内容 ,判断其中有没有该商品
            $cart=cookie("cart")?cookie('cart'):[];
            $key=$goods_id.'-'.$attr_ids;
            //dump($key);die();
            if (array_key_exists($key,$cart)){
                $cart[$key]+=$goods_amount;
            }else{
                $cart[$key]=$goods_amount;
            }
            cookie('cart',$cart);
             }
        return TRUE;
    }

    public function listData()
    {
        //判断用户是否登录,拿数据的位置不一样
        $userInfo=get_user_info();
        //dump($userInfo);die();
        $list=[];
        if ($userInfo){
            //从数据库的购物车拿数据
            $list=$this->where(['user_id'=>$userInfo['id']])->select();

            //$data=db('goods')->field(['goods_name','shop_price','goods_thumb'])->where(['id'=>$userInfo['id']])->find();

        }
        else{
            //用户没有登录的情况
            $cart=cookie('cart')?cookie('cart'):[];
            //dump($cart);die();
            foreach ($cart as $key =>$v){
                $tmp=explode('-',$key);
                $list[]=[
                        'goods_id'=>$tmp[0],
                        'goods_attr_ids'=>$tmp[1],
                        'goods_count'=>$v
                        ];
                }
             }
        //dump($list);die();
        //获取商品属性信息

        foreach ($list as $k => $v){
            //dump($v[$k]);die();
            //获取商品属性,每件商品都是list 的一个元素,所以是个多维数组
/*            $sql="SELECT a.attr_value,b.attr_name FROM shop_goods_attr a left join shop_attribute b
                  on a.attr_id=b.id where a.id in($attr_ids)";
            $data=db('attribute')->query($sql);
            $list[$k]['attr']=$data;*/
            $list[$k]['attr']=db('goods_attr')->alias('a')->field('a.attr_value,b.attr_name')->join('shop_attribute b','a.attr_id=b.id','left')->where('a.id','in',$v['goods_attr_ids'])->select();

            $goodsInfo=db('goods')->field(['goods_name','shop_price','goods_thumb'])->where(['id'=>$v['goods_id']])->find();
            $list[$k]['goods_info']=$goodsInfo;
        }
        //dump($list);die();

        return $list;
    }

    //计算订单的金额
    public function payMoney(){
        $user=get_user_info();
        $id=$user['id'];
        $sum=$count=0;
        //获取商品的数量和单价
        $sql="SELECT a.goods_id,a.goods_count,b.shop_price from shop_cart a left join shop_goods b on  a.goods_id=b.id where user_id=$id";
        $goods=$this->query($sql);

        //dump($goods);die();

        foreach ($goods as $k=>$v) {
            $count+=$v['goods_count'];
            $sum+=$v['goods_count']*$v['shop_price'];
        }
        $arr=array(
            'count'=>$count,
            'sum'=>$sum,
        );

        //dump($arr);die();
        return $arr;




    }


}