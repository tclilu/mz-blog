<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/6
 * Time: 18:36
 */
namespace API\AdminBg;

class AdminBg {
    // 图片主题
    private static $themeArray = array('xiaoqingxin'=>25,'yangmi'=>4,'zhaoliyin'=>35);

    /**
     * 构造函数
     * AdminBg constructor.
     * @param $customTheme 传入背景主题,参数必须是一个数组,键名为主题名,键值为主题图片数量
     */
    public function __construct($customTheme = null){
        if ($customTheme != null){
            if (!is_array($customTheme)){
                exit('参数错误');
            } else {
                self::$themeArray = $customTheme;
            }
        }
    }

    /**
     * 获取不指定主题的随机背景相对路径
     * @return string
     */
    public static function myBackground(){
        return self::get_random_background();
    }

    /**
     * 获取对应主题的随机背景相对路径
     * @param null $theme
     * @return string
     */
    public function myBackgroundByTheme($theme = null){
        return self::get_random_background($theme);
    }

    /**
     * 获取随机主题背景图路径
     * @param string $theme 主题名称
     * @return string 相对路径
     */
    private static function get_random_background($theme = ''){
        if ($theme == ''){
            // 随机一个主题
            $theme = key(array_slice(self::$themeArray,mt_rand(0,count(self::$themeArray)-1),1));
        }
        // 主题不存在
        if (!array_key_exists($theme,self::$themeArray)){
            return $theme . ' 主题不存在';
        }
        // 获取主题图片数量
        $sum = self::$themeArray[$theme];
        // 产生随机数
        $bg = mt_rand(1,$sum);
        // 返回背景图的相对路径
        return $theme . '/' . $bg . '.jpg';
    }

    /**
     * 批量重命名图片
     */
    private function renameImages(){
        $dir = "F:\PHP\myphp_www\PHPTutorial\WWW\\tp5_blog\public\static\images\xiaoqingxin";
        $result = array();
        $i = 1;
        $handle = opendir($dir);//读资源
        if ($handle){
            while (($file = readdir($handle)) !== false ){
                if ($file != '.' && $file != '..'){
                    $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($cur_path )){//判断是否为目录，递归读取文件
                        $result['dir'][$cur_path] = read_all_dir($cur_path );
                    }else{
                        $result['file'][] = $cur_path;
                        rename($cur_path,'F:\PHP\myphp_www\PHPTutorial\WWW\tp5_blog\public\static\images\xiaoqingxin\\' . $i . '.jpg');
                        $i++;
                    }
                }
            }
            closedir($handle);
        }
        dump($result);
    }
}