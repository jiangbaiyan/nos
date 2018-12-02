<?php
/**
 * 框架底层异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-30
 * Time: 16:30
 */

namespace Nos\Exception;

use Nos\Http\Response;
use Throwable;

class CoreException extends \Exception{

    public function __construct($message = Response::MSG_CORE_ERROR, $code = Response::CODE_CORE_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}