<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/20
 * Time: 14:15
 */

namespace app\common\model;

use think\Model;

class Category extends Model {

    /**
     * 前台页面查询分类信息
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllCategories(){
        return Category::field('cid,name')->where('status','EQ',1)->select();
    }

    /**
     * 查询所有分类信息包括photo字段
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllCategoriesIncludePhoto(){
        return Category::field('cid,name,photo')->where('status','EQ',1)->select();
    }

    /**
     * 查询所有分类信息包括status字段
     * @return array
     */
    public function getAllCategoriesIncludeStatus(){
        return Category::column('cid,name,photo,status');
    }

    /**
     * 添加分类
     * @param $categoryInfo
     * @return false|int
     */
    public function addCategory($categoryInfo){
        return Category::data($categoryInfo)->save();
    }

    /**
     * 更新对应id的分类信息
     * @param $categoryInfo
     * @param $cid
     * @return Category
     */
    public function updateCategoryByCid($categoryInfo,$cid){
        return Category::where('cid','EQ',$cid)->update($categoryInfo);
    }

    /**
     * 禁用对应id的分类
     * @param $cid
     * @return Category
     */
    public function disableCategoryByCid($cid){
        return Category::where('cid','EQ',$cid)->update(['status' => 0]);
    }
}