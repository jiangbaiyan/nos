<?php
/**
 * 参数异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 13:51
 */

class Exception_ParamValidateFailed extends \Exception{

    public function __construct(string $message = "参数不全或校验失败", int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}