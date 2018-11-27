<?php
/**
 * 控制器公共基类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-27
 * Time: 17:21
 */

class Comm_Control extends Yaf_Controller_Abstract{

    /**
     * 请求对象
     *
     * @var Yaf_Request_Http
     */
    private static $request;

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
     * 参数校验
     */
    protected function checkParam(){}


    /**
     * 业务逻辑
     */
    protected function indexAction(){}


    /**
     * 获取GET参数
     * @param $key
     * @param string $default
     * @return string
     */
    public function get($key, $default = ''){
        $data = $this->getRequestObj()->getQuery($key);
        return isset($data) ? $this->getRequestObj()->getQuery($key) : $default;
    }

    /**
     * 获取POST参数
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public function post($key, $default = ''){
        $data = $this->getRequestObj()->getPost($key);
        return isset($data) ? $this->getRequestObj()->getPost($key) : $default;
    }

    /**
     * 接口成功返回
     * @param array $data
     */
    public function responseSuccess($data = array()){
        ob_clean();
        echo json_encode(array(
            'status' => 200,  //待配置化
            'msg'    => 'success',
            'data'   => $data
        ));
    }


    /**
     * 获取请求对象
     * @return Yaf_Request_Abstract|Yaf_Request_Http
     */
    private function getRequestObj(){
        if (!self::$request instanceof Yaf_Request_Abstract){
            self::$request = $this->getRequest();
        }
        return self::$request;
    }
}