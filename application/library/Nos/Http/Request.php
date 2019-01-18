<?php
/**
 * 请求操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-02
 * Time: 11:31
 */

namespace Nos\Http;

use Nos\Comm\Log;
use Nos\Exception\OperateFailedException;
use Yaf\Config\Ini;

class Request{

    /**
     * 请求协议
     * @var
     */
    private static $schema;


    /**
     * 获取单个GET参数
     * @param $key
     * @param string $default
     * @return string
     */
    public static function get($key, $default = null){
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * 获取单个POST参数
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public static function post($key, $default = null){
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * 获取请求头
     * @param $key
     * @param null $default
     * @return array|false|mixed|null
     */
    public static function header($key = null, $default = null){
        $headers = array();
        if (!function_exists('getallheaders')) {
            function getallheaders() {
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
            }
        } else{
            $headers = getallheaders();
        }
        if (empty($key)){
            return $headers;
        }
        return isset($headers[$key]) ? $headers[$key] : $default;
    }

    /**
     * 获取所有请求参数，自动判断请求类型
     * @return mixed
     */
    public static function all(){
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        if ($method == 'GET'){
            return $_GET;
        } else if ($method == 'POST'){
            return $_POST;
        } else{
            return array();
        }
    }

    /**
     * 获取完整URL
     * @return string
     */
    public static function getFullUrl(){
        $schema = self::$schema;
        if (!isset($schema)){
            $config = new Ini(APP_PATH . '/config/application.ini', ini_get('yaf.environ'));
            $config = $config->toArray();
            self::$schema = $config['schema'];
        }
        return self::$schema . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取不带参数的url
     * @return string
     */
    public static function getBaseUrl(){
        $fullUrl = self::getFullUrl();
        if (strpos($fullUrl, '?') === false){
            return $fullUrl;
        }
        $arr = explode('?', $fullUrl);
        return $arr[0];
    }

    /**
     * 发送Http请求
     * @param $type
     * @param $url
     * @param array $postData
     * @param array $options
     * @param int $retry
     * @param int $timeout
     * @return bool|string
     * @throws OperateFailedException
     */
    public static function send($type, $url, $postData = array(), $options = array(), $retry = 3, $timeout = 20){
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (!empty($options)){
            curl_setopt_array($ch, $options);
        }
        $type = strtoupper($type);
        if ($type == 'POST'){
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);
        }
        $res = curl_exec($ch);
        if (empty($res)){
            for ($i = 0;$i<$retry;$i++){
                $res = curl_exec($ch);
                if (!empty($res)){
                    break;
                }
            }
            if ($i == $retry){
                Log::fatal('curl|send_request_error|url:' . $url . '|type:' . $type . '|postData:' .$postData . '|retry:' . $retry . '|curl_error:' . json_encode(curl_error($ch)));
                throw new OperateFailedException('发送请求失败，请重试');
            }
        }
        curl_close($ch);
        return $res;
    }

}