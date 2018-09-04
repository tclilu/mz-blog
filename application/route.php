<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // ==================
    // API
    // 发送邮件
    // 'sendMail' => ['API/sendMail/sendMail',['method' => 'get']],
    // 文件上传
    'upload/[:dir]' => ['common/Upload/upload',['method' => 'post']],
    // ==================
    // ==================
    // 前台
    // 前台首页
    '/' => 'index/Index/index',
    'api/articles/[:currentPage]' => 'index/Index/getPageArticles',
    'article/[:id]' => 'index/Index/getArticleDetail',
    'about' => 'index/Index/about',
    // ==================
    // ==================
    // 后台
    // 后台首页
    'tclilu' => 'admin/Index/index',
    // 后台登录页面
    'login' => 'admin/Login/index',
    // 登录逻辑判断
    'doLogin' => ['admin/Login/doLogin',['method' => 'post']],
    // 管理员列表页面
    'admins' => 'admin/Admin/admin_list',
    // 添加或编辑管理员信息页面
    'admin_edit/[:id]' => ['admin/Admin/admin_edit',['method' => 'get']],
    // 保存管理员信息方法
    'admin_save' => 'admin/Admin/save',
    // 禁用管理员方法
    'admin_disable' => 'admin/Admin/admin_disable',

    // 菜单列表页面
    'menus/[:pid]' => 'admin/Menu/menu_list',
    // 保存管理员信息方法
    'menu_save' => 'admin/Menu/menu_save',

    // 角色列表页面
    'roles' => 'admin/Role/role_list',
    // 添加或编辑角色信息页面
    'role_edit/[:rid]' => ['admin/Role/role_edit',['method' => 'get']],
    // 保存角色信息方法
    'role_save' => 'admin/Role/role_save',
    // 禁用角色方法
    'role_disable' => 'admin/Role/role_disable',

    // 后台退出登录
    'logout' => 'admin/Login/logout',

    // 文章分类列表页面
    'categories' => 'admin/Category/category_list',
    // 保存分类信息方法
    'category_save' => 'admin/Category/category_save',
    // 禁用分类方法
    'category_disable' => 'admin/Category/category_disable',

    // 标签列表页面
    'tags' => 'admin/Tag/tag_list',
    // 保存标签信息方法
    'tag_save' => 'admin/Tag/tag_save',

    // 添加|编辑博文页面
    'article_edit/[:id]' => 'admin/Article/article_edit',
    // 保存文章方法
    'article_save' => 'admin/Article/article_save',
    // 博文列表页面
    'articles' => 'admin/Article/article_list',
    // 博文列表信息接口
    'api/articles' => 'admin/Article/articles_data',
    // 软删除博文接口地址
    'softDelete/[:id]' => 'admin/Article/article_soft_delete',
    // ==================
];
