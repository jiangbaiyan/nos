<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/12/1
 * Time: 14:56
 */
class IndexController extends   Yaf\Controller_Abstract{
    function indexAction(){
        $redis = new Redis();
        $redis->connect("127.0.0.1",6379);
        $redis->set("value",3);
        $redis->get("value");
    }
}