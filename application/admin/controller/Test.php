<?php
namespace app\admin\controller;

use think\Db;
use think\Validate;

class Test extends Common{

    public  function forOne(){
        $arr=[88,
            [1,2,3],
            [4,5,6],
            [7,8,9],
            10
        ];
        //$arr=[1,2,3,4];
        foreach ($arr as $v){
            var_dump($v);
        }
        exit();
    }
    //测试缓存
    public function cacheSet(){
       cache('name','gold',3600);
    }
    public function cacheGet(){
        $dd=cache('name');
        dump($dd);
    }

    //避免链表查询，释放数据库的压力
    public function avoidLinkTable(){

        $types=Db::name('type')->select();
        $attr= Db::name('attribute')->select();
        $type=[];
        foreach($types as $v){
        //二维数组，将每个一维下标[0,1,2..]转化为 其对应元素的里面的id,保存在一个新的数组中
            //
            $type[$v['id']]=$v;
        }
        dump($type);
        foreach ($attr as $key=>$value){
            //$value=$value->toArray();
            $value['type_name']=$type[$value['type_id']]['type_name'];
            $attr[$key]=$value;
        }
        dump($attr);die();
    }

    public function moveFile(){
        $image='uploads/20180716/93def7873f04b371c2e8371ff1ef85a8.jpeg';
        include_once '../extend/ftp.php';
        $ftp=new \ftp('192.168.164.134','21','ftpuser','123456');

        $ftp->up_file($image,$image);
    }
    public function index(){
        $data=['name'=>'gold','age'=>22,'dept'=>'php'];
        $rule=[
            'name'=>'require',
            'age'=>'require|gt:23',
        ];
        $validate=new \think\Validate();
        $flag=$validate->check($data,$rule);
        dump($flag);
        echo $validate->getError();
    }

    public function indexA(){
        $data=['name'=>'gold','age'=>22,'dept'=>'jj'];
        $rule=[
            'name'=>'require',
            'age'=>'require|lt:14',
            'dept'=>'require|checkDp:aaaaaa'
        ];
        $validate=new \think\Validate();
        //$validate=$this->validate($data,'Test');
        //dump($validate);
        $flag=$validate->check($data,$rule);
        dump($flag);
        echo $validate->getError();
    }
    public function indexB(){
        $data=['name'=>'gold','age'=>22,'dept'=>'jj'];
        $rule=[
            'name'=>'require',
            'age'=>'require|lt:14',
            'dept'=>'require|checkDp:aaaaaa'
        ];
        $validate=new \think\Validate();
        //dump($validate);
        $flag=$validate->check($data,$rule);
        dump($flag);
        echo $validate->getError();
    }

}