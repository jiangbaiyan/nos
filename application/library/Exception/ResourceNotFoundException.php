<?php
/**
 * 资源不存在异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 17:45
 */

namespace Exception;

use Throwable;

class ResourceNotFoundException extends \Exception{

    public function __construct($message = "资源未找到", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}