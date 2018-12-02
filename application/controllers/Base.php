<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/11/30
 * Time: 10:00
 */
/**
 * 控制器公共基类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-27
 * Time: 17:21
 */

use Yaf\Controller_Abstract;
use Nos\Comm\Config;

class BaseController extends Controller_Abstract{


    /**
     * 请求参数
     *
     * @var array
     */
    protected $params = array();

    /**
     * 返回数据
     *
     * @var array
     */
    protected $output = array();

    /**
     * 是否需要校验
     *
     * @var bool
     */
    protected $needAuth = true;//是否需要校验


    /**
     * 初始化
     */
    protected function init(){
        if ($this->needAuth){
            $this->auth();//用户授权检测
        }
        $this->checkParam();//请求参数校验
        $this->loadModel();//模型载入
    }

    private function auth(){
        //验证token...
    }

    /**
     * 参数校验
     */
    protected function checkParam(){}

    /**
     * 业务逻辑
     */
    protected function indexAction(){}

    /**
     * 加载模型
     */
    protected function loadModel(){}


}