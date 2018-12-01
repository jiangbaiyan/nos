<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/12/1
 * Time: 18:43
 */
namespace Exception;

use Throwable;

class RedisException extends \Exception{

    public function __construct($message = "Redis连接错误", $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}