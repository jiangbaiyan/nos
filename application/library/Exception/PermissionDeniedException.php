<?php
/**
 * 权限不足异常
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 17:46
 */

namespace Exception;

use Throwable;

class PermissionDeniedException extends \Exception{

    public function __construct(string $message = "权限不足", int $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}