<?php
namespace app\index\controller;
use think\Controller;
class Common extends Controller{


    public function __construct()
    {
        parent::__construct();
        //获取所有的分类信息
        $data=db('category')->select();
        $category=$this->getAllTree($data);

        $userInfo=get_user_info();
        $this->assign('userInfo',$userInfo);
        $this->assign('category',$category);
    }

    //商品结算需要先登录检查,$dest为登录之后页面跳转的目标地址,由控制器和方法名组成,如index/index;
    function checkIsLogin($dest)
    {
        $userInfo=get_user_info();
        if (!$userInfo){
            return $this->error('请先登录!',url("index/User/login").'?dest='.$dest);
        }else{
            return TRUE;
        }

    }

    //此无限极分类为最简单的的方法
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

