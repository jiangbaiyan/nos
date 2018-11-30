<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/11/30
 * Time: 10:15
 */

use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Application;
use Comm\Db;

class Bootstrap extends Bootstrap_Abstract
{

    private $config = array();

    /**
     * 加载所有配置文件
     */
    public function _initConfig(){
        $this->config = Application::app()->getConfig()->toArray();
    }

    /**
     * 初始化DB
     * @throws \Exception\CoreException
     */
    public function _initDb(){
        Yaf\Registry::set('db', new Db($this->config));
    }

    /**
     * 初始化redis
     */
    public function _initRedis(){

    }

    /**
     * 初始化composer autoload
     * @param Dispatcher $dispatcher
     */
    public function _initLoader(Dispatcher $dispatcher)
    {
        //加载composer
        //include_once ROOT_PATH . '/vendor/autoload.php';
    }

}
