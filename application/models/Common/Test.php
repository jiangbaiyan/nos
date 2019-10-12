<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-01
 * Time: 11:55
 */

namespace Common;

class TestModel extends \BaseModel {

    /*
     * 表名
     */
    public $table = 'test';

    /**
     * @return mixed
     * @throws \Nos\Exception\CoreException
     */
    public function getData(){
        $params = [
            'name' => 'grapes'
        ];

        $wheres = [
            'id'   => 222
        ];
        $condition = $this->prepareWhere($wheres);

        $row = $this->update($params, $condition['where'], $condition['bind']);
        //$data = $this->select(['id','name'],$condition['where'],$condition['bind']);
        var_dump($row);die;
        $data = $this->getList($select, $ext, $bind);
        return $data;
    }

}