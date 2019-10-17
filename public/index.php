<?php
/**
 * 入口文件
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-26
 * Time: 19:29
 */

use Yaf\Application;

// 定义基本变量
define('APP_PATH', dirname(__DIR__));
// 设置时区
ini_set('date.timezone','Asia/Shanghai');
// 初始化yaf_application
$app = new Application(APP_PATH . '/config/application.ini');
// composer自动加载
include_once APP_PATH . '/vendor/autoload.php';
// 获取yaf请求分发器
$dispatcher = $app->getDispatcher();
// 捕获异常
$dispatcher->catchException('true');
// 关闭视图
$dispatcher->disableView();
// 加载框架
$app->run();