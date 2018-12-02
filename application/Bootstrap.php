<?php
/**
 * 框架启动
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/11/30
 * Time: 10:15
 */

use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Application;

class Bootstrap extends Bootstrap_Abstract
{

    /**
     * 加载所有配置文件
     */
    public function _initConfig(){
        $config = Application::app()->getConfig()->toArray();
        Yaf\Registry::set('config', $config);
    }


    /**
     * 初始化composer autoload
     * @param Dispatcher $dispatcher
     */
    public function _initLoader(Dispatcher $dispatcher)
    {
        //加载composer
        //include_once APP_PATH . '/vendor/autoload.php';
    }

}
