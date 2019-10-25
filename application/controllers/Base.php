<?php

use Yaf\Controller_Abstract;

abstract class BaseController extends Controller_Abstract
{

    /**
     * 当前登录用户
     * @var array $user
     */
    protected $user = [];

    /**
     * 是否需要用户鉴权
     * @var bool $auth
     */
    protected $auth = true;


    /**
     * 接口鉴权函数
     */
    protected function auth() {}

    /**
     * 业务逻辑
     */
    abstract protected function indexAction();

    /**
     * 初始化
     */
    private function init()
    {
        $this->auth && $this->user = $this->auth();
    }

}