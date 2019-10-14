<?php
/**
 * 请求操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-02
 * Time: 11:31
 */

namespace Nos\Http;

use Nos\Comm\File;
use Nos\Exception\CoreException;
use Yaf\Config\Ini;

class Request
{
    /**
     * 请求协议
     * @var static $schema
     */
    private static $schema = '';

    /**
     * 请求参数
     * @var array $params
     */
    private static $params = [];


    /**
     * 获取单个GET参数
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function get(string $key, string $default = ''){
        // 如果有参数缓存则从缓存中取，否则从$_GET中取
        if (empty(self::$params)) {
            return $_GET[$key] ?? $default;
        } else {
            return self::$params[$key];
        }
    }

    /**
     * 获取单个POST参数
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    public static function post(string $key, string $default = ''){
        // 如果有参数缓存则从缓存中取，否则从$_POST中取
        if (empty(self::$params)) {
            return $_POST[$key] ?? $default;
        } else {
            return self::$params[$key];
        }
    }

    /**
     * 获取文件信息
     * 返回数据示例：
     * [
     *     "name": "WechatIMG9.jpeg",
     *     "type": "image/jpeg",
     *     "tmp_name": "/tmp/phpSMXprN",
     *     "error": 0,
     *     "size": 25569
     * ]
     * @param $key
     * @return File|bool
     */
    public static function file(string $key){
        if (!isset($_FILES[$key]) || empty($_FILES[$key])) {
            return false;
        }
        return new File($_FILES[$key]);
    }

    /**
     * 获取所有请求参数
     * @return mixed
     */
    public static function all(){
        // 如果有参数缓存则从缓存中取，否则从输入流获取
        if (empty(self::$params)) {
            // 从输入流中获取所有参数（包括PUT/DELETE）
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            // 缓存此次请求参数
            self::$params = array_merge($_GET, $_POST, $data);
        }
        return self::$params;
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
     * 获取完整URL
     * @return string
     */
    public static function getFullUrl(){
        // 缓存协议为空，需要重新从配置文件获取
        if (empty(self::$schema)){
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