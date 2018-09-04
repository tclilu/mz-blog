<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/18
 * Time: 19:28
 */

namespace app\admin\model;

use app\admin\controller\Role;
use think\Model;

class AdminRole extends Model {
    // 主键字段为rid
    protected $pk = 'rid';
    // 权限字段自动使用json格式写入和写出，使用save新增数据时失效
    protected $type = ['permissions' => 'json'];

    /**
     * 获取器，获取status字段值后自动处理
     * @param $value
     * @return mixed
     */
    public function getStatusAttr($value){
        $status = [1 => '正常',0 => '<jy style="color: #ff0000;">禁用</jy>'];
        return $status[$value];
    }

    /**
     * 查询所有角色信息
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRoles(){
        return AdminRole::field('rid,name')->select();
    }

    /**
     * 查询所有角色信息包括status字段
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRolesIncludeStatus(){
        return AdminRole::field('rid,name,status')->select();
    }

    /**
     * 根据角色id查询角色信息（非静态）
     * @param $rid
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRoleByRid($rid){
        return AdminRole::where('rid','EQ',$rid)->find();
    }

    /**
     * 根据角色id查询角色信息（静态）
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRoleById($id){
        return AdminRole::where('rid','EQ',$id)->find();
    }

    /**
     * 添加角色
     * @param $roleInfo
     * @return false|int
     */
    public function addRole($roleInfo){
        return AdminRole::data($roleInfo)->save();
    }

    /**
     * 更新对应id角色信息
     * @param $roleInfo
     * @param $rid
     * @return AdminRole
     */
    public function updateRoleByRid($roleInfo,$rid){
        return AdminRole::where('rid','EQ',$rid)->update($roleInfo);
    }

    /**
     * 检测角色是否已经存在
     * @param $name
     * @return int|string 返回角色的数量
     */
    public function checkRoleIsExist($name){
        return AdminRole::where('name','EQ',$name)->count('1');
    }

    /**
     * 禁用对应rid的角色
     * @param $rid
     * @return AdminRole
     */
    public function disableRoleByRid($rid){
        return AdminRole::where('rid','EQ',$rid)->update(['status' => 0]);
    }
}