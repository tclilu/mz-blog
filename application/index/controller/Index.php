<?php
namespace app\index\controller;

use app\common\exception\ParamErrorException;
use app\common\model\Article;
use app\common\model\Tag;

class Index extends BaseIndex {

    public function index() {
        return view('index');
    }

    /**
     * 获取分页文章
     * @throws ParamErrorException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPageArticles(){
        // 接收路由参数,当前页
        $currentPage = (int)$this->request->route('currentPage');
        if ($currentPage <= 0){
            throw new ParamErrorException();
        }
        // 分页查询博文
        $pageArticles = Article::getPageArticles(5,$currentPage);
        // 总页数
        $pages = ceil(Article::getArticlesCount()/5);
        // 关联查询分类名称和作者姓名
        foreach ($pageArticles as $key => $pageArticle) {
            $pageArticles[$key]->category_id = $pageArticles[$key]->category->name;
            $pageArticles[$key]->admin_id = $pageArticles[$key]->admin->true_name;
        }
        echo json_encode(['code' => 200,'data' => $pageArticles,'pages' => $pages]);
    }

    /**
     * 根据id查询文章详细内容
     * @return mixed
     * @throws ParamErrorException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleDetail(){
        // 接收路由参数 博文id
        $id = $this->request->route('id');
//        if ($id <= 0){
//            throw new ParamErrorException();
//        }
        $article = Article::getArticleById($id);
        // 根据id查询不到文章则抛出参数错误异常
        if (!$article){
            throw new ParamErrorException();
        }
        // 关联查询标签
        $article['tags'] = $article->tags()->where('tid','IN',$article['tags'])->select();
        $this->assign('article',$article);
        return $this->fetch('detail');
    }

    /**
     * 关于本站
     * @return mixed
     */
    public function about(){
        return $this->fetch('about');
    }
}