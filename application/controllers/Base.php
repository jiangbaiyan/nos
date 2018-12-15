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

class BaseController extends Controller_Abstract{

    /**
     * 当前登录用户
     * @var null
     */
    protected $user = null;

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
    protected $needAuth;//是否需要校验

    /**
     * 初始化
     */
    private function init(){
        $this->needAuth && $this->user = $this->auth();
        $this->checkParam();//请求参数校验
        $this->loadModel();//模型载入
    }

    /**
     * 用户授权并返回当前用户对象，自动载入到控制器$user变量中
     * @return object
     */
    protected function auth(){}

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

    public static function getConfig(){

        $module = strstr(get_called_class(), '_', true);

        $key = strtolower(preg_replace("/^{$module}_(.*)Controller$/", '$1', get_called_class()));

        $configKey = strtolower("{$module}.{$key}");

        $config = Config::get($configKey);//Config::get($configKey);

        return $config;
    }
}