<?php
namespace app\admin\controller;
use think\Db;
class Goods extends Common{
    //状态切换
    public function changeStatus(){
        $flag=model('Goods')->changeStatus();
        if ($flag===FALSE){
            return json(['status'=>0,'msg'=>$this->model()->getError()]);
        }
        return json(['status'=>1,'msg'=>'ok','imgStatus'=>$flag]);
    }

    public function add(){
        if ($this->request->isGet()){

            //显示商品所属的类型
            $types=$this->get_type_info();
            $this->assign('types',$types);
           // dump($types);die;


            $data=Db::name('category')->select();
            $tree=$this->getAllCate($data);
            $this->assign('tree',$tree);

            return $this ->fetch();
        }

        //$goods_id=9;

        $model=model('Goods');
        $flag=$model->addGoods();
            if ($flag===false){
                $this->error($model->getError());
            }
         $this->success('ok');
    }

    //选择商品类型时触发ajax弹出类型下所有的属性信息
    public function showAttr(){
        $type_id=input('type_id');
        $data=model('Attribute')->getTypeAttr($type_id);
       //dump($data) ;die;

        $this->assign('data',$data);

        //这里的ajax向模板中返回的就是一个组装好的html代码块,放在模板中可以直接使用
        //dump() ;die;
        return  $this->fetch();

    }

    public function index(){
        $da=Db::name('category')->select();
        $tree=$this->getAllTree($da);
        $this->assign('tree',$tree);

        if ($this->request->isGet()){
                //处理模板中搜索框中的数据回写，不然会报错
                $pick=[
                    'cate_id'=>0,
                    'intro_type'=>'all',
                    'is_sale'=>2,
                    'keyword'=>'',
                ];
            $model=model('Goods');
            $data=$model->listGoods(1);
            //dump($data);exit();

            $this->assign('pick',$pick);
            $this->assign('data',$data);
            return $this->fetch();
        }

        $pick=input();
        $model=model('Goods');
        $data=$model->listGoods(1);
        //dump($data);exit();
        $this->assign('pick',$pick);
        $this->assign('data',$data);

        return $this->fetch();
    }

    public function edit(){
        if ($this->request->isGet()){
            $id=input('goods_id');
            $info=Db::name('goods')->where('id',$id)->find();
            $this->assign('info',$info);

            $dat=Db::name('category')->select();
            $tree=$this->getAllTree($dat);
            $this->assign('tree',$tree);

            //显示商品所属的类型
            $types=$this->get_type_info();
            $this->assign('types',$types);

            //显示商品的属性
            $goodsAttr=model('GoodsAttr')->getGoodsAttr($id);
            //dump($goodsAttr);
            $this->assign('goodsAttr',$goodsAttr);

            //获取商品相册里的图片,用于在前台展示
            $imgs=db('goods_img')->where(['goods_id'=>$id])->select();
            $this->assign('imgs',$imgs);

            return $this ->fetch();
        }

        $model=model('Goods');
        $flag=$model->saveEdit();
        if ($flag===FALSE){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
    public function delImg(){
        $id=input('id');
        $flag=Db::name('goods_img')->where(['id'=>$id])->delete();
        return json(['status'=>1,'flag'=>$flag]);

    }

    //回收站功能
    public function toRecycle(){
        $id=input('id');
        $flag=Db::name("Goods")->where('id',$id)->update(['is_del'=>0]);
        if ($flag===FALSE){

            return json(['flag'=>0,'msg'=>$this->error('放入回收站失败')]);
        }
        return json(['flag'=>1]);

    }
    public function recycle(){
        $list=model('Goods')->listGoods(0);
        $this->assign('list',$list);
        return $this->fetch('recycle');

    }
    public function restore(){
        $id=input('id');
        $flag=Db::name("Goods")->where('id',$id)->update(['is_del'=>1]);
        if (!$flag){
            $this->error('还原失败','recycle');
            return FALSE;
        }
        $list=model('Goods')->listGoods(0);
        $this->assign('list',$list);
        return $this->fetch('recycle');
    }
    public function remove(){
        $id=input('id');
        $a=Db::name('Goods')->delete($id);
        $b=Db::name('GoodsAttr')->where(['goods_id'=>$id])->delete();
        $c=Db::name('GoodsImg')->where(['goods_id'=>$id])->delete();

        if ($a&&$b&&$c){
            return $this->error('删除失败!','recycle');
        }
       return $this->success('ok!','recycle');
    }




}