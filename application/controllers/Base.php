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
use Comm\Config;

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


    /**
     * 获取GET参数
     * @param $key
     * @param string $default
     * @return string
     */
    public function get($key, $default = ''){
        $data = $this->getRequest()->getQuery($key);
        return isset($data) ? $data : $default;
    }

    /**
     * 获取POST参数
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public function post($key, $default = ''){
        $data = $this->getRequest()->getPost($key);
        return isset($data) ? $data : $default;
    }

    /**
     * 普通返回
     * @param $status
     * @param $msg
     * @param $data
     */
    public function response($status, $msg, $data = array()){
        ob_clean();
        echo json_encode(array(
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data
        ));
        exit;
    }

    /**
     * 接口成功返回
     * @throws \Exception\OperateFailedException
     * @throws \Exception\ParamValidateFailedException
     */
    public function responseSuccess(){
        $config = Config::get('response.SUCCESS');
        $this->response($config['STATUS'], $config['MSG'], $this->output);
    }

}