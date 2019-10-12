<?php
/**
 * 异常处理类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 11:21
 */

use Nos\Comm\Log;
use Nos\Http\Request;
use Nos\Http\Response;
use Yaf\Controller_Abstract;

class ErrorController extends Controller_Abstract
{

    /**
     * 异常处理入口
     * @param $exception
     */
    public function errorAction($exception)
    {
        // 获取异常代码
        $code = $exception->getCode();
        // 获取异常信息
        $msg = $exception->getMessage();
        // 获取异常文件
        $file = $exception->getFile();
        // 获取异常文件行数
        $line = $exception->getLine();
        // 记录异常日志
        Log::error(json_encode([
            'status' => $code,
            'msg'    => $msg,
            'file'   => $file,
            'line'   => $line,
            'url'    => $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'params' => Request::all()
        ]));
        // 输出异常响应
        Response::apiResponse($code, $msg);
    }
}