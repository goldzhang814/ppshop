<?php
namespace app\index\controller;

class Order extends Common{

    public function checkOut()
    {


        //结算时用户必须登录,先判断用户是否登录
        $cont=$this->request->controller();
        $action=$this->request->action();
        $dest=$cont.'/'.$action;
        $user=$this->checkIsLogin($dest);
        if ($user){
            //获取用户的id
            $user=cookie('_userinfo');
            $info=decrypt($user);
            //获取到stdClass类
            $user_id=json_decode($info)->id;

            $addr=db('address')->where(['user_id'=>$user_id])->select();
            $data=model("Cart")->listData();
            $payMoney=model("Cart")->payMoney();

            $this->assign('addr',$addr);
            $this->assign('data',$data);
            $this->assign('payMoney',$payMoney);
            return $this->fetch();

        }else{
            return $this->error('请先登录才能结算!','index/user/login');
        }
    }

    //保存用户输入的地址信息
    public function saveAddr(){
        //获取用户的收货地址信息
        //$addr=db("region")->where(['pid'=>$pid]);
    }

    public function getAllAddress(){
        $pid=$_GET['pid']?$_GET['pid']:1;
        $rows=[];
        $sql="select * from region where pid=$pid";
        $res=db::name("address")->query($sql);

        while ($a=mysqli_fetch_assoc($res)) {
            $rows=$a;
        }
        return json_encode($rows);
    }
    //设置默认收获地址
    public function setDefault(){
        //{eq name="$vo.default" value='1'}<span style="background-color: #9DADCE">默认地址</span>{/eq}
        //对应地址的id
        $id=input('id');
        $user_id=input('user_id');
        $reset=db("address")->where(['user_id'=>$user_id])->setField(['default'=>0]);
        if ($reset===FALSE){return $this->error('设置失败!');}
        $flag=db("address")->where(['id'=>$id])->setField(['default'=>1]);
        if ($flag===FALSE){return $this->error('设置失败!');}

/*        //获取用户的id
        $user=cookie('_userinfo');
        $info=decrypt($user);
        //获取到stdClass类
        $user_id=json_decode($info)->id;*/
        $addr=db('address')->where(['user_id'=>$user_id])->select();
        $this->assign('addr',$addr);
        return $this->fetch();


    }

    public function submitOrder()
    {

        $data=input();
        dump($data);

        $user=cookie('_userinfo');
        $info=decrypt($user);
        //获取到stdClass类
        $user_id=json_decode($info)->id;

        $addrInfo=db('address')->where(['id'=>input('address_id')])->find();

        $receiver=$addrInfo['receiver'];

        $address=$addrInfo['province'].$addrInfo['city'].$addrInfo['region'].$addrInfo['street'];

        $payMoney=model("Cart")->payMoney();

        $order=[
                'order_num'=>time().mt_rand(100000,999999),
                'user_id'  =>$user_id,
                'receiver' =>$receiver,
                'address'  =>$address,
                'tel'      =>$addrInfo['phone'],
                'pay'      =>$data['pay'],
                'money'    =>$payMoney['sum'],
                'status'   =>0,
                'addtime'  =>time()
            ];
        //dump($order);die();
        $flag=db('order')->insert($order);
        if (!$flag){
            return $this->error('订单提交失败!','order/checkOut');
        }
        $order_id=db('order')->getLastInsID();

        $goodsInfo=db('cart')->where(['user_id'=>$user_id])->select();
        $order_detail=[];
        foreach ($goodsInfo as $k=>$v){
            $order_detail[]=[
                'order_id'      =>$order_id,
                'goods_id'      =>$v['goods_id'],
                'goods_count'   =>$v['goods_count'],
                'goods_attr_ids'=>$v['goods_attr_ids']
            ];
        }

        $res=db('order_detail')->insertAll($order_detail);
        if (!$res){
            return $this->error('订单提交失败!','order/checkOut');
        }
        //dump($order_detail);die();
        return $this->fetch();
    }


}