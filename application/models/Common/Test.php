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

    /**
     * @return mixed
     * @throws \Nos\Exception\CoreException
     */
    public function getData(){
        $sql = 'select * from test where id = ?';
        $data = Db::fetchAll($sql, array(2));
        return $data;
    }

}