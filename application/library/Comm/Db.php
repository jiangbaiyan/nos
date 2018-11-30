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

class Db{

    private $db;

    /**
     * 构造方法，初始化句柄
     * Db constructor.
     * @param $config
     * @param bool $isSlave
     * @throws CoreException
     */
    public function __construct($config, $isSlave = true){
        try{
            if ($isSlave){
                $config = $config['db']['read'];
            } else{
                $config = $config['db']['write'];
            }
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            $this->db = new PDO($dsn, $config['user'], $config['password']);
        } catch (\Exception $e){
            Log::fatal($e->getMessage());
            throw new CoreException('db connect failed');
        }
    }


}