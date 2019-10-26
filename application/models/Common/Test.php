<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-01
 * Time: 11:55
 */

namespace Common;

use Nos\Base\BaseModel;

class TestModel extends BaseModel {

    /*
     * 表名
     */
    public static $table = 'test';

    /**
     * 模型层查询示例
     * @return mixed
     * @throws \Exception
     */
    public function getData()
    {
        // 要更新的数据
        $params = [
            'name' => 'grapes'
        ];
        // 条件过滤
        $wheres = [
            'id'   => 222
        ];
        // 附加选项
        $option = [
            'id'  => 'asc'
        ];
        // 更新操作
        $row = self::update($params, $wheres);
        // 查询操作
        $data = self::select(['id','name'], $wheres, $option);
        // 返回数据
        return $data;
    }

}