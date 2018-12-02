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

class Request{


    /**
     * 获取完整URL
     * @return string
     */
    public static function getFullUrl(){
        return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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
     * @param null $params
     * @param int $retry
     * @param int $timeout
     * @return bool|string
     * @throws OperateFailedException
     */
    public static function send($type,$url,$params = null,$retry = 3,$timeout = 20){
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
            } else if (!empty($params) && is_array($params)){
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