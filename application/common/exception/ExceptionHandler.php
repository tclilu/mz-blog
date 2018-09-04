<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/8/27
 * Time: 17:41
 */

namespace app\common\exception;

use Exception;
use think\exception\Handle;

class ExceptionHandler extends Handle {
    private $code;
    private $msg;
    private $errorCode;
    public function render(Exception $e) {
        // 自定义异常
        if ($e instanceof BaseException) {
            // 如果是自定义异常，则控制http状态码，不需要记录日志
            // 因为这些通常是因为客户端传递参数错误或者是用户请求造成的异常
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            // 如果是服务器未处理的异常，将http状态码设置为500，并记录日志
            if (config('app_debug')){
                // 调试模式下显示TP默认异常页面
                return parent::render($e);
            }
            $this->code = 500;
            $this->msg = 'Sorry,System error';
            $this->errorCode = 66666;
        }
        $result = [
            'code' => $this->errorCode,
            'msg' => $this->msg
        ];
        return json($result,$this->code);
    }
}