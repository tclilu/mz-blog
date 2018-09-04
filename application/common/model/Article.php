<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/21
 * Time: 14:28
 */

namespace app\common\model;

use think\Model;

class Article extends Model {
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 自定义发布时间字段
    protected $createTime = 'publish_time';
    // tags字段自动json格式转换 貌似失效？？
    protected $type = ['tags' => 'json'];

    /**
     * 一对一关联查询分类名称
     * @return \think\model\relation\HasOne
     */
    public function category(){
        return $this->hasOne('Category','cid','category_id')->field('name');
    }

    /**
     * 一对一关联查询作者姓名
     * @return \think\model\relation\HasOne
     */
    public function admin(){
        return $this->hasOne('app\admin\model\Admin','id','admin_id')->field('true_name,avatar');
    }

    /**
     * 一对多关联查询标签
     * @return \think\model\relation\HasMany
     */
    public function tags(){
        return $this->hasMany('Tag','tid','tags')->field('tname');
    }
    /**
     * 添加博文
     * @param $articleInfo
     * @return false|int
     */
    public function addArticle($articleInfo){
        return Article::data($articleInfo)->save();
    }

    /**
     * 根据id更新博文信息
     * @param $articleInfo
     * @param $id
     * @return Article
     */
    public function updateArticleById($articleInfo,$id){
        return Article::where('id','EQ',$id)->update($articleInfo);
    }

    /**
     * 查询对应id管理员所写文章数量
     * @param $admin_id
     * @return int|string
     */
    public function getArticleCountByAdminId($admin_id){
        return Article::where('admin_id','EQ',$admin_id)->count();
    }

    /**
     * 分页查询对应id管理员博文信息
     * 超级管理员查询所有博文
     * @param $pageCount
     * @param $currentPage
     * @param $admin_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPageArticlesByAdminId($pageCount,$currentPage,$admin_id){
        if ($admin_id == 1){
            // 超级管理员，查看所有人发布的所有文章
            return Article::field('id,title,update_time,status,read_count,love_count,admin_id,category_id')->limit($pageCount)->page($currentPage)->select();
        } else {
            // 其它人只能看到自己发布的文章
            return Article::field('id,title,update_time,status,read_count,love_count,admin_id,category_id')->limit($pageCount)->page($currentPage)->where('admin_id','EQ',$admin_id)->select();
        }
    }

    /**
     * 根据id软删除博文
     * 设置status字段为trashed
     * @param $id
     * @return Article
     */
    public function softDeleteArticleById($id){
        return Article::where('id','EQ',$id)->update(['status' => 'trashed']);
    }

    /**
     * 根据id查询文章信息(静态)
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getArticleById($id){
        return Article::where('id','EQ',$id)->find();
    }

    /**
     * 分页查询已发布的文章
     * @param $pageCount 每页数量
     * @param $currentPage 当前页
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getPageArticles($pageCount,$currentPage){
        return Article::field('id,title,cover_photo,summary,update_time,read_count,love_count,admin_id,category_id,tags')->limit($pageCount)->page($currentPage)->where('status','EQ','published')->select();
    }

    /**
     * 查询已发布文章总数
     * @return int|string
     */
    public static function getArticlesCount(){
        return Article::where('status','EQ','published')->count('1');
    }
}