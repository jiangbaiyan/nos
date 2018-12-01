<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/12/1
 * Time: 14:00
 */
namespace  Comm;

use Yaf\Registry;
use Exception\CoreException;


class Redis
{
    private  $redis;
    private static $key = "redis";
    private static $enable = false;
    private $host;
    private $port;
    private $timeout;
    private $password;
    private $database;

    public function __construct()
    {
            $config = Registry::get("config");
            $this->host = !empty($config[self::$key]['host']) ? $config[self::$key]['host'] : null;
            $this->port = !empty($config[self::$key]['port']) ? $config[self::$key]['port'] : null;
            $this->password = !empty([self::$key]['password']) ? $config[self::$key]['password'] : null;
            $this->timeout = !empty($config[self::$key]['$timeout']) ? $config[self::$key]['$timeout'] : 0;
            $this->database = !empty($config[self::$key]['database']) ? $config[self::$key]['database'] : 0;
            self::$enable = true;

    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->redis->close();
    }

    public function connect(){
        try {
            $redis = new \Redis();
            $result = $redis->connect($this->host, $this->port, $this->timeout);
        if ($result === false) {
            throw new CoreException("connect error!");
        }
        if (!empty($this->password)) {
            $result = $redis->auth($this->password);
            if ($result === flase) {
                throw new CoreException("passwd error!");
            }
        }
        if (!empty($this->_database)) {
            $result = $redis->select($this->_database);

            if ($result === false) {
                throw new CoreException("database select error!");
            }
        }
        $this->redis = $redis;
        }catch (\Exception $e){
            Log::fatal($e->getMessage());
            throw new CoreException('redis connect failed');
        }
    }

    public function getRedis(){
        return $this->redis;
    }
    /**
     * 写缓存
     */
    public function set($key,$value,$expire = 0){
       if($expire == 0){
           $ret = $this->redis->set($key,$value);
       }else{
           $ret = $this->redis->set($key,$value,$expire);
       }
       return $ret;
    }
    /**
     * 读缓存，可读一个或多个key
     */
    public function get($key){
        $func = is_array($key) ? 'mGet' : 'get';
        return $this->redis->{$func}($key);
    }
    /**
     * 条件形式设置缓存，如果 key 不存时就设置，存在时设置失败
     */
    public function setnx($key,$value){
        return $this->redis->setnx($key,$value);
    }
    /**
     * 删除key
     * 缓存KEY，支持单个健:"key1" 或多个健:array('key1','key2')
     */
    public function del($key){
        return $this->redis->delete($key);
    }
    /**
     * 值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
     *
     * @param string $key 缓存KEY
     * @param int $default 操作时的默认值
     * @return int　操作后的值
     */
    public function incr($key,$default =1){
        if($default == 1){
            return $this->redis->incr($key);
        }else{
            return $this->redis->incrBy($key,$default);
        }
    }
    /**
     * 值减减操作,类似 --$i ,如果 key 不存在时自动设置为 0 后进行减减操作
     *
     * @param string $key 缓存KEY
     * @param int $default 操作时的默认值
     * @return int　操作后的值
     */
    public function decr($key, $default = 1)
    {
        if ($default == 1)
        {
            return $this->redis->decr($key);
        }
        else
        {
            return $this->redis->decrBy($key, $default);
        }
    }

    /**
     *    lpush
     */
    public function lpush($key, $value)
    {
        return $this->redis->lpush($key, $value);
    }
    /**
     *    rpush
     */
    public function rpush($key, $value)
    {
        return $this->redis->rpush($key, $value);
    }

    /**
     *    add lpop
     */
    public function lpop($key)
    {
        return $this->redis->lpop($key);
    }
    /**
     * lrange
     */
    public function lrange($key, $start, $end)
    {
        return $this->redis->lrange($key, $start, $end);
    }

    public function hset($name, $key, $value)
    {
        if (is_array($value))
        {
            $value = json_encode($value);
        }
        return $this->redis->hset($name, $key, $value);
    }
    /**
     *    get hash opeation
     */
    public function hget($name, $key = null)
    {
        if ($key)
        {
            $data = $this->redis->hget($name, $key);
            $value = json_decode($data, true);
            if (is_null($value))
            {
                $value = $data;
            }
            return $value;
        }
        return $this->redis->hgetAll($name);
    }
    /**
     *    delete hash opeation
     */
    public function hdel($name, $key = null)
    {
        if ($key)
        {
            return $this->redis->hdel($name, $key);
        }
        return $this->redis->del($name);
    }


}