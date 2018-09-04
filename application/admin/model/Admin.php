<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/18
 * Time: 19:24
 */

namespace app\admin\model;

use think\Model;

class Admin extends Model {
    // 添加时间输出格式
    protected $dateFormat = 'Y年m月d日 H:i:s';
    protected $type = ['add_time' => 'timestamp'];
    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    // 定义添加时间字段名
    protected $createTime = 'add_time';
    // 关闭更新时间自动写入
    protected $updateTime = false;

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
     * 一对一关联查询
     * @return \think\model\relation\HasOne
     */
    public function adminRole(){
        // 第一个参数：被关联的模型名称
        // 第二个参数：要关联表的关联字段
        // 第三个参数：这个数据表的关联字段
        return $this->hasOne('AdminRole','rid','rid')->field('name');
    }

    /**
     * 获取管理员信息
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAdmins(){
        return Admin::field('id,username,true_name,add_time,rid,status')->select();
    }

    /**
     * 根据id查询管理员
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAdminById($id){
        return Admin::field('id,username,true_name,rid,status')->where(['id' => $id])->find();
    }

    /**
     * 根据用户名查询用户
     * 返回用户名
     * @param $username
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAdminUsernameByUsername($username){
        return Admin::field('username')->where(['username' => $username])->find();
    }

    /**
     * 根据用户名查询用户完整信息
     * @param $username
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAdminInfoByUsername($username){
        return Admin::where('username','EQ',$username)->find();
    }
    /**
     * 新增管理员
     * @param $adminInfo
     * @return false|int
     */
    public function addAdmin($adminInfo){
        return Admin::data($adminInfo)->save();
    }

    /**
     * 根据管理员id修改管理员信息
     * @param $adminInfo
     * @param $id
     * @return Admin
     */
    public function updateAdminById($adminInfo,$id){
        return Admin::where('id','EQ',$id)->update($adminInfo);
    }

    /**
     * 根据id禁用管理员
     * @param $id
     * @return Admin
     */
    public function disableAdminById($id){
        return Admin::where('id','EQ',$id)->update(['status' => 0]);
    }
}