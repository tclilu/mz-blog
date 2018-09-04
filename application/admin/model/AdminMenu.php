<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/18
 * Time: 19:27
 */

namespace app\admin\model;

use think\Model;

class AdminMenu extends Model {
    /**
     * 根据权限查询以mid为索引的菜单信息
     * @param $permission
     * @return array
     */
    public static function getMenuByPermissionTakeMidAsIndex($permission){
        // 根据权限查询对应菜单（不能隐藏和禁用）
        $where = 'mid in(' . implode(',',$permission) . ') and is_hidden=1 and status=1';
        return AdminMenu::where($where)->column('mid,parent_id,icon_code,menu_name,route_name','mid');
    }

    /**
     * 查询以mid为索引的菜单
     * @return array
     */
    public static function getMenuTakeMidAsIndex(){
        return AdminMenu::column('mid,parent_id,menu_name,status','mid');
    }
    /**
     * 根据pid查询菜单
     * @param $pid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMenusByParentId($pid){
        return AdminMenu::where('parent_id','EQ',$pid)->select();
    }

    /**
     * 根据mid查询pid
     * @param $mid
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getParentIdByMid($mid){
        return AdminMenu::field('parent_id')->where('mid','EQ',$mid)->find();
    }

    /**
     * 添加菜单
     * @param $menuInfo
     * @return false|int
     */
    public function addMenu($menuInfo){
        return AdminMenu::data($menuInfo)->save();
    }

    /**
     * 删除分类
     * @param $mid
     * @return int
     */
    public function delMenu($mid){
        return AdminMenu::where('mid','EQ',$mid)->delete();
    }

    /**
     * 更新分类
     * @param $menuInfo
     * @param $mid
     * @return AdminMenu
     */
    public function updateMenuByMid($menuInfo,$mid){
        return AdminMenu::where('mid','EQ',$mid)->update($menuInfo);
    }
}