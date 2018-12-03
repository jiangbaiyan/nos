<?php
/**
 * 短信验证码
 * Created by PhpStorm.
 * User: baiyanzzz
 * Date: 2018-11-28
 * Time: 15:37
 */


use Nos\Http\Request;
use Nos\Http\Response;

class Common_TestController extends BaseController {

    public function checkParam(){
        $this->params['name'] = Request::param('name');
    }


    public function indexAction()
    {
        $testModel = new Common\TestModel();
        $data = $testModel->getData();
        $this->output = $data;
        Response::apiResponseSuccess($this->output);

//        $sql = 'insert into test values (?, ?)';
//        $res = Db::update($sql, array('2', 'baiyan'));

//        $sql2 = 'select * from test';
//        $this->output = Db::fetchAll($sql2);
//        $this->responseSuccess();
//        $redis = new Comm\Redis();
//        $redis->set("a",3);
//        $a =$redis->get("a");
//        echo $a;
        //$redis->connect();
    }

}