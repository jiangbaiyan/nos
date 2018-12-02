<?php
/**
 * 短信验证码
 * Created by PhpStorm.
 * User: baiyanzzz
 * Date: 2018-11-28
 * Time: 15:37
 */


use Nos\Comm\Validator;
use Nos\Comm\Db;
use Nos\Comm\Redis;

class Common_TestController extends BaseController {

    public function checkParam(){
        $this->params['name'] = $this->get('name');
    }


    public function indexAction()
    {
        $page = $this->get('page', 1);
        $data = \Nos\Comm\Page::paginate(41, $page, 10);
        $this->output = $data;
        $this->responseSuccess();
//        $sql = 'insert into test values (?, ?)';
//        $res = Db::update($sql, array('2', 'baiyan'));

//        $sql2 = 'select * from test';
//        $this->output = Db::fetchAll($sql2);
//        $this->responseSuccess();
        $redis = new Comm\Redis();
        $redis->set("a",3);
        $a =$redis->get("a");
        echo $a;
        //$redis->connect();
    }

}