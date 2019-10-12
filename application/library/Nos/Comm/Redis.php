<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/12/1
 * Time: 14:00
 */

namespace Nos\Comm;

use Nos\Exception\CoreException;
use Yaf\Config\Ini;

class Redis
{

    private static $redis;

    private static $config;

    /**
     * 获取redis实例
     * @return \Redis
     * @throws CoreException
     */
    public static function getInstance()
    {
        // 之前有redis实例
        if (empty(self::$redis)){ // 没有redis实例
            if (empty(self::$config)) {
                $config = new Ini(APP_PATH . '/config/redis.ini', ini_get('yaf.environ'));
                self::$config = $config->toArray();
            }
            // 加载redis配置
            $host = self::$config['host'];
            $port = self::$config['port'];
            $password = self::$config['password'];
            $timeout = self::$config['timeout'];
            $database = self::$config['database'];
            // 创建redis实例
            self::$redis = new \Redis();
            // 连接
            $result = self::$redis->connect($host, $port, $timeout);
            if ($result === false) {
                Log::fatal('redis|connect_failed|errorInfo:' . json_encode(self::$redis->errorInfo()));
                throw new CoreException();
            }
            // 密码配置
            if (!empty($password)) {
                $result = self::$redis->auth($password);
                if ($result === false) {
                    Log::fatal('redis|auth_failed|errorInfo:' . json_encode(self::$redis->errorInfo()));
                    throw new CoreException();
                }
            }
            // 数据库选择
            if (!empty($database)) {
                $result = self::$redis->select($database);
                if ($result === false) {
                    Log::fatal('redis|select_db_failed|errorInfo:' . json_encode(self::$redis->errorInfo()));
                    throw new CoreException();
                }
            }
        }
        return self::$redis;
    }

}