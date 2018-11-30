<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/11/30
 * Time: 10:15
 */

use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;

class Bootstrap extends Bootstrap_Abstract
{

    public function _initLoader(Dispatcher $dispatcher)
    {
        //加载composer
        //include_once ROOT_PATH . '/vendor/autoload.php';
    }

}
