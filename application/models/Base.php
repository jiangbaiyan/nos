<?php
/**
 * 模型基类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-17
 * Time: 08:35
 */

use Nos\Comm\Db;
use Nos\Comm\Page;
use Nos\Exception\CoreException;

class BaseModel extends Db
{
    /**
     * @var string $table 表名
     */
    protected static $table;

    /**
     * @var array 操作符
     */
    private static $operations = [
        '>', '<', '>=', '<=', '!=', 'like'
    ];

    /**
     * @var bool $isTransacting 是否开启事务
     */
    private static $isTransacting = false;

    /**
     * 事务开始
     * @throws CoreException
     */
    public static function beginTransaction()
    {
        $dbInstance = self::getInstance(self::DB_NODE_MASTER_KEY);
        $dbInstance->begin();
        self::$isTransacting = true;
    }

    /**
     * 事务提交
     * @throws CoreException
     */
    public static function commit()
    {
        $dbInstance = self::getInstance(self::DB_NODE_MASTER_KEY);
        $dbInstance->commit();
        self::$isTransacting = false;
    }

    /**
     * 事务回滚
     * @throws CoreException
     */
    public static function rollback()
    {
        $dbInstance = self::getInstance(self::DB_NODE_MASTER_KEY);
        $dbInstance->rollback();
        self::$isTransacting = false;
    }

    /**
     * 单条插入
     * @param array $row 一维数组
     * $row示例:
     * [
     *     'name' => '苍老师',
     *     'age' => 10
     * ]
     * @return int 影响行数
     * @throws CoreException
     */
    public static function insert(array $row)
    {
        if (empty($row)) {
            return true;
        }
        $fields     = array_keys($row);
        $bindFields = array_map(function ($v) {
            return ':' . $v;
        }, $fields);

        $sql        = 'insert into `' . static::$table . '` (`' . implode('`,`', $fields) . '`) values (' . implode(',', $bindFields) . ')';

        return self::doSql(self::DB_NODE_MASTER_KEY, $sql, $row);
    }

    /**
     * 批量插入
     * @param array $rows 二维数组
     * $rows示例:
     * [
     *     [
     *         'name' =>' 苍老师',
     *         'age' => 10
     *     ],
     *     [
     *         'name' => '苍老师',
     *         'age' => 10
     *     ]
     * ]
     * @return bool
     * @throws CoreException
     */
    public static function insertBatch(array $rows)
    {
        if (empty($rows)) {
            return true;
        }
        $firstData  = $rows[0];
        $countFirstData = count($firstData);
        $fields     = array_keys($firstData);
        $bindFields = array_map(function () {
            return '?';
        }, $fields);
        $sql = 'insert into `' . static::$table . '` (`' . implode('`,`', $fields) . '`) values ';
        $bindArr = [];
        // 拼接values
        foreach ($rows as $row) {
            if (count($row) != $countFirstData) {
                throw new CoreException('baseModel|wrong_insert_data_number');
            }
            // 拼接sql
            $sql .= '(' . implode(',', $bindFields) . '),';
            // 按序获取绑定参数
            $bindArr = array_merge(array_values($row), $bindArr);
        }
        // 去除多余逗号
        $sql = rtrim($sql, ',');
        return self::doSql(Db::DB_NODE_MASTER_KEY, $sql, $bindArr);
    }


    /**
     * 删除数据
     * @param array $where 查询条件
     * @return int 影响行数
     * @throws CoreException
     */
    public static function delete(array $where)
    {
        if (empty($where)) {
            throw new CoreException('baseModel|empty_delete_where');
        }

        $sql = 'delete from `' . static::$table . '`';

        $where = self::prepareWhere($where);

        if (!empty($where['where'])) {
            $sql .= ' where ' . $where['where'];
        }
        return self::doSql(self::DB_NODE_MASTER_KEY, $sql, $where['bind']);
    }

    /**
     * 查询数据
     * @param array $fields 需要查询的字段,默认查询所有的字段
     * @param array $where 查询条件
     * @param array $otherOption limit | group by | order by 等操作
     * @return array 数据
     * @throws CoreException
     */
    public static function select(array $fields = [], array $where = [], array $otherOption = [])
    {
        if (!empty($where)) {
            $where = self::prepareWhere($where);
        }
        if (!empty($otherOption)) {
            $otherOption = self::prepareOption($otherOption);
        }
        if (empty($fields)) {
            $fields = ['*'];
        } else {
            $fields = array_unique($fields);
        }
        $fieldStr = '`' . implode('`,`', $fields) . '`';
        $sql = 'select ' . $fieldStr . ' from `' . static::$table . '`';
        if (!isset($where['where']) && !empty($where['where'])) {
            $sql .= ' where ' . $where['where'];
        }
        if (!empty($otherOption)) {
            $sql .= ' ' . $otherOption;
        }
        return self::doSql(self::DB_NODE_SLAVE_KEY, $sql, $where['bind']);
    }

    /**
     * 更新数据
     * @param array $params 更新的数据
     * @param array $where 被更新的记录
     * @return int 影响行数
     * @throws CoreException
     */
    public static function update(array $params, array $where)
    {
        if (empty($where)) {
            throw new CoreException('baseModel|empty_update_where');
        }
        $where = self::prepareWhere($where);
        $params = array_unique($params);
        $settingBinds = array_map(function ($k) {
            return '`' . $k . '`=:' . $k;
        }, array_keys($params));
        $sql = 'update `' . static::$table . '` set ' . implode(',', $settingBinds) . ' where ' . $where['where'];
        return self::doSql(self::DB_NODE_MASTER_KEY, $sql, array_merge($params, $where['bind']));
    }


    /**
     *
     * 处理where条件
     * @param array $condition 条件数组
     * @return array
     * [
     *     'where' => '...'
     *     'bind' => []
     * ]
     */
    public static function prepareWhere(array $condition)
    {
        if (empty($condition)) {
            return [
                'where' => '',
                'bind' => []
            ];
        }
        $whereArr = [];
        $bind = [];
        foreach ($condition as $field => $val) {
            // 当$field为数字的时候支持 a=1 or b=1 这种自定义查询
            if (is_int($field) && !empty($val)) {
                $whereArr[] = '(' . $val . ')';
                continue;
            }

            if (is_array($val)) {
                // 检测是否为有操作符行为
                if (in_array(key($val), self::$operations, true)) {
                    $i = 0;
                    foreach ($val as $operation => $item_val) {
                        $whereArr[] = sprintf('`%s` ' . $operation . ' :%s%d', $field, $field, $i);
                        $bind[$field . $i] = $item_val;
                        $i++;
                    }
                } elseif (!empty($val)) {
                    $params = array_unique($val);

                    $i = 0;
                    $whereNo = [];
                    $bindNo = [];
                    foreach ($params as $key => $param) {
                        $whereNo[] = sprintf(':%s%d', $key, $i);
                        $bindNo[sprintf('%s%d', $key, $i)] = $param;
                        $i++;
                    }

                    $whereArr = sprintf('`%s` %s (%s)', $key, 'in', implode(', ', $whereNo));
                    $bind = array_merge($bind, $bindNo);
                }
            } else {
                $whereArr[] = sprintf('`%s` = :%s', $field, $field);
                $bind[$field] = $val;
            }
        }

        $whereArr = array_filter($whereArr);
        return [
            'where' => implode(' AND ', $whereArr),
            'bind' => $bind,
        ];
    }


    /**
     * 特殊选项处理
     * @param array $options
     * @return string
     */
    public static function prepareOption(array $options)
    {
        $optionArr = [];
        if (!empty($options['group'])) {
            if (is_array($options['group'])) {
                $group = implode(', ', $options['group']);
            } else {
                $group = $options['group'];
            }
            $optionArr[] = 'group by ' . $group;
        }
        if (!empty($options['order'])) {
            if (is_array($options['order'])) {
                $orders = [];
                foreach ($options['order'] as $sortField => $sort_type) {
                    if ($sortField && $sort_type) {
                        $orders[] = ' ' . $sortField . ' ' . $sort_type . ' ';
                    }
                }
                if ($orders) {
                    $optionArr[] = 'order by ' . implode(', ', $orders);
                }
            } else {
                $optionArr[] = $options['order'];
            }
        }
        if (!empty($options['limit'])) {
            if (is_array($options['limit'])) {
                $optionArr[] = Page::getLimitString($options['limit']);
            } else {
                $optionArr[] = $options['limit'];
            }
        }

        return implode(' ', $optionArr);
    }
}