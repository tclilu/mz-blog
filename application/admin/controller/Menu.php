<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/8
 * Time: 22:22
 */

namespace app\admin\controller;

use app\admin\model\AdminMenu;
use app\common\exception\PermissionNotHaveException;
use think\Request;

class Menu extends BaseAdmin {
    protected $adminMenu = null;

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->adminMenu = new AdminMenu();
    }

    /**
     * 渲染菜单列表视图
     * @return mixed
     * @throws PermissionNotHaveException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function menu_list(){
        // 权限判断
        if (!in_array(parent::permission_menus,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        // 获取路由变量
        // 接收子菜单按钮传递的pid
        // 接收返回上级菜单按钮传递的pid
        // 第一次渲染页面时未传递pid,int强转后值为0,恰好为一级菜单
        $data['pid'] = (int)$this->request->route('pid');
        $data['menus'] = $this->adminMenu->getMenusByParentId($data['pid']);
        // 大于0表示传递了pid
        if ($data['pid'] > 0){
            // 当前子菜单对应的上级菜单应该为  所有parent_id等于 (mid等于当前pid的菜单的parent_id) 的菜单
            $data['backid'] = ($this->adminMenu->getParentIdByMid($data['pid']))['parent_id'];
        }
        $this->assign('data',$data);
        return $this->fetch('menus');
    }

    /**
     * 保存菜单
     */
    public function menu_save(){
        $pid = (int)input('post.pid');
        $sort_ids = input('post.sort_ids/a');
        $icon_codes = input('post.icon_codes/a');
        $menu_names = input('post.menu_names/a');
        $route_names = input('post.route_names/a');
        $is_hiddens = input('post.is_hiddens/a');
        $status = input('post.status/a');
        // $key对应的就是当前菜单的mid
        foreach ($sort_ids as $key=>$sort_id) {
            $menu['parent_id'] = $pid;
            $menu['sort_id'] = $sort_id;
            // 使用htmlentities函数转义HTML标签
            $menu['icon_code'] = htmlentities($icon_codes[$key]);
            $menu['menu_name'] = htmlentities($menu_names[$key]);
            $menu['route_name'] = htmlentities($route_names[$key]);
            $menu['is_hidden'] = isset($is_hiddens[$key]) ? 1 : 0;
            $menu['status'] = isset($status[$key]) ? 1 : 0;
            // 最后一行,如果存在菜单名称则添加菜单
            if ($key == 0 && $menu_names[0]){
                // 权限判断
                if (!in_array(parent::permission_menu_add,$this->role['permissions'])){
                    throw new PermissionNotHaveException();
                }
                $this->adminMenu->addMenu($menu);
            }
            if ($key > 0){
                // 如果图标代码、菜单名称和路由名称全为空则删除该菜单
                if ($menu['icon_code'] == '' && $menu['menu_name'] == '' && $menu['route_name'] == ''){
                    // 权限判断
                    if (!in_array(parent::permission_menu_del,$this->role['permissions'])){
                        throw new PermissionNotHaveException();
                    }
                    $this->adminMenu->delMenu($key);
                } else {
                    // 权限判断
                    if (!in_array(parent::permission_menu_update,$this->role['permissions'])){
                        throw new PermissionNotHaveException();
                    }
                    // 修改菜单
                    $this->adminMenu->updateMenuByMid($menu,$key);
                }
            }
        }
        exit(json_encode(array('code'=>2002,'msg'=>'保存成功')));
    }
}