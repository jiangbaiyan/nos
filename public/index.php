<?php
/**
 * 入口文件
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-26
 * Time: 19:29
 */

use Yaf\Application;

define('APP_PATH', dirname(__DIR__));
define('ROOT_PATH',  APP_PATH . '/application');
$app = new Application(APP_PATH . '/config/application.ini');
include_once APP_PATH . '/vendor/autoload.php';
$dispatcher = $app->getDispatcher();
$dispatcher->registerPlugin(new RoutePlugin());
$dispatcher->catchException('true');
$dispatcher->disableView();
$app->run();