<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/7
 * Time: 19:33
 */

namespace app\admin\controller;

use app\admin\model\AdminMenu;
use app\admin\model\AdminRole;
use think\Controller;
use think\Request;

class BaseAdmin extends Controller{
//    private static $now;
//    private static $last;
    // 登录用户
    protected $login_admin;
    // 登录用户的角色
    protected $role;
    // 定义全局权限常量
    // 管理员列表权限id
    const permission_admins = 1;
    // 添加或编辑管理员id
    const permission_admin_add = 2;
    const permission_admin_update = 3;
    // 管理员禁用
    const permission_admin_disable = 4;

	// 菜单列表页面
    const permission_menus = 6;
    // 添加菜单
    const permission_menu_add = 7;
    // 更新菜单
    const permission_menu_update = 8;
    // 删除菜单
    const permission_menu_del = 9;
	
	// 角色列表页面
    const permission_roles = 10;
    // 添加角色
    const permission_role_add = 11;
    // 更新角色
    const permission_role_update = 12;
    // 禁用角色
    const permission_role_disable = 13;
	
	// 分类列表页面
    const permission_categories = 14;
    // 添加分类
    const permission_category_add = 15;
    // 更新分类
    const permission_category_update = 16;
    // 禁用分类
    const permission_category_disable = 17;
	
    // 添加博文页面
    const permission_article_edit_html = 19;
    // 博文列表页面
    const permission_articles_html = 22;
    // 添加博文方法
    const permission_article_add = 23;
    // 更新博文
    const permission_article_update = 24;
    // 分页查询博文
    const permission_article_list = 25;
    // 软删除博文
    const permission_article_disable = 26;

    // 标签列表页面
    const permission_tags = 28;
    // 添加标签
    const permission_tag_add = 29;
    // 更新标签
    const permission_tag_update = 30;
	
    /**
     * 禁止未登录用户访问
     * 根据权限加载菜单
     * BaseAdmin constructor.
     * @param Request|null $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function __construct(Request $request = null){
        parent::__construct($request);
        // 未登录的用户不允许访问
        $this->login_admin = session('admin');
        if (!$this->login_admin){
            header('Location: /login');
            exit();
        }
        // 用户被禁用后应该退出
        if ($this->login_admin['status'] != '正常'){
            header('Location: /login');
            exit();
        }
        // 加载用户信息
        $admin_info['username'] = $this->login_admin['username'];
        $admin_info['true_name'] = $this->login_admin['true_name'];
        $admin_info['avatar'] = $this->login_admin['avatar'];
        $admin_info['add_time'] = $this->login_admin['add_time'];
        // 根据角色权限加载左侧菜单
        // 查询角色信息
        $this->role = AdminRole::getRoleById($this->login_admin['rid']);
        $menus = '暂无权限';
        $subMenusRouteNames = array();
        // 根据登录用户的rid能查询到角色
        if ($this->role){
            // 判断角色状态
            if ($this->role['status'] != '正常'){
                $menus = '已禁用';
            } else {
                if ($this->role['status'] == '正常' && $this->role['permissions']){
                    // 查询菜单并以mid为结果数组索引
                    $menus = AdminMenu::getMenuByPermissionTakeMidAsIndex($this->role['permissions']);
                    // 如果有菜单则生成菜单树，将子菜单存放到父级菜单新生成的submenu键中
                    $menus && $menus = self::getMenusTree($menus);
                    // 如果有菜单则获取子菜单路由名称
                    $menus && $subMenusRouteNames = self::getSubMenusRoutes($menus);
                }
            }
        }
        // 输出数据到模板
        $this->assign(['admin_info' => $admin_info,'subMenusRouteNames' => $subMenusRouteNames,'menus' => $menus]);
    }

    /**
     * 得到子菜单树,submenu键对应的值为子菜单
     * @param $menus
     * @return array
     */
    protected static function getMenusTree($menus){
        $menusTree = array();
        // 传入的菜单$menus是以mid为索引的
        foreach ($menus as $value) {
            // 根据parent_id是否为0判断子菜单和一级菜单
            if ($value['parent_id'] != 0){
                // 如果parent_id不等于0,则为子菜单,将当前子菜单的引用赋给父级菜单的submenu键
                $menus[$value['parent_id']]['submenu'][] = &$menus[$value['mid']];
            } else {
                // 为一级菜单,将当前一级菜单的引用赋给变量menusTree
                $menusTree[] = &$menus[$value['mid']];
            }
        }
        return $menusTree;
    }

    /**
     * 得到每个（含有子菜单）菜单对应子菜单路由名称数组
     * @param $menus
     * @return array
     */
    protected static function getSubMenusRoutes($menus){
        $subMenusRoutes = array();
        foreach ($menus as $key => $menu) {
            if (isset($menu['submenu'])){
                $route_name = [];
                foreach ($menu['submenu'] as $key => $submenu) {
                    $route_name[] = $submenu['route_name'];
                }
                $subMenusRoutes[$menu['menu_name']] = $route_name;
            }
        }
        return $subMenusRoutes;
    }

    /**
     * 得到数组的深度 算法存在问题
     * @param $array
     * @return int
     */
    protected static function array_depth($array) {
        if(!is_array($array)) return 0;
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::array_depth($value) + 1;
                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
        return $max_depth;
    }


//    protected static function preventFastClick(){
//        self::$now = time();
//        if (self::$last = null){
//            self::$last = time();
//            return true;
//        }
//        if (self::$now - self::$last > 5000){
//            self::$last = time();
//            return true;
//        } else {
//            return false;
//        }
//    }
}