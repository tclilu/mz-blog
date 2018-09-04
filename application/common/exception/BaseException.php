<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/27
 * Time: 17:55
 */

namespace app\common\exception;

use think\Exception;

class BaseException extends Exception {
    public $code = 400;
    public $msg = 'My Custom error';
    public $errorCode = 10000;
    /**
     * 构造函数，接收一个关联数组
     * @param array $params 关联数组只应包含code、msg和errorCode，且不应该是空值
     */
    public function __construct($params = []) {
        if (!is_array($params)) {
            return;
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }
}