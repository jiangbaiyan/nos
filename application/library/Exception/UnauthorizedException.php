<?php
/**
 * 未授权异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 17:43
 */

namespace Exception;

use Throwable;

class UnauthorizedException extends \Exception {

    public function __construct($message = "未授权", $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}