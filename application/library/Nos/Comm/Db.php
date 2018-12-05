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
use Yaf\Registry;

class Db{

    private static $db;

    private static $node;

    const DB_NODE_MASTER_KEY = 'write';//主库

    const DB_NODE_SLAVE_KEY  = 'read';//从库


    /**
     * 初始化数据库连接
     * Db constructor.
     * @throws CoreException
     */
    private static function connect(){
        try{
            $key = 'db_' . self::$node;
            if (Registry::has($key)){
                self::$db = Registry::get($key);
                if (!empty(self::$db)){
                    return;
                }
            }
            $config = Registry::get('config');
            $config = $config['db'][self::$node];
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            self::$db = new PDO($dsn, $config['user'], $config['password']);
            Registry::set($key, self::$db);
        } catch (\Exception $e){
            Log::fatal($e->getMessage());
            throw new CoreException('db connect failed');
        }
    }


    /**
     * 查库
     * @param $sql
     * @param array $bind
     * @return mixed
     * @throws CoreException
     */
    public static function fetchAll($sql, $bind = array()){
        self::$node = self::DB_NODE_SLAVE_KEY;
        return self::doSql($sql, $bind);
    }

    /**
     * 增删改库
     * @param $sql
     * @param array $bind
     * @return mixed
     * @throws CoreException
     */
    public static function update($sql, $bind = array()){
        self::$node = self::DB_NODE_MASTER_KEY;
        return self::doSql($sql, $bind);
    }

    /**
     * 执行sql语句
     * @param $sql
     * @param array $bind
     * @return mixed
     * @throws CoreException
     */
    private static function doSql($sql, $bind = array()){
        if (!is_array($bind)){
            throw new CoreException('pdo_do_sql_wrong_bind' . '|bind:' . json_encode($bind));
        }
        try{
            self::connect();
            $handle = self::$db->prepare($sql);
            $res = $handle->execute($bind);
            if (!$res){
               throw new CoreException(json_encode($handle->errorInfo()));
            }
            if (self::$node == self::DB_NODE_MASTER_KEY){
                return true;
            }
            $data = $handle->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (\Exception $e){
            Log::fatal('pdo_do_sql_failed|msg:' .  $e->getMessage() . '|sql:' . $sql . '|node:' . self::$node . '|bind:' . json_encode($bind));
            throw new CoreException('db query failed');
        }
    }

}