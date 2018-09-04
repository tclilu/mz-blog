<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/21
 * Time: 14:27
 */

namespace app\admin\controller;

use app\common\exception\ParamErrorException;
use app\common\exception\PermissionNotHaveException;
use think\Request;

class Article extends BaseAdmin {
    // 文章模型
    protected $articleModel = null;
    // 所有分类和标签
    protected $categories;
    protected $tags;

    /**
     * Article constructor.
     * @param Request|null $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function __construct(Request $request = null){
        parent::__construct($request);
        $this->articleModel = new \app\common\model\Article();
        // 查询所有分类和标签
        $this->categories = \app\common\model\Category::getAllCategoriesIncludePhoto();
        $this->tags = \app\common\model\Tag::getAllTags();
    }

    /**
     * 渲染添加|编辑博文页面
     * @return mixed
     * @throws ParamErrorException
     * @throws PermissionNotHaveException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function article_edit(){
        // 权限判断
        if (!in_array(parent::permission_article_edit_html,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
		// 接收路由参数
        $id = $this->request->route('id');
		if ($id == ''){
			// 添加
			// 发送下拉列表数据到前端，渲染页面
			$this->assign(['article' => null,'categories' => $this->categories,'tags' => $this->tags]);
			return $this->fetch('article_edit');
		} else {
			// 编辑
			// 查询该id对应文章信息
			$article= \app\common\model\Article::getArticleById($id);
			if ($article == null){
				throw new ParamErrorException();
			}
			// 发送数据到前端
			$this->assign(['article' => $article,'categories' => $this->categories,'tags' => $this->tags]);
			return $this->fetch('article_edit');
		}
    }

    /**
     * 保存博文信息
     */
    public function article_save(){
        // 接收表单传递的文章信息
        $id = input('post.id');
        $data['title'] = htmlentities(trim(input('post.title')));
        $data['category_id'] = (int)input('post.category_id');
        $data['cover_photo'] = htmlentities(trim(input('post.cover_photo')));
        $data['summary'] = htmlentities(trim(input('post.summary')));
        $data['content'] = htmlentities(trim(input('post.content')));
        $tags = input('post.tags/a');
        if ($tags == ''){
            $data['tags'] = null;
        } else {
            $data_tags = array();
            foreach ($tags as $tag) {
                $data_tags[] = (int)$tag;
            }
            $data['tags'] = json_encode($data_tags);
        }
        // 非空校验
        if ($data['title'] == ''){
            exit(json_encode(array('code' => 4015,'msg' => '文章标题不能为空')));
        }
        if ($data['category_id'] == 0){
            exit(json_encode(array('code' => 4016,'msg' => '分类必须选择')));
        }
        if ($data['content'] == ''){
            exit(json_encode(array('code' => 4017,'msg' => '文章内容必须填写')));
        }
        if ($data['summary'] == ''){
            // 取出HTML标签和转义字符及空格换行
            $content = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", strip_tags(html_entity_decode($data['content'])));
            // 计算正文的长度
            $len = mb_strlen($content,'utf-8');
            // 截取文章摘要
            if ($len > 800) {
                $data['summary'] = mb_substr($content,0,150,'utf-8');
            } else if ($len > 500 && $len <= 800){
                $data['summary'] = mb_substr($content,0,100,'utf-8');
            } else if ($len > 300 && $len <= 500){
                $data['summary'] = mb_substr($content,0,80,'utf-8');
            } else {
                if ($len > 50 && $len <= 300){
                    $data['summary'] = mb_substr($content,0,50,'utf-8');
                } else {
                    $data['summary'] = $content;
                }
            }
        }
        // 设置默认信息
        $data['status'] = 'published'; // 默认已发布
        $data['admin_id'] = $this->login_admin['id']; // 发布者id
        if ($id < 0){
            throw new ParamErrorException();
        }
        if ($id == 0){
            // 权限判断
            if (!in_array(parent::permission_article_add,$this->role['permissions'])){
                throw new PermissionNotHaveException();
            }
            // 等于0，表示新增
            $result = $this->articleModel->addArticle($data);
        } else {
            // 权限判断
            if (!in_array(parent::permission_article_update,$this->role['permissions'])){
                throw new PermissionNotHaveException();
            }
            // 大于0，表示更新
            $result = $this->articleModel->updateArticleById($data,$id);
        }
        if (!$result){
            exit(json_encode(array('code'=>5004,'msg'=>'保存失败')));
        }
        exit(json_encode(array('code'=>2002,'msg'=>'保存成功')));
    }

    /**
     * 渲染博文列表页面视图
     * @return mixed
     * @throws PermissionNotHaveException
     */
    public function article_list(){
        // 权限判断
        if (!in_array(parent::permission_articles_html,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        // 聚合查询当前登录用户的博文总数
        $article_count = $this->articleModel->getArticleCountByAdminId($this->login_admin['id']);
        $this->assign('article_count',$article_count);
        return $this->fetch('articles');
    }

    /**
     * 分页查询当前登录用户博文数据接口
     * 超级管理员可查看全部博文
     * 其余用户只能查看自己发布的博文
     */
    public function articles_data(){
        // 权限判断
        if (!in_array(parent::permission_article_list,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        // 获取分页参数
        $currentPage = (int)input('get.page'); // 当前第几页
        $pageCount = (int)input('get.limit'); // 每页数量
        // 分页查询博文数据
        $pageArticles = $this->articleModel->getPageArticlesByAdminId($pageCount,$currentPage,$this->login_admin['id']);
        // 关联查询分类名称和作者姓名
        foreach ($pageArticles as $key => $pageArticle) {
            $pageArticles[$key]->category_id = $pageArticles[$key]->category->name;
            $pageArticles[$key]->admin_id = $pageArticles[$key]->admin->true_name;
        }
        echo json_encode(['code' => 0, 'msg' => '加载成功' ,'count' => $pageCount,'data' => $pageArticles]);
    }

    /**
     * 加入回收站
     * 使用模型的软删除时，使用公共模型Article实现软删除失败
     * 根据数据库状态字段status进行软删除
     * @throws ParamErrorException
     * @throws PermissionNotHaveException
     */
    public function article_soft_delete(){
        // 权限判断
        if (!in_array(parent::permission_article_disable,$this->role['permissions'])){
            throw new PermissionNotHaveException();
        }
        // 接收路由参数id
        $id = $this->request->route('id');
        if ($id <= 0){
            throw new ParamErrorException();
        }
        $up = $this->articleModel->softDeleteArticleById($id);
        if (!$up){
            echo json_encode(['code' => 5007, 'msg' => '删除失败']);
        } else {
            echo json_encode(['code' => 2006, 'msg' => '删除成功']);
        }
    }
}