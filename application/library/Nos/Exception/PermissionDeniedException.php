<?php
/**
 * 权限不足异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 17:46
 */

namespace Nos\Exception;

use Nos\Http\Response;
use Throwable;

class PermissionDeniedException extends \Exception{

    public function __construct($message = Response::MSG_PERMISSION_DENIED, $code = Response::CODE_PERMISSION_DENIED, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}