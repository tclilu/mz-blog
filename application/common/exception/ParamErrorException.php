<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/27
 * Time: 21:40
 */

namespace app\common\exception;


class ParamErrorException extends BaseException {
    public $code = 404;
    public $msg = '参数错误';
    public $errorCode = 50000;
}