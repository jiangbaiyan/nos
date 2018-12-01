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
    private static $redis;
    private static $enable = false;
    private $host;
    private $port;
    private $timeout;
    private $password;
    private $database;

    public function __construct()
    {
        if($this->isEnable()){
            $config = Registry::get("redis");
            $this->host = !empty($config['host']) ? $config['host'] : null;
            $this->port = !empty($config['port']) ? $config['port'] : null;
            $this->password = !empty($config['password']) ? $config['password'] : null;
            $this->timeout = !empty($config['$timeout']) ? $config['$timeout'] : 0;
            $this->database = !empty($config['database']) ? $config['database'] : 0;
            $this->enable = true;

        }
    }
    public function connect(){
        try {
            $redis = new \Redis();
            $result = $redis->connect($this->host, $this->port, $this->timeout);
            var_dump($result);
        if ($result === false) {
            return false;
        }
        if (!empty($this->password)) {
            $result = $redis->auth($this->password);
            if ($result === flase) {
                return false;
            }
        }
        if (!empty($this->_database)) {
            $result = $redis->select($this->_database);

            if ($result === false) {
                return false;
            }
        }
        self::$redis = $redis;
        }catch (\Exception $e){
            Log::fatal($e->getMessage());
            throw new CoreException('redis connect failed');
        }
    }

    public function isEnable(){
        return self::$enable;
    }
}