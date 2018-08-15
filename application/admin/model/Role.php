<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Role extends Model{
        public function getRules(){

            $rules=Db::name('permission')->select();
            return $rules;

        }
}