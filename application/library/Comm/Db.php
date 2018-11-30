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

    private $db;

    /**
     * 构造方法，初始化句柄
     * Db constructor.
     * @param bool $isSlave
     * @throws CoreException
     */
    public function connect($isSlave = true){
        try{
            if (Registry::has('db_read')){
                $this->db = Registry::get('db_read');
                return;
            }
            if (Registry::has('db_write')){
                $this->db = Registry::get('db_write');
                return;
            }
            $config = Registry::get('config');
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


    /**
     * 查库
     * @throws CoreException
     */
    public function fetch(){
        $this->connect(true);
        Registry::set('db_read', $this->db);
    }

    /**
     * 更新库
     * @throws CoreException
     */
    public function update(){
        $this->connect(false);
        Registry::set('db_write', $this->db);
    }




}