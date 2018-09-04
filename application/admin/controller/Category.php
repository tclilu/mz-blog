<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/14
 * Time: 12:01
 */

namespace app\admin\controller;

use app\common\exception\ParamErrorException;
use app\common\exception\PermissionNotHaveException;
use think\Request;

class Category extends BaseAdmin {
    protected $categoryModel = null;

    /**
     * Category constructor.
     * @param Request|null $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->categoryModel = new \app\common\model\Category();
    }

    /**
     * 渲染分类列表页面
     * @return mixed
     * @throws PermissionNotHaveException
     */
    public function category_list(){
        // 权限判断
        if (!in_array(parent::permission_categories,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        // column方法不经过获取器，设置getStatusAttr无效
        $categories = $this->categoryModel->getAllCategoriesIncludeStatus();
        $this->assign(['empty' => '<tr><td colspan="5">~暂无分类~</td></tr>','categories' => $categories]);
        return $this->fetch('categories');
    }

    /**
     * 保存分类信息 添加|编辑
     */
    public function category_save(){
        // 接收表单数据
        // 添加时cid为空,int强转后值为0
        $cid = (int)input('post.cid');
        // 使用htmlentities函数转义HTML标签
        $data['name'] = htmlentities(trim(input('post.name')));
        $data['photo'] = trim(input('post.photo'));
        $data['status'] = (int)input('post.status');
        if ($data['name'] == ''){
            exit(json_encode(array('code'=>4009,'msg'=>'分类名称不能为空')));
        }
        if ($cid == 0){
            // 权限判断
            if (!in_array(parent::permission_category_add,$this->role['permissions'])){
                throw new PermissionNotHaveException();
            }
            // 添加分类
            $result = $this->categoryModel->addCategory($data);
            if (!$result){
                exit(json_encode(array('code'=>5004,'msg'=>'保存失败')));
            }
        } else {
            // 权限判断
            if (!in_array(parent::permission_category_update,$this->role['permissions'])){
                throw new PermissionNotHaveException();
            }
            // 编辑分类
            $up = $this->categoryModel->updateCategoryByCid($data,$cid);
            if (!$up){
                exit(json_encode(array('code'=>6002,'msg'=>'未作出任何修改')));
            }
        }
        exit(json_encode(array('code'=>2002,'msg'=>'保存成功')));
    }

    /**
     * 禁用分类
     * @throws ParamErrorException
     * @throws PermissionNotHaveException
     */
    public function category_disable(){
        // 权限判断
        if (!in_array(parent::permission_category_disable,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        // 接收分类cid
        $cid = (int)input('post.cid');
        if ($cid <= 0){
            throw new ParamErrorException();
        }
        $result = $this->categoryModel->disableCategoryByCid($cid);
        if (!$result){
            exit(json_encode(array('code'=>5005,'msg'=>'禁用失败')));
        }
        exit(json_encode(array('code'=>2003,'msg'=>'禁用成功')));
    }
}