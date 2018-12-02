<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/12/1
 * Time: 14:00
 */
namespace  Comm;

use Exception\RedisException;
use Yaf\Registry;


class Redis
{
    private  static  $redis;
    private static $key = "redis";
    private static $enable = false;
    private static $host;
    private static $port;
    private static $timeout;
    private static $password;
    private static $database;

    public function __construct()
    {
        self::connect();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        self::$redis->close();
    }

 public static function  connect(){
        try {
            $config = Registry::get("config");
            self::$host = !empty($config[self::$key]['host']) ? $config[self::$key]['host'] : null;
            self::$port = !empty($config[self::$key]['port']) ? $config[self::$key]['port'] : null;
            self::$password = !empty([self::$key]['password']) ? $config[self::$key]['password'] : null;
            self::$timeout = !empty($config[self::$key]['$timeout']) ? $config[self::$key]['$timeout'] : 0;
            self::$database = !empty($config[self::$key]['database']) ? $config[self::$key]['database'] : 0;
            self::$enable = true;
            $redis = new \Redis();
            $result = $redis->connect(self::$host,self::$port, self::$timeout);
        if ($result === false) {
            throw new RedisException(json_encode($redis->errorInfo()));
        }
        if (!empty(self::$password)) {
            $result = $redis->auth(self::$password);
            if ($result === false) {
                throw new RedisException(json_encode($redis->errorInfo()));
            }
        }
        if (!empty(self::$database)) {
            $result = $redis->select(self::$database);

            if ($result === false) {
                throw new RedisException(json_encode($redis->errorInfo()));
            }
        }
            self::$redis = $redis;
        }catch (\Exception $e){
            Log::fatal($e->getMessage());
            throw new RedisException('redis connect failed');
        }
    }

    public  static function getRedis(){
        return self::$redis;
    }
    /**
     * 写缓存
     */
    public static function set($key,$value,$expire = 0){
       if($expire == 0){
           $ret = self::$redis->set($key,$value);
       }else{
           $ret = self::$redis->set($key,$value,$expire);
       }
       return $ret;
    }
    /**
     * 读缓存，可读一个或多个key
     */
    public static function get($key){
        $func = is_array($key) ? 'mGet' : 'get';
        return self::$redis->{$func}($key);
    }
    /**
     * 条件形式设置缓存，如果 key 不存时就设置，存在时设置失败
     */
    public static function setnx($key,$value){
        return self::$redis->setnx($key,$value);
    }
    /**
     * 删除key
     * 缓存KEY，支持单个健:"key1" 或多个健:array('key1','key2')
     */
    public static function del($key){
        return self::$redis->delete($key);
    }
    /**
     * 值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
     *
     * @param string $key 缓存KEY
     * @param int $default 操作时的默认值
     * @return int　操作后的值
     */
    public static function incr($key,$default =1){
        if($default == 1){
            return self::$redis->incr($key);
        }else{
            return self::$redis->incrBy($key,$default);
        }
    }
    /**
     * 值减减操作,类似 --$i ,如果 key 不存在时自动设置为 0 后进行减减操作
     *
     * @param string $key 缓存KEY
     * @param int $default 操作时的默认值
     * @return int　操作后的值
     */
    public static function decr($key, $default = 1)
    {
        if ($default == 1)
        {
            return self::$redis->decr($key);
        }
        else
        {
            return self::$redis->decrBy($key, $default);
        }
    }

    /**
     *    lpush
     */
    public static function lpush($key, $value)
    {
        return self::$redis->lpush($key, $value);
    }
    /**
     *    rpush
     */
    public static function rpush($key, $value)
    {
        return self::$redis->rpush($key, $value);
    }

    /**
     *    add lpop
     */
    public static function lpop($key)
    {
        return self::$redis->lpop($key);
    }
    /**
     * lrange
     */
    public static function lrange($key, $start, $end)
    {
        return self::$redis->lrange($key, $start, $end);
    }

    public static function hset($name, $key, $value)
    {
        if (is_array($value))
        {
            $value = json_encode($value);
        }
        return self::$redis->hset($name, $key, $value);
    }
    /**
     *    get hash opeation
     */
    public static function hget($name, $key = null)
    {
        if ($key)
        {
            $data = self::$redis->hget($name, $key);
            $value = json_decode($data, true);
            if (is_null($value))
            {
                $value = $data;
            }
            return $value;
        }
        return self::$redis->hgetAll($name);
    }
    /**
     *    delete hash opeation
     */
    public static function hdel($name, $key = null)
    {
        if ($key)
        {
            return self::$redis->hdel($name, $key);
        }
        return self::$redis->del($name);
    }


}