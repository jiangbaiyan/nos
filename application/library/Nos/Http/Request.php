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

class Request extends Http{


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
            self::$request = new parent();
        }
        return self::$request;
    }

    /**
     * 获取GET参数
     * @param $key
     * @param string $default
     * @return string
     */
    public static function param($key, $default = ''){
        $data = self::getRequestInstance()->getQuery($key);
        return isset($data) ? $data : $default;
    }

    /**
     * 获取POST参数
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public static function form($key, $default = ''){
        $data = self::getRequestInstance()->getPost($key);
        return isset($data) ? $data : $default;
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
     * @param array $params
     * @param int $retry
     * @param int $timeout
     * @return bool|string
     * @throws OperateFailedException
     */
    public static function send($type, $url, $params = array(), $retry = 3, $timeout = 20){
        if (!is_array($params)){
            Log::fatal('curl|illegal_request_params|params:' . json_encode($params));
            throw new OperateFailedException('请求参数格式不合法');
        }
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (!empty($params['header'])){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$params['header']);
        }
        $type = strtoupper($type);
        if ($type == 'POST'){
            curl_setopt($ch,CURLOPT_POST,true);
            if (!empty($params['json'])){
                curl_setopt($ch,CURLOPT_POSTFIELDS,$params['json']);
            } else if (!empty($params['form'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params['form']);
            } else{
                curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));
            }
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