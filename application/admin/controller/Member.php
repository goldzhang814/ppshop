<?php
namespace app\admin\controller;
use think\Db;
class Member extends Common{
        //添加等级,一次可以添加多条
        public function levelSet(){
                $data=Db::name('memberLvScore')->select();
                if ($this->request->isGet()){
                    //$data=Db::name('memberLvScore')->select();

                    $num=count($data);
                    $this->assign('num',$num);
                    $this->assign('data',$data);
                    return $this->fetch();
                }
                $i=0;
                $dd=[];
                //计算等级个数
                foreach ($data as $k=> $v){
                    $i++;
                }
                $lv=$i;
                $scores=input('score/a');
                //dump($scores);die();
                //找到新添加的等级,并保存在$dd中
                foreach ($scores as $k => $v){

                    if ($i<$k+1){
                        $dd[]=$scores[$i];
                        $i++;}
                }
               // dump($dd);die();
                //组装数据,要加上对应的等级level字段
                $list=[];
                foreach ($dd as $k => $v){
                    $list[]=['level'=>$lv,'score'=>$v];
                    if ($lv>$k+1){$lv=$lv+1;}
                }
                //dump($list);die();
                $flag=Db::name('memberLvScore')->insertAll($list);
                if (!$flag){$this->error('设置失败,请重试!');
                    return FALSE;
                    }
                return $this->success('ok!');
        }
        public function edit(){
            $model=Db::name("memberLvScore");
            if ($this->request->isGet()){
                $id=input('id');
                $data=$model->where(['id'=>$id])->find();
                $this->assign('data',$data);
                return $this->fetch();
            }
            $id=input('id');
            $dd=input('score');
            $flag=$model->where(['id'=>$id])->setField('score',$dd);
            if (!$flag){
                $this->error('设置出错,请重试!');
                return FALSE;
            }
            return $this->success('ok!');


        }
        public function delete(){
            $id=input('id');
            $model=Db::name("memberLvScore");
            $flag=$model->where(['id'=>$id])->delete();
            if (!$flag){
                $this->error('删除出错!');
                return FALSE;
            }
            return $this->success('ok!');
        }



}