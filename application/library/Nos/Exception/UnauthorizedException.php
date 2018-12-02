<?php
/**
 * 未授权异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 17:43
 */

namespace Nos\Exception;

use Nos\Http\Response;
use Throwable;

class UnauthorizedException extends \Exception {

    public function __construct($message = Response::MSG_UNAUTHORIZED, $code = Response::CODE_UNAUTHORIZED, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}