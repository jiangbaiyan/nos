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
use Nos\Comm\Validator;

class Common_TestController extends BaseController {

    /**
     * 是否需要登录授权
     * @var bool
     */
    public $needAuth = false;

    /*
     * 当前登录用户
     */
    public $user;

    /*
     * 模型
     */
    private $testModel;

    /*
     * 参数校验
     */
    public function checkParam(){

    }

    /**
     * 加载模型
     */
    public function loadModel()
    {
        $this->testModel = new \Common\TestModel();
    }

    /**
     * 业务逻辑
     */
    public function indexAction()
    {
        $this->testModel->getData();
        Response::apiSuccess($a);
    }

}