<?php
/**
 * 模型基类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-17
 * Time: 08:35
 */

use Nos\Comm\Db;
use Nos\Comm\Log;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;

class BaseModel{

    /**
     * 表名
     * @var
     */
    protected $table;

    /**
     * 创建记录
     * @param $data
     * @return mixed
     * @throws OperateFailedException
     * @throws \Nos\Exception\CoreException
     */
    public function create($data){
        !is_array($data) && $bind = array($data);
        $keys = array_keys($data);
        $vals = array_values($data);
        $paras = array_fill(0, count($keys),'?');
        $sql = "insert into {$this->table} (`" . join("`,`", $keys) . "`) values(" . join(",", $paras) . ")";
        $rows = Db::update($sql, $vals);
        if (!$rows){
            Log::fatal('baseModel|create_failed|$data:' . json_encode($data) . '|sql:' . $sql);
            throw new OperateFailedException('创建失败');
        }
        return $rows;
    }

    /**
     * 删除记录
     * @param bool $isSoft
     * @param string $ext
     * @param array $bind
     * @param string $deleteColumn
     * @return mixed
     * @throws CoreException
     * @throws OperateFailedException
     */
    public function delete($isSoft = false, $ext = '', $bind = array(), $deleteColumn = 'deleted_at'){
        !is_array($bind) && $bind = array($bind);
        if ($isSoft){
            $time = date('Y-m-d H:i:s');
            $this->update(array(
                $deleteColumn => $time,
            ), $ext, $bind);
        } else{
            $sql = "delete from {$this->table} " . $ext;
            $rows = Db::update($sql, $bind);
            if (!$rows){
                Log::fatal('baseModel|delete_failed|$data:'  . '|sql:' . $sql . '|bind:' . json_encode($bind));
                throw new OperateFailedException('删除失败');
            }
            return $rows;
        }
    }

    /**
     * 获取记录列表
     * @param array $select
     * @param string $ext
     * @param array $bind
     * @return mixed
     * @throws CoreException
     */
    public function getList($select = array(), $ext = '', $bind = array()){
        !is_array($bind) && $bind = array($bind);
        if (!is_array($select)){
            $fields = $select;
        } else if (empty($select)){
            $fields = '*';
        } else{
            $fields = implode('`, `', $select);
        }
        if ($fields == '*'){
            $sql = "select * from {$this->table} " . $ext;
        } else{
            $sql = "select  '{$fields}' from {$this->table} " . $ext;
        }
        return Db::fetchAll($sql, $bind);
    }

    /**
     * @param array $select
     * @param string $ext
     * @param array $bind
     * @return mixed
     * @throws CoreException
     *
     */
    public function getListAndCount($select = array(), $ext = '', $bind = array()){
        !is_array($bind) && $bind = array($bind);
        if (!is_array($select)){
            $fields = $select;
        } else if (empty($select)){
            $fields = '*';
        } else{
            $fields = implode('`, `', $select);
        }
        if ($fields == '*'){
            $sql = "select SQL_CALC_FOUND_ROWS * from {$this->table} " . $ext;
        } else{
            $sql = "select SQL_CALC_FOUND_ROWS  `{$fields}` from {$this->table} " . $ext;
        }
        $data = Db::fetchAll($sql, $bind);
        $count = Db::fetchAll("SELECT FOUND_ROWS()");
        $count = $count[0]['FOUND_ROWS()'];
        return array("data" => $data,
            "count" => $count,
            );
    }

    /**
     * 获取记录总数
     * @param string $ext
     * @param array $bind
     * @return array
     * @throws CoreException
     */
    public function getTotal($ext = '', $bind = array()){
        !is_array($bind) && $bind = array($bind);
        $sql = "select count(*) as count from {$this->table} " . $ext;
        $data = Db::fetchAll($sql, $bind);
        return isset($data[0]['count']) ? $data[0]['count'] : array();
    }

    /**
     * 通过id获取记录
     * @param $id
     * @param array $select
     * @return array
     * @throws CoreException
     */
    public function getById($id, $select = array()){
        $data = $this->getList($select, 'where id = ?', array($id));
        return isset($data[0]) ? $data[0] : array();
    }

    /**
     * 更新记录
     * @param $data
     * @param string $ext
     * @param array $bind
     * @param bool $autoTime
     * @param string $updateColumn
     * @return mixed
     * @throws CoreException
     * @throws OperateFailedException
     */
    public function update($data, $ext = '', $bind = array(), $autoTime = true, $updateColumn = 'updated_at'){
        !is_array($bind) && $bind = array($bind);
        !is_array($data) && $data = array($data);
        if ($autoTime){
            $now = date('Y-m-d H:i:s');
            $data = array_merge($data, array(
                $updateColumn => $now
            ));
        }
        $keys = array_keys($data);
        $vals = array_values($data);
        foreach ($keys as &$key){
            $key .= '=?';
        }
        $keyStr = join(',', $keys);
        $sql = "update {$this->table} set {$keyStr} " . $ext;
        $rows = Db::update($sql, array_merge($vals, $bind));
        if (!$rows){
            Log::fatal('baseModel|update_failed|$data:'  . '|sql:' . $sql . '|bind:' . json_encode($bind));
            throw new OperateFailedException('更新失败');
        }
        return $rows;
    }
}