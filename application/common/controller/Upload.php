<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/24
 * Time: 13:48
 */

namespace app\common\controller;

use think\Controller;

class Upload extends Controller {

    /**
     * 文件上传接口
     */
    public function upload(){
        // 获取路由参数dir
        $dir = $this->request->route('dir');
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('upload_img');
        if ($file == null){
            exit(json_encode(array('code' => 4014,'msg' => '没有图片上传')));
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        // 验证不通过，则move方法返回false。
        // size 单位为字节Byte
        $info = $file->validate(['size' => (1024 * 3072),'ext' => 'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads/' . $dir);
        // if (!in_array($info->getExtension(),['jpg','jpeg','gif','png'])){
            // exit(json_encode(array('code' => 5006,'msg' => '不支持的文件格式')));
        // }
        if ($info) {
            // 成功上传后 获取上传信息
            exit(json_encode(['code' => 2005,'msg' => $info->getSaveName()]));
        } else {
            // 上传失败获取错误信息
            exit(json_encode(['code' => 5006,'msg' => $file->getError()]));
        }
    }
}