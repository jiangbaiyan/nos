<?php
/**
 * 参数异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 13:51
 */

namespace Nos\Exception;

use Nos\Http\Response;
use Throwable;

class ParamValidateFailedException extends \Exception{

    public function __construct($message = Response::MSG_PARAM_ERROR, $code = Response::CODE_PARAM_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}