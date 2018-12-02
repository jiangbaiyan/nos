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
use Nos\Http\Request;
use Nos\Http\Response;
use Nos\Comm\Page;

class Common_TestController extends BaseController {

    public function checkParam(){
        $this->params['name'] = $this->get('name');
    }


    public function indexAction()
    {
        $page = Request::param('page');
        $data = Page::paginate(41, $page, 10);
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