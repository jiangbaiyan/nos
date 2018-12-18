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
use Common\TestModel;

class Common_GetCodeController extends BaseController {
    /**
     * 是否需要登录授权
     * @var bool
     */
    public $needAuth = true;

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
//        Validator::make($this->params = Request::all(), array(
//            'id'    => 'required',
//            'phone' => 'phone|required',
//        ));
//        $this->params['phone'] = Request::get('phone');//获取get参数
//        $this->params['name']  = Request::post('name');//获取post参数
    }

    /**
     * 加载模型
     */
    public function loadModel()
    {
        //$this->output['data'] = $this->testModel->getData();
        //Response::apiSuccess($this->output);
        self::getConfig();
        //$res = self::getConfig();
        //var_dump($res);
    }
    /**
     * 业务逻辑
     */
    public function indexAction()
    {
        $this->testModel = new TestModel();
        $res  = $this->testModel->getData();
        var_dump($res);
        //Response::apiSuccess($this->output);
    }

}