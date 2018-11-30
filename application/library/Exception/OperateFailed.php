<?php
/**
 * 操作失败异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 11:33
 */

namespace Exception;

class OperateFailedException extends \Exception{

    public function __construct(string $message = "操作失败", int $code = 402, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}