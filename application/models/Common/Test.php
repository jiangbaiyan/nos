<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-01
 * Time: 11:55
 */

namespace Common;

use Nos\Comm\Db;

class TestModel{

    public function getData(){
        $sql = 'select * from test';
        $data = Db::fetchAll($sql);
        return $data;
    }

}