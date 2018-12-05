<?php
/**
 * 控制器示例
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 15:37
 */


use Nos\Http\Request;
use Nos\Http\Response;

class Common_TestController extends BaseController {

    public $needAuth = false;

    private $testModel;

    public function checkParam(){
        $this->params['phone'] = Request::param('phone');
    }

    public function loadModel()
    {
        $this->testModel = new \Common\TestModel();
    }

    public function indexAction()
    {
        $data = $this->testModel->getData();
        $this->output = $data;
        Response::apiSuccess($this->output);
    }

}