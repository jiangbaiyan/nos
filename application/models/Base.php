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

class BaseModel extends Db{

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
     * 插入数据
     * @param array $row 如:['name'=>'苍老师', 'age'=>10]
     * @return int 影响行数
     * @throws CoreException
     */
    public static function insert(array $row)
    {
        $fields     = array_keys($row);
        $bindFields = array_map(function ($v) {
            return ':' . $v;
        }, $fields);

        $sql        = 'insert into `' . static::$table . '` (`' . implode('`,`', $fields) . '`) values (' . implode(',', $bindFields) . ')';
        return self::doSql(self::DB_NODE_MASTER_KEY, $sql, $row);
    }


    /**
     * 删除数据
     * @param string $where
     * @param array $bindParams 如:['id'=>12]
     * @return int 影响行数
     * @throws CoreException
     */
    public static function delete(string $where, array $bindParams)
    {
        $sql = 'delete from `' . static::$table . '`';

        if ($where) {
            $sql .= ' where ' . $where;
        } else {
            throw new CoreException('baseModel|empty_delete_where');
        }
        return self::doSql(self::DB_NODE_MASTER_KEY, $sql, $bindParams);
    }

    /**
     * 查询数据
     * @param array $fields 需要查询的字段,默认查询所有的字段
     * @param string $where 查询条件
     * @param array $bindParams 参数绑定
     * @param string $otherOption limit | group by | order by 等操作
     * @return array 数据
     * @throws CoreException
     */
    public static function select(array $fields = [], string $where = '', array $bindParams = [], string $otherOption = '')
    {
        if (empty($fields)) {
            $fields = ['*'];
        } else {
            $fields = array_unique($fields);
        }
        $fieldStr = '`' . implode('`,`', $fields) . '`';
        $sql = 'select ' . $fieldStr . ' from `' . static::$table . '`';
        if (!empty($where)) {
            $sql .= ' where ' . $where;
        }
        if ($otherOption) {
            $sql .= ' ' . $otherOption;
        }
        return self::doSql(self::DB_NODE_SLAVE_KEY, $sql, $bindParams);
    }

    /**
     * 更新数据
     * @param array $params 更新的数据
     * @param string $where 被更新的记录
     * @param array $whereBinds 参数绑定
     * @return int 影响行数
     * @throws CoreException
     */
    public static function update(array $params, string $where, array $whereBinds)
    {
        if (empty($where)) {
            throw new CoreException('baseModel|empty_update_where');
        }
        $params = array_unique($params);
        $settingBinds = array_map(function ($k) {
            return '`' . $k . '`=:' . $k;
        }, array_keys($params));
        $sql = 'update `' . static::$table . '` set ' . implode(',', $settingBinds) . ' where ' . $where;
        return self::doSql(self::DB_NODE_MASTER_KEY, $sql, array_merge($params, $whereBinds));
    }

    /**
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