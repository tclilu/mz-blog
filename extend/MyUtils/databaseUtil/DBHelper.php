<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/6
 * Time: 22:35
 */

namespace MyUtils\databaseUtil;
use think\Db;

/**
 * v 1.1.0版本以启用该工具类
 * 封装Tp5框架Db类常用操作的辅助工具类
 * Class DBHelper
 * @package MyUtils\databaseUtil
 */
class DBHelper {

    /**
     * 指定查询表名
     * @param $table 数据库表名
     * @return $this 当前对象 支持链式编程
     */
    public function table($table){
        $this->where = array();
        $this->field = '*';
        $this->table = $table;
        return $this;
    }

    /**
     * 指定查询字段
     * @param string $field 默认为*,查询所有
     * @return $this 当前对象 支持链式编程
     */
    public function field($field = '*'){
        $this->field = $field;
        return $this;
    }

    /**
     * 指定查询条件
     * @param array $where 查询where条件,默认为空数组
     * @return $this 当前对象 支持链式编程
     */
    public function where($where = array()){
        $this->where = $where;
        return $this;
    }

    /**
     * 查询一条记录,如果查询结果为空则返回false
     * @return array|bool|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function item(){
        $item = Db::name($this->table)->field($this->field)->where($this->where)->find();
        return $item ? $item : false;
    }

    /**
     * 查询所有记录,如果查询结果为空则返回false
     * @return bool|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists(){
        $lists = Db::name($this->table)->field($this->field)->where($this->where)->select();
        return $lists ? $lists : false;
    }

    /**
     * 查询所有记录,如果查询结果为空返回false
     * 不为空返回自定义数据表字段索引的数组
     * @param $index
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function customIndexList($index){
        $lists = $this->lists();
        if (!$lists) {return false;}
        $result = array();
        foreach ($lists as $value) {
            $result[$value[$index]] = $value;
        }
        return $result;
    }

    /**
     * 插入一条记录
     * @param $data
     * @return int|string
     */
    public function insert($data){
        return Db::name($this->table)->insert($data);
    }

    /**
     * 更新一条记录
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function update($data){
        return Db::name($this->table)->where($this->where)->update($data);
    }

    /**
     * 删除一条记录
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete(){
        return Db::name($this->table)->where($this->where)->delete();
    }

    /**
     * 查看执行的sql语句 直接返回SQL而不是执行查询
     * @param $CURD
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function fetchSql($CURD){
        return Db::name($this->table)->field($this->field)->where($this->where)->fetchSql(true)->$CURD();
    }
}