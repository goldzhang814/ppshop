<?php
namespace app\admin\controller;


class Index extends Common
{
    public function index()
    {
        //获取到热卖商品的数据

        return $this->fetch();
    }
    public function top()
    {
        return $this->fetch();
    }
    public function main()
    {
        return $this->fetch();
    }
    public function menu()
    {
        //dump($this->_user['menus']);die();
        $this->assign('menus',$this->_user['menus']);
        return $this->fetch();
    }


}
