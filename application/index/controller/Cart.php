<?php
namespace app\index\controller;
use think\Db;

class Cart extends Common
{
    public function add(){
        $goods_id=input('goods_id');
        $goods_amount=input('goods_amount');
       $attr=input('attr/a');
       $attr_ids=implode(',',$attr);

       //dump($attr);die();
       $cartModel=model('Cart');
       $flag=$cartModel->addCart($goods_id,$goods_amount,$attr_ids);
       if ($flag){
           return $this->success('ok,添加成功!','cart');
       }else{
           $this->error($cartModel->getError());
           return FALSE;
       }
    }

    public function cart(){
        $data=model("Cart")->listData();
        $this->assign('data',$data);
        return $this->fetch();
    }
    //ajax实现删除操作
    public function del(){
        $id=input('id/d');

        if ($id){
            $flag=Db::name('cart')->where(['id'=>$id])->delete();
            if ($flag){
                return json(['status'=>1,'msg'=>'Cart']);}
            else{
                return json(['status'=>0,'msg'=>'FALSE']);
            }
        }else{
            $goods_id=input('goods_id');
            $goods_attr_ids=input('goods_attr_ids');

            $cart=cookie('cart');

            $key=$goods_id.'-'.$goods_attr_ids;
            unset($cart[$key]);
            cookie('cart',$cart,3600*24*7);
            $flag=isset($cart[$key]);
            if (!$flag){
                return json(['status'=>1,'msg'=>'Cookie']);
            }else{
                return json(['status'=>0,'msg'=>'FALSE']);
            }

        }
    }

    //测试cookie
/*    public function cleanCookie(){
        dump(cookie('cart'));die();
        cookie('cart',null);
    }*/
}