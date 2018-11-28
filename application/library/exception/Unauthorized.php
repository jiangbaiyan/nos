<?php
/**
 * 未授权异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 17:43
 */

class Exception_Unauthorized extends \Exception {

    public function __construct(string $message = "未授权", int $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}