<?php

use Yaf\Controller_Abstract;

abstract class BaseController extends Controller_Abstract
{
    /**
     * 业务逻辑
     */
    abstract protected function indexAction();
}