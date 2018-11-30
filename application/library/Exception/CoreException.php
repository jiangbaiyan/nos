<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-30
 * Time: 16:30
 */

namespace Exception;

use Throwable;

class CoreException extends \Exception{

    public function __construct(string $message = "底层错误", int $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}