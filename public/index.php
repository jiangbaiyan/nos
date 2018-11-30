<?php
/**
 * 入口文件
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-26
 * Time: 19:29
 */

define('APP_PATH', dirname(__DIR__));
define('ROOT_PATH',  APP_PATH . '/application');
$app = new Yaf_Application(APP_PATH . '/config/application.ini');
$app->getDispatcher()->catchException('true');
$app->getDispatcher()->disableView();
$app->run();