<?php
/**
 * 操作失败异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 11:33
 */

namespace Nos\Exception;

use Nos\Http\Response;
use Throwable;

class OperateFailedException extends \Exception
{
    public function __construct($message = Response::MSG_OPERATE_FAILED, $code = Response::CODE_OPERATE_FAILED, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}