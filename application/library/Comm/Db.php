<?php
/**
 * 数据库操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-30
 * Time: 16:32
 */

namespace Comm;

use Exception\CoreException;
use PDO;
use Yaf\Registry;

class Db{

    private static $db;

    /**
     * 构造方法，初始化句柄
     * Db constructor.
     * @param bool $isSlave
     * @throws CoreException
     */
    private static function connect($isSlave = true){
        try{
            if (Registry::has('db_read')){
                self::$db = Registry::get('db_read');
                return;
            }
            if (Registry::has('db_write')){
                self::$db = Registry::get('db_write');
                return;
            }
            $config = Registry::get('config');
            if ($isSlave){
                $config = $config['db']['read'];
            } else{
                $config = $config['db']['write'];
            }
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            self::$db = new PDO($dsn, $config['user'], $config['password']);
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
        return self::doSql($sql, true, $bind);
    }

    /**
     * 更新库
     * @param $sql
     * @param array $bind
     * @return mixed
     * @throws CoreException
     */
    public static function update($sql, $bind = array()){
        return self::doSql($sql, false, $bind);
    }

    /**
     * 执行sql语句
     * @param $sql
     * @param bool $isSlave
     * @param array $bind
     * @return mixed
     * @throws CoreException
     */
    private static function doSql($sql, $isSlave = true, $bind = array()){
        try{
            self::connect($isSlave);
            $str = $isSlave ? 'read' : 'write';
            Registry::set('db_' . $str, self::$db);
            $handle = self::$db->prepare($sql);
            $res = $handle->execute($bind);
            if (!$res){
               throw new CoreException(json_encode($handle->errorInfo()));
            }
            if (!$isSlave){
                return true;
            }
            $data = $handle->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (\Exception $e){
            Log::fatal('pdo_do_sql_failed|msg:' .  $e->getMessage() . '|sql:' . $sql . '|isSlave:' . $isSlave . '|bind:' . json_encode($bind));
            throw new CoreException('db query failed');
        }
    }



}