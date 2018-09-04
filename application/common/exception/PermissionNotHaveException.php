<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/27
 * Time: 18:25
 */

namespace app\common\exception;


class PermissionNotHaveException extends BaseException {
    public $code = 404;
    public $msg = '您没有权限进行该项操作';
    public $errorCode = 233333;
}