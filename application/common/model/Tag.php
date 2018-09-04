<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/23
 * Time: 19:12
 */

namespace app\common\model;

use think\Model;

class Tag extends Model {
    /**
     * 查询所有标签（静态）
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllTags(){
        return Tag::select();
    }

    /**
     * 查询所有标签（非静态）
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllTagsNotStatic(){
        return Tag::select();
    }

    /**
     * 添加标签
     * @param $tagInfo
     * @return false|int
     */
    public function addTag($tagInfo){
        return Tag::data($tagInfo)->save();
    }

    /**
     * 更新标签
     * @param $tagInfo
     * @param $tid
     * @return Tag
     */
    public function updateTagByTid($tagInfo,$tid){
        return Tag::where('tid','EQ',$tid)->update($tagInfo);
    }

    /**
     * 根据id查询标签
     * @param $tid
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getTagByTid($tid){
        return Tag::where('tid','EQ',$tid)->find();
    }
}