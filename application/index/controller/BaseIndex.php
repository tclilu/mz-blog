<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/17
 * Time: 13:20
 */

namespace app\index\controller;

use app\common\model\Category;
use app\common\model\Tag;
use think\Controller;
use think\Request;

class BaseIndex extends Controller {

    public function __construct(Request $request = null) {
        parent::__construct($request);
        // 加载公共区域数据
        // 1、加载分类
        $data['categories'] = Category::getAllCategories();
        // 2、加载标签云
        $data['tags'] = Tag::getAllTags();
        $data['color'] = array(
            'layui-bg-purple',
            'layui-bg-darkcyan',
            'layui-bg-red',
            'layui-bg-orange',
            'layui-bg-green',
            'layui-bg-cyan',
            'layui-bg-blue',
            'layui-bg-black',
            'layui-bg-gray'
        );
        $this->assign('data',$data);
    }
}