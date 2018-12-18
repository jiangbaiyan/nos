<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-01
 * Time: 11:55
 */

namespace Common;

class TestModel extends \BaseModel {

    /**
     * @return mixed
     * @throws \Nos\Exception\CoreException
     */
    protected $table = "test";
    public function getData(){
        $sql = '*';
        $data = $this->getListAndCount($sql, "where name= ? limit 0,1",array(' '));
        return $data;
    }

}