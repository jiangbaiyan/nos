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
use Yaf\Registry;
use Yaf\Request\Http;

class Request{


    /**
     * 请求实例
     * @var Http
     */
    private static $request;

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
     * @param $key
     * @param string $default
     * @return string
     */
    public static function get($key, $default = null){
        return self::getRequestInstance()->getQuery($key, $default);
    }

    /**
     * 获取单个POST参数
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public static function post($key, $default = null){
        return self::getRequestInstance()->getPost($key, $default);
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
        $schema = Registry::get('config')['schema'];
        return $schema . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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
            curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($postData));
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
                Log::fatal('curl|send_request_error|url:' . $url . '|type:' . $type . '|params:0 . ' .$params . '|retry:' . $retry . '|curl_error:' . json_encode(curl_error($ch)));
                throw new OperateFailedException('发送请求失败，请重试');
            }
        }
        curl_close($ch);
        return $res;
    }

}