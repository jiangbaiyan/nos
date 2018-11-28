<?php
/**
 * 资源不存在异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 17:45
 */

class Exception_ResourceNotFound extends \Exception{

    public function __construct(string $message = "资源未找到", int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}