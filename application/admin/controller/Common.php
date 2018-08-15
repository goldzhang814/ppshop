<?php 
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;


class Common extends Controller
{
    public $request;
    public $_user=[];//保存用户登录后的信息
    public $is_check=TRUE;//用户登录后是否验证权限,对于超级管理员是不用检验的
    public function __construct()
    {
        parent::__construct();
        $this->request=request();
        $permission_ids=[];
        //检验用户是否登录
        if(!session('_user')){
            $this->error('非法登陆！','admin/login/index');
        }
      // var_dump(session('_user'));die();
        //登陆后记录用户的标识
        $this->_user['admin_id']=session('_user');

        $role_info=\db('admin_role')->where(['admin_id'=>$this->_user['admin_id']])->find();
        //dump($role_info);die();
        if (!$role_info){
            $this->error('角色错误!','index');
        }
        $this->_user['role_id']=$role_info['role_id'];
        //dump($this->_user['role_id']);
        //超级管理员不需要验证
        if ($this->_user['role_id']==1){
            $this->is_check=FALSE;
            //如果判断是超级管理员,获取所有的显示的权限就可以退出了
            $this->_user['menus']=\db('permission')->where(['is_show'=>'1'])->select();
            return TRUE;
        }else{
            $permission=\db('role_permission')->where(['role_id'=>$this->_user['role_id']])->select();
            foreach ($permission as $v){
               $permission_ids[]=$v['permission_id'];
           }
        }
        $permissions=\db('permission')->where(['id'=>['in',$permission_ids]])->select();
        //dump($permissions);die();
        foreach($permissions as $k => $v){
            //保存用户的控制器和方法
            $this->_user['permit'][]=$v['controller_name'].'/'.$v['action_name'];
            if ($v['is_show']==1){
            //保存用户显示的菜单目录,要保存id信息,方便在模板中遍历父子数据
              $this->_user['menus'][]=$v;
            }
        }
        //dump($this->_user['menus']);die;
        //后台首页的访问任何用户都可以访问,可以判断控制器的名字是不是index,也可以直接加到权限列表中
/*        if ($this->is_check){
            $cont=\request()->controller();
            if ($cont=='index'){

            }
        }*/

        if ($this->is_check){
            $this->_user['permit'][]='index/index';
            $this->_user['permit'][]='index/top';
            $this->_user['permit'][]='index/menu';
            $this->_user['permit'][]='index/main';
            //获取当前用户要访问的控制器名和方法名
            $action=strtolower(\request()->controller().'/'.\request()->action());
            if (!in_array($action,$this->_user['permit'])){
                $this->error('无权访问!','login/index');
                return FALSE;
            };
        }
    }


    public function getAllCate($data,$exclude=null){
        return $this->getTree($data,0,0,$exclude);
    }
    public function getTree($data,$parent_id,$lv=0,$exclude=0){

        static $list=[];
        //$exclude=(int) $exclude;
        foreach($data as $v ){

           if ($v['parent_id']===$exclude){
                continue;
            }
            if ($v['parent_id']==$parent_id){
                $v['lv']=$lv;
                $list[]=$v;
                $this->getTree($data,$v['id'],$lv+1);
            }
        }
        //dump($list);die();
        return $list;
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
    public function get_type_info(){
        $types=Db::name('type')->select();
        return $types;
    }
}

