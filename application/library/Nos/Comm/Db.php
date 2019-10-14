<?php
/**
 * 数据库操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-30
 * Time: 16:32
 */

namespace Nos\Comm;

use Nos\Exception\CoreException;
use PDO;
use Yaf\Config\Ini;

class Db
{
    const DB_NODE_MASTER_KEY = 'write'; //主库
    const DB_NODE_SLAVE_KEY  = 'read';  //从库

    /**
     * @var array $config 数据库配置
     */
    private static $config = [];

    /**
     * @var array $connPool 数据库连接池
     */
    private static $connPool = [];


    /**
     * 查询操作
     * @param string $sql 查询SQL语句
     * @param array $bind 参数绑定
     * @return mixed
     * @throws CoreException
     */
    public static function query($sql, array $bind = [])
    {
        return self::doSql(self::DB_NODE_SLAVE_KEY, $sql, $bind);
    }

    /**
     * 增删改操作
     * @param string $sql 查询SQL语句
     * @param array $bind 参数绑定
     * @return mixed
     * @throws CoreException
     */
    public static function modify(string $sql, array $bind = []){
        return self::doSql(self::DB_NODE_MASTER_KEY, $sql, $bind);
    }

    /**
     * 执行sql语句
     * @param string $node 数据库节点
     * @param string $sql SQL语句
     * @param array $bind 参数绑定
     * @return mixed
     * @throws CoreException
     */
    public static function doSql(string $node, string $sql, array $bind = [])
    {
        try{
            // 从连接池拿出数据库实例
            $dbInstance = self::getInstance($node);
            $handle = $dbInstance->prepare($sql);
            $res = $handle->execute($bind);
            if (!$res){
                throw new CoreException(json_encode($handle->errorInfo()));
            }
            // 获取影响行数
            $count = $handle->rowcount();
            // 主库增删改操作，直接返回影响的行数
            if ($node == self::DB_NODE_MASTER_KEY){
                return $count;
            }
            // 以下是查询操作
            if (!$count){
                return []; // 结果集为空，直接返回
            } else {
                return $handle->fetchAll(PDO::FETCH_ASSOC); // 结果集不为空，取出数据
            }
        } catch (\Exception $e){
            throw new CoreException('db|pdo_do_sql_failed|msg:' .  $e->getMessage() . '|sql:' . $sql . '|node:' . $node . '|bind:' . json_encode($bind));
        }
    }

    /**
     * 从连接池获取数据库连接实例
     * @param string $node 节点类型
     * @return mixed
     * @throws CoreException
     */
    public static function getInstance(string $node)
    {
        try {
            /* 获取数据库配置，返回的配置格式如下:
             * [
             *     write => [],
             *     read => []
             * ]
             *  若配置为空，需要重新加载
             */
            if (empty(self::$config)) {
                $config = new Ini(APP_PATH . '/config/db.ini', ini_get('yaf.environ'));
                self::$config = $config->toArray();
            }
            // 获取当前节点下的配置
            $config = self::$config[$node];
            // PDO连接
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            // 这几个参数唯一确定连接池的key
            $connPoolKey = $dsn . $config['user'] . $config['password'];
            // 连接池中不存在，需要重新往连接池中添加
            if (!isset(self::$connPool[$connPoolKey])) {
                $dbInstance = new PDO($dsn, $config['user'], $config['password']);
                self::$connPool[$connPoolKey] = $dbInstance;
            }
            return self::$connPool[$connPoolKey];
        } catch (\Exception $e) {
            throw new CoreException('db|connect_failed|msg:' . $e->getMessage());
        }
    }
}