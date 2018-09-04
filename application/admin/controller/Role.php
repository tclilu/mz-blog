<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/8
 * Time: 22:22
 */

namespace app\admin\controller;

use app\admin\model\AdminMenu;
use app\admin\model\AdminRole;
use app\common\exception\PermissionNotHaveException;
use think\Request;

class Role extends BaseAdmin {
    protected $adminRole = null;

    /**
     * 构造函数，创建AdminRole模型
     * Role constructor.
     * @param Request|null $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->adminRole = new AdminRole();
    }

    /**
     * 渲染角色列表页面视图
     * @return mixed
     * @throws PermissionNotHaveException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function role_list(){
        // 权限判断
        if (!in_array(parent::permission_roles,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        $roles = $this->adminRole->getRolesIncludeStatus();
        $this->assign('roles',$roles);
        return $this->fetch('roles');
    }

    /**
     * 渲染添加|编辑角色页面视图
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function role_edit(){
        // 接收可选路由变量rid
        // 点击添加时不传递rid，使用int强转后为0
        $rid = (int)($this->request->route())['rid'];
        $role = array('rid'=>'','name'=>'','permissions'=>array(),'status'=>'');
        if ($rid > 0){
            // 查询当前角色信息
            $role = $this->adminRole->getRoleByRid($rid);
        }
        // 查询以mid为索引的所有菜单
        $menus = AdminMenu::getMenuTakeMidAsIndex();
        // 生成子菜单树,父级菜单中生成一个submenu键存放子菜单
        $menuTree = parent::getMenusTree($menus);
        $data['menus'] = array();
        // 如果菜单树深度大于2，则含有三级菜单
        if (parent::array_depth($menuTree) > 2){
            foreach ($menuTree as $value) {
                // 将三级菜单内容合并到二级菜单中，方便前台勾选
                $value['submenu'] = isset($value['submenu']) ? self::formatSubmenu($value['submenu']) : false;
                $data['menus'][] = $value;
            }
        }
        // 输出到模板
        $this->assign(['role' => $role,'menus' => $data['menus']]);
        return $this->fetch('role_edit');
    }

    /**
     * 保存角色信息
     */
    public function role_save(){
        // 接收表单数据
        // 添加时int强转null结果为0
        $rid = (int)input('post.rid');
        // 使用htmlentities函数转义HTML标签
        $data['name'] = htmlentities(trim(input('post.role_name')));
        $permissions = input('post.menus/a');
        // 类型转换，权限字段自动json写入
        $permissions && $data['permissions'] = json_encode(array_keys($permissions));
        $data['status'] = (int)input('post.status');
        // 非空校验，权限可以为空
        if ($data['name'] == ''){
            exit(json_encode(array('code'=>4008,'msg'=>'角色名称不能为空')));
        }
        // 如果rid大于0表示更新角色，如果等于0表示新增角色
        if ($rid > 0){
            // 权限判断
            if (!in_array(parent::permission_role_update,$this->role['permissions'])){
                throw new PermissionNotHaveException();
            }
            // 更新角色
            $result = $this->adminRole->updateRoleByRid($data,$rid);
        } else {
            // 校验角色是否已存在
            $role = $this->adminRole->checkRoleIsExist($data['name']);
            if ($role){
                exit(json_encode(array('code'=>6003,'msg'=>'该角色已存在')));
            }
            // 新增角色
            // 新增时，自动转换自动写入json字段失效，手动转换成json格式
            // 权限判断
            if (!in_array(parent::permission_role_add,$this->role['permissions'])){
                throw new PermissionNotHaveException();
            }
            $result = $this->adminRole->addRole($data);
        }
        if (!$result){
            exit(json_encode(array('code'=>5004,'msg'=>'保存失败')));
        }
        exit(json_encode(array('code'=>2002,'msg'=>'保存成功')));
    }

    /**
     * 禁用角色方法
     */
    public function role_disable(){
        // 权限判断
        if (!in_array(parent::permission_role_disable,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        $rid = (int)input('post.rid');
        $result = $this->adminRole->disableRoleByRid($rid);
        if (!$result){
            exit(json_encode(array('code'=>5005,'msg'=>'禁用失败')));
        }
        exit(json_encode(array('code'=>2003,'msg'=>'禁用成功')));
    }

    /**
     * 格式化二级菜单
     * 使所有三级以上菜单全部和二级菜单在同一级，方便前台勾选
     * @param $submenu 二级菜单
     * @param array $result 传递引用
     * @return array 只包含一级的菜单
     */
    private static function formatSubmenu($submenu,&$result = array()){
        // 遍历第二级菜单
        foreach ($submenu as $value) {
            // 如果存在第三级菜单
            if (isset($value['submenu'])){
                // 将第三级菜单内容存放到第二级菜单中
                // 使用tmp接收第三级菜单
                $tmp = $value['submenu'];
                // 销毁第三级菜单内容
                unset($value['submenu']);
                // 将当前不存在第三级菜单的菜单放置数组中
                $result[] = $value;
                // 整理三级菜单的格式,如果存在四级菜单（无限级菜单）
                self::formatSubmenu($tmp,$result);
            } else {
                // 不存在三级菜单
                $result[] = $value;
            }
        }
        return $result;
    }
}