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
use Nos\Exception\CoreException;
use Yaf\Config\Ini;
use Yaf\Request\Http;

class Request
{


    /**
     * 请求实例
     * @var Http
     */
    private static $request;

    /**
     * 请求协议
     * @var
     */
    private static $schema;

    /**
     * 单例获取请求实例，避免请求期间重复实例化
     * @return Http
     */
    private static function getRequestInstance(){
        if (!self::$request instanceof Http){
            self::$request = new Http();
        }
        return self::$request;
    }

    /**
     * 获取单个GET参数
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function get(string $key, string $default = ''){
        return self::getRequestInstance()->getQuery($key, $default);
    }

    /**
     * 获取单个POST参数
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    public static function post(string $key, string $default = ''){
        return self::getRequestInstance()->getPost($key, $default);
    }

    /**
     * 获取文件
     * @param $key
     * @return array|mixed
     */
    public static function file(string $key){
        return self::getRequestInstance()->getFiles($key);
    }

    /**
     * 获取请求头
     * @param string $key
     * @param string $default
     * @return array|false|mixed|null
     */
    public static function header(string $key = '', string $default = ''){
        $headers = [];
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
        $obj = self::getRequestInstance();
        if ($obj->isGet()){
            return self::getRequestInstance()->getQuery();
        } else if ($obj->isPost()){
            return self::getRequestInstance()->getPost();
        } else{
            return false;
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
     * @param string $type
     * @param string $url
     * @param array $postData
     * @param array $options
     * @param int $retry
     * @param int $timeout
     * @return bool|string
     * @throws CoreException
     */
    public static function send(string $type, string $url, array $postData = [], array $options = [], int $retry = 3, int $timeout = 20){
        try {
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
                    throw new CoreException('curl|send_request_error|url:' . $url . '|type:' . $type . '|postData:' .json_encode($postData) . '|retry:' . $retry . '|curl_error:' . json_encode(curl_error($ch)));
                }
            }
            curl_close($ch);
        } catch (\Exception $e) {
            throw new CoreException('curl|send_request_error|url:' . $url . '|type:' . $type . '|postData:' . json_encode($postData) . '|retry:' . $retry . '|curl_exception:' . json_encode($e->getMessage()) . '|curl_error:' . json_encode(curl_error($ch)));
        }
        return $res;
    }
}