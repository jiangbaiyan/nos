<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-01
 * Time: 11:55
 */

namespace Common;

class TestModel extends \BaseModel
{

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
        // 解析过滤条件
        $condition = self::prepareWhere($wheres);
        // 更新操作
        $row = self::update($params, $condition['where'], $condition['bind']);
        // 查询操作
        $data = self::select(['id','name'], $condition['where'], $condition['bind']);
        // 返回数据
        return $data;
    }

}