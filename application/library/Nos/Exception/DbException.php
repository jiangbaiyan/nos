<?php
/**
 * 数据库底层异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-30
 * Time: 16:30
 *
 */

namespace Nos\Exception;

use Nos\Http\Response;
use Throwable;

class DbException extends \Exception{

    public function __construct($message = Response::MSG_Db_ERROR, $code = Response::CODE_Db_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
