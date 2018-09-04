<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/5
 * Time: 22:55
 */

namespace app\admin\controller;

use think\Controller;
use API\AdminBg\AdminBg;
use think\Request;
use app\admin\model\Admin;

class Login extends Controller {
    protected $admin = null;

    /**
     * 构造函数
     * Login constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        // 实例化Admin模型
        $this->admin = new Admin();
    }

    /**
     * 渲染登录页面
     * @return mixed
     */
    public function index(){
//        if (session('admin') != null){
//            header('Location: /tclilu');
//            exit();
//        }
        // 使用随机背景
        $src = AdminBg::myBackground();
        $this->assign('src',$src);
        return $this->fetch('login');
    }

    /**
     * 登录验证
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doLogin(){
        // 接收登录表单参数
        // 使用htmlentities函数转义HTML标签
        $username = htmlentities(trim(input('post.username')));
        $password = htmlentities(trim(input('post.password')));
        $verifyCode = htmlentities(trim(input('post.verify_code')));
        // 非空验证
        if ($username == ''){exit(json_encode(array('code'=>4001,'msg'=>'请输入用户名')));}
        if ($password == ''){exit(json_encode(array('code'=>4002,'msg'=>'请输入密码')));}
        if ($verifyCode == ''){exit(json_encode(array('code'=>4003,'msg'=>'请输入验证码')));}
        if (!captcha_check($verifyCode)){
            // 验证码不正确
            exit(json_encode(array('code'=>5001,'msg'=>'验证码错误')));
        }
        $admin = $this->admin->getAdminInfoByUsername($username);
        if (!$admin){
            exit(json_encode(array('code'=>4004,'msg'=>'用户名不存在')));
        }
        if (md5($username . $password) != $admin['password']){
            exit(json_encode(array('code'=>5002,'msg'=>'密码错误')));
        }
        if ($admin['status'] != '正常'){
            exit(json_encode(array('code'=>5003,'msg'=>'您已被禁用')));
        }
        // 登录成功 设置session 响应客户端
        session('admin',$admin);
        exit(json_encode(array('code'=>2001,'msg'=>'登录成功')));
    }

    /**
     * 退出
     */
    public function logout(){
        session('admin',null);
        exit(json_encode(array('code'=>2004,'msg'=>'退出成功')));
    }
}