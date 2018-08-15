<?php
namespace app\admin\controller;
use think\Db;
class Attribute extends Common
{
    // add attribute
    public function add()
    {
        if ($this->request->isGet()) {
            $types = Db::name('type')->select();
            $this->assign('types', $types);
            return $this->fetch();
        }
        $data = input();
        //当为文本框时，默认值不需要
        if ($data['attr_input_type'] == 1) {
            unset($data['attr_input_type']);
        } else {
            if (!$data['attr_values']) {
                $this->error('select选择时默认值必须有！');
            }
        }
        Db::name('attribute')->insert($data);
        $this->success('ok');

    }

    //list attribute
    public function index()
    {
        $model=model('attribute');
        $data=$model->index();
        $this->assign('data', $data);
        return $this->fetch();
    }

    //edit attribute
    public function edit()
    {
        if ($this->request->isGet())
        {
            $id=input('id');
            $types=Db::name('type')->select();
            $data = Db::name('attribute')->where('id', $id)->find();
            $this->assign('types', $types);
            $this->assign('data', $data);
            //dump($data);
            return $this->fetch();
        }

        $model=model('attribute');
        $flag=$model->editAttr();
        if ($flag===FALSE){
            $this->error($model->getError());

            return FALSE;
        }
        $this->success('ok');
        return TRUE;


    }

    //delete attribute
    public function delete(){
        $id=input('id');
        $flag=Db::name('attribute')->delete(['id'=>$id]);
        if ($flag===FALSE){
            $this->error('删除失败！请重试。','index');
            return FALSE;
        }
        return $this->success('ok');
    }

}