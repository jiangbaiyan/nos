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
use Nos\Comm\Db;
use Yaf\Registry;
use Nos\Comm\Config;
class Common_TestController extends BaseController {

    public $needAuth = false;

    private $testModel;

//    public function checkParam(){
//        $params = Request::all();//获取全部参数
//        Validator::make($params, array(
//            'id'    => 'required',
//            'phone' => 'phone|required',
//        ));
//        $this->params['phone'] = Request::get('phone');//获取get参数
//        $this->params['name']  = Request::post('name');//获取post参数
//    }
//
//    public function loadModel()
//    {
//        $this->testModel = new \Common\TestModel();
//    }

    public function indexAction()
    {

        //$this->output['data'] = $this->testModel->getData();
        //Response::apiSuccess($this->output);
        $res = self::getConfig();
        var_dump($res);
    }

    public static function getConfig(){
        $module = strstr(get_called_class(), '_', true);

        $key = strtolower(preg_replace("/^{$module}_(.*)Controller$/", '$1', get_called_class()));

        $configKey = strtolower("{$module}.{$key}");

        $config = Config::get($configKey);//Config::get($configKey);

        return $config;
    }

}