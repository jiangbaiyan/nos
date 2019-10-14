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

    /**
     * redis实例
     * @var \Redis $redis
     */
    private static $redis;


    /**
     * 获取redis实例
     * @return \Redis
     * @throws CoreException
     */
    public static function getInstance()
    {
        // 有redis缓存实例，直接取
        if (self::$redis instanceof \Redis) { // 没有redis实例
            return self::$redis;
        } else { // 之前无redis实例，需要重新实例化
            // 读取redis配置
            $configInstance = new Ini(APP_PATH . '/config/redis.ini', ini_get('yaf.environ'));
            $config = $configInstance->toArray();
            // 加载redis配置
            $host     = $config['host'];
            $port     = $config['port'];
            $password = $config['password'];
            $timeout  = $config['timeout'];
            $database = $config['database'];
            // 创建redis实例
            $redis = new \Redis();
            // 连接
            $result = $redis->connect($host, $port, $timeout);
            if ($result === false) {
                throw new CoreException('redis|connect_failed|errorInfo:' . json_encode(self::$redis->errorInfo()));
            }
            // 密码配置
            if (!empty($password)) {
                $result = $redis->auth($password);
                if ($result === false) {
                    throw new CoreException('redis|auth_failed|errorInfo:' . json_encode(self::$redis->errorInfo()));
                }
            }
            // 数据库选择
            if (!empty($database)) {
                $result = $redis->select($database);
                if ($result === false) {
                    throw new CoreException('redis|select_db_failed|errorInfo:' . json_encode(self::$redis->errorInfo()));
                }
            }
            // 缓存redis实例等待下次调用
            self::$redis = $redis;
            return $redis;
        }
    }
}