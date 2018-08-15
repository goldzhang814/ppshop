<?php
namespace app\admin\model;
use app\admin\validate\Test;
use think\Model;
use app\Common;
use think\db;

class Goods extends Model{

    public function addGoods()
    {
        //获取表单提交 的数据；
        $data=input();
      /*  if(!$data['goods_name']){
            $this->error='商品名称错误';
            return FALSE;
        }*/
        if ($this->checkGoodsSn($data)===FALSE){
            $this->error='货号重复';
            return FALSE;
        }

        $vali=new Test();
        if(!$vali->check($data)){
            $this->error=$vali->getError();
            return FALSE;
        }

        if ($this->uploadImg($data)===FALSE){
            return FALSE;
        }

        $data['addtime']=time();

       // dump($data);exit();

        Db::startTrans();
        try{
            //商品表入库
            Goods::allowField(true)->save($data);
            $goods_id=Goods::getLastInsId();

            //dump(input('attr/a'));die();
            //属性值入库
            model("GoodsAttr")->insertData($goods_id,input('attr/a'));

            //上传商品相册
            $this->uploadImgs($goods_id);

           Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            $this->error='数据写入错误!';
            return FALSE;
        }
        return TRUE;
    }

    //&引用传递,直接更改源数据
    public function checkGoodsSn(&$data){
        if(!$data['goods_sn']){
            $data['goods_sn']=uniqid();
            return $data;
            //var_dump($data['goods_sn']);exit;
        }else{
            if (Goods::get(['goods_sn'=>$data['goods_sn']])){
                return FALSE;
            }
        }
    }
    //上传商品图片
    public function uploadImg(&$data){
        //图片上传
        $file=request()->file('goods_img');
        //图片不能为空
        if($file===null){
            $this->error='图片必须上传';
            return FALSE;
        }
        $info=$file->validate(['ext'=>'jpg,jpeg,png'])->move('uploads');
        if (!$info){
            $this->error=$file->getError();
            return FALSE;
        }
        $data['goods_img']=str_replace('\\','/','uploads/'.$info->getSaveName());
        //生成缩略图
        $img=\think\Image::open($data['goods_img']);
        $data['goods_thumb']='uploads/'.date(('Ymd').'/thumb_').$info->getFilename();
        $img->thumb(150,150)->save($data['goods_thumb']);

        //将图片传到nginx服务器上

        moveImageToServer($data['goods_img']);
        moveImageToServer($data['goods_thumb']);
    }
    //上传商品相册
    public function uploadImgs($goods_id){
        $image=request()->file('image');
        //dump($image);die();
        $list=[];
        if ($image){
            foreach($image as $img){
            //验证图片的后缀是否合法,如果合法就转移图片的位置保存
                $info=$img->validate(['ext'=>'jpg,png,jpeg'])->move('uploads');
                if (!$info){
                    $this->error='图片格式不支持,图片上传失败!';
                    return FALSE;
                }
            //组装要保存的路径文件名
            $goods_img=str_replace('\\','/','uploads/'.$info->getSaveName());
            $goods_thumb='uploads/'.date('Ymd').'/thumb_'.$info->getFileName();
            $img=\think\Image::open($goods_img);
            $img->thumb(150,150)->save($goods_thumb);
            //将相册资源文件转移到服务器上

               moveImageToServer($goods_img);
               moveImageToServer($goods_thumb);
            $list[]=[
              'goods_id'=>$goods_id,
              'goods_img'=>$goods_img,
              'goods_thumb'=>$goods_thumb
            ];
            }

            //dump($list);die();
            if (!$list){
            $this->error='图片保存失败!';
            return FALSE;
            }
            //将图片的路径写入数据库
            return Db::name('goods_img')->insertAll($list);
        }else{
            $this->error='图片保存失败!';
            return FALSE;
        }
    }

    public function saveEdit(){

        $data=input();
        $goods_id=input('goods_id');

/*        if ($this->checkGoodsSn($data)===FALSE){
            $this->error='货号重复';
            return FALSE;
        }
        $this->checkGoodsSn($data);*/
        $vali=new Test();
        if(!$vali->check($data)){
            $this->error=$vali->getError();
            return FALSE;
        }
        //修改的时候可以不传照片
/*        if ($this->uploadImg($data)===FALSE){
            return FALSE;
        }*/
        $data['addtime']=time();
        //更新goods表
        $flag=Goods::allowField(true)->isUpdate(true)->save($data,['id'=>$goods_id]);
        //更新商品属性表,先删除,再添加
        $goodsAttrModel=model('GoodsAttr');
        $goodsAttrModel->where(['goods_id'=>$goods_id])->delete();
        $res=$goodsAttrModel->insertData($goods_id,input('attr/a'));
        //上传新增的图片
        $ii=$this->uploadImgs($goods_id);
/*       dump($flag);
        dump($res);
        dump($ii);die();*/

        if ($flag===FALSE||$res===FALSE||$ii===FALSE){
            $this->error='修改失败,请重试!';
            return FALSE;
        }
        return TRUE;
    }

   /* public function  indexGoods(){
        $data=self::alias('a')->field('a.*,b.cname')->join('shop_category b','a.cate_id=b.id','left')->paginate(2);
        return $data;
    }*/

    public function listGoods($isdel){
        $where=[];

        //获取没有删除的商品
        $where['is_del']=$isdel;
        //根据类别查找商品
        $cate_id=input('cate_id');
        if ($cate_id){
            $data=model('Category')->select();
            $sonTree=model('Category')->getAllTree($data,$cate_id,0,true);
            //dump($sonTree);
            foreach($sonTree as $v){
                $ids[]=$v['id'];
            }
            //将自己添加进来
            $ids[]=$cate_id;
            $where['cate_id']=['in',$ids];
        }

        $intro_type=input('intro_type');
        if ($intro_type){

            $where['a.'.$intro_type]=1;
        }

        $is_sale=input('is_sale');
        if ($is_sale){
            if ($is_sale!=2){
            $where['a.is_sale']=$is_sale;}
        }

        $keyword=input('keyword');
        if($keyword){
            $where['a.goods_name']=['like','%'.$keyword.'%'];
        }


        $data=self::alias('a')->field('a.*,b.cname')->join('shop_category b','a.cate_id=b.id','left')
            ->where($where)->paginate(5,FALSE,['query'=>input()]);
        //数据不对时查看sql语句对应
        //echo $this->getLastSql();

        return $data;

    }

    public function changeStatus(){
        $data=input();
        $allow=['is_hot','is_new','is_sale','is_rec'];
        if (!in_array($data['field'],$allow)){
            $this->error='非法操作';
        }
        $goodsInfo=Goods::get($data['goods_id'])->toArray();
        $status=$goodsInfo[$data['field']]?0:1;
        $this->where(['id'=>$data['goods_id']])->setField($data['field'],$status);

        return $status;
    }

}