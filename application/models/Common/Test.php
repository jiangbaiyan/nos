<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-01
 * Time: 11:55
 */

namespace Common;

class TestModel extends \BaseModel {

    public $table = 'test';

    /**
     * @return mixed
     * @throws \Nos\Exception\CoreException
     */
    public function getData(){
        $select = array('id', 'name');
        $ext = 'where id = ?';
        $bind = array(2);
        $data = $this->getList($select, $ext, $bind);
        return $data;
    }

}