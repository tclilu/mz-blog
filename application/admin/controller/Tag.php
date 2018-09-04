<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/23
 * Time: 19:13
 */

namespace app\admin\controller;

use think\Request;
use app\common\exception\PermissionNotHaveException;

class Tag extends BaseAdmin {
    protected $tagModel = null;
    public function __construct(Request $request = null){
        parent::__construct($request);
        $this->tagModel = new \app\common\model\Tag();
    }

    /**
     * 渲染标签列表页面
     * @return mixed
     * @throws PermissionNotHaveException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tag_list(){
        // 权限判断
        if (!in_array(parent::permission_tags,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        $tags = $this->tagModel->getAllTags();
        $this->assign(['empty' => '<tr><td colspan="3">~暂无标签~</td></tr>','tags' => $tags]);
        return $this->fetch('tags');
    }

    public function tag_save(){
        // 接收表单参数
        $tid = (int)input('post.tid');
        $tname = htmlentities(trim(input('post.tname')));
        if ($tname == ''){exit(json_encode(array('code'=>4013,'msg'=>'标签名称不能为空')));}
        if ($tid == 0){
            // 权限判断
            if (!in_array(parent::permission_tag_add,$this->role['permissions'])){
                throw new PermissionNotHaveException();
            }
            // 添加标签
            $result = $this->tagModel->addTag(['tname' => $tname]);
            if (!$result){
                exit(json_encode(array('code'=>5004,'msg'=>'保存失败')));
            }
        } else {
            // 权限判断
            if (!in_array(parent::permission_tag_update,$this->role['permissions'])){
                throw new PermissionNotHaveException();
            }
            // 编辑标签
            $up = $this->tagModel->updateTagByTid(['tname' => $tname],$tid);
            if (!$up){
                exit(json_encode(array('code'=>6002,'msg'=>'未作出任何修改')));
            }
        }
        exit(json_encode(array('code'=>2002,'msg'=>'保存成功')));
    }
}