<?php
/**
 * 资源不存在异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 17:45
 */

namespace Nos\Exception;

use Nos\Http\Response;
use Throwable;

class ResourceNotFoundException extends \Exception
{
    public function __construct($message = Response::MSG_RESOURCE_NOT_FOUND, $code = Response::CODE_RESOURCE_NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}