<?php
/**
 * 异常处理类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 11:21
 */

class ErrorController extends Comm_Control{

    public function errorAction($exception){
        $code = $exception->getCode();
        $msg = $exception->getMessage();
        $this->response($code,$msg);
        Comm_Log::fatal();//写日志
    }

}