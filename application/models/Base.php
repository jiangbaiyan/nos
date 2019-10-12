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
use Nos\Comm\Page;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;

class BaseModel{

    /**
     * 表名
     * @var
     */
    protected $table;

    private $operations = [
        '>', '<', '>=', '<=', '!=', 'like'
    ];

    /**
     * 是否开启事务
     * @var bool
     */
    private static $is_transacting = false;

    /**
     * 事务开始
     * @throws CoreException
     */
    public function beginTransaction()
    {
        $connection = Db::connect();
        $connection->begin();
        self::$is_transacting = true;
    }

    /**
     * 事务提交
     * @throws CoreException
     */
    public function commit()
    {
        $connection = Db::connect();
        $connection->commit();
        self::$is_transacting = false;
    }

    /**
     * 回滚
     * @throws CoreException
     */
    public function rollback()
    {
        $connection = Db::connect();
        $connection->rollback();
        self::$is_transacting = false;
    }

    /**
     * 插入数据
     * @param array $row 如:['name'=>'苍老师', 'age'=>10]
     * @return int
     * @throws \Exception $e
     */
    public function insert(array $row)
    {
        $fields      = array_keys($row);
        $bind_fields = array_map(function ($v) {
            return ':' . $v;
        }, $fields);

        $sql        = 'insert into `' . $this->table . '` (`' . implode('`,`', $fields) . '`) values (' . implode(',', $bind_fields) . ')';
        try {
            return Db::doSql(Db::DB_NODE_MASTER_KEY,$sql, $row);
        } catch (\Exception $e) {
            throw new CoreException();
        }
    }


    /**
     * 删除数据
     * @param string $where
     * @param array  $bind_params 如:['id'=>12]
     * @return int
     * @throws \Exception
     */
    public function delete(string $where, array $bind_params)
    {
        $sql        = 'delete from `' . $this->table . '`';

        if ($where) {
            $sql .= ' where ' . $where;
        } else {
            throw new CoreException();
        }
        try {
            return Db::doSql(Db::DB_NODE_MASTER_KEY,$sql,$bind_params);
        } catch (\Exception $e) {
            throw new CoreException();
        }
    }

    /**
     * 查询数据
     * @param array  $fields 需要查询的字段,默认查询所有的字段
     * @param string $where
     * @param array  $bind_params
     * @param string $other_option limit | group by | order by 等操作
     * @return array
     * @throws \Exception
     */
    public function select(array $fields = [], string $where = '', array $bind_params = [], string $other_option = '')
    {
        if (empty($fields)) {
            $fields = ['*'];
        } else {
            $fields = array_unique($fields);
        }
        if (empty($fields)) {
            throw new CoreException();
        }
        $field_str = '`' . implode('`,`', $fields) . '`';
        $sql = 'select ' . $field_str . ' from `' . $this->table . '`';
        if (!empty($where)) {
            $sql .= ' where ' . $where;
        }
        if ($other_option) {
            $sql .= ' ' . $other_option;
        }
        try {
            return Db::doSql(Db::DB_NODE_SLAVE_KEY,$sql,$bind_params);
        } catch (\Exception $e) {
            throw new CoreException();
        }
    }

    /**
     * 更新数据
     * @param array  $params
     * @param string $where
     * @param array  $where_binds
     * @return int
     * @throws \Exception
     */
    public function update(array $params, string $where, array $where_binds)
    {
        $start_time = microtime(true);
        if (empty($where)) {
            throw new CoreException();
        }
        $params = array_unique($params);
        $setting_binds = array_map(function ($k) {
            return '`' . $k . '`=:' . $k;
        }, array_keys($params));
        $sql           = 'update `' . $this->table . '` set ' . implode(',', $setting_binds) . ' where ' . $where;
        try {
            return Db::doSql(Db::DB_NODE_MASTER_KEY,$sql,array_merge($params, $where_binds));
        } catch (\Exception $e) {
            throw new CoreException();
        }
    }

    /**
     * 处理where条件
     * @param array $condition 条件数组
     * @return array
     */
    public function prepareWhere(array $condition)
    {
        $where_arr = [];
        $bind = [];
        if (empty($condition) || !is_array($condition)) {
            return [
                'where' => '',
                'bind' => []
            ];
        }
        foreach ($condition as $field => $val) {
            // 当$field为数字的时候支持 a=1 or b=1 这种自定义查询
            if (is_int($field) && !empty($val)) {
                $where_arr[] = '(' . $val . ')';
                continue;
            }

            if (is_array($val)) {
                // 检测是否为有操作符行为
                if (in_array(key($val), $this->operations, true)) {
                    $i = 0;
                    foreach ($val as $operation => $item_val) {
                        $where_arr[] = sprintf('`%s` ' . $operation . ' :%s%d', $field, $field, $i);
                        $bind[$field . $i] = $item_val;
                        $i++;
                    }
                } elseif (!empty($val)) {
                    $params = array_unique($val);

                    $i = 0;
                    $where_no = [];
                    $bind_no = [];
                    foreach ($params as $key => $param) {
                        $where_no[] = sprintf(':%s%d', $key, $i);
                        $bind_no[sprintf('%s%d', $key, $i)] = $param;
                        $i++;
                    }

                    $where_arr = sprintf('`%s` %s (%s)', $key, 'in', implode(', ', $where_arr));
                    $bind = array_merge($bind, $where_no['bind']);
                }
            } else {
                $where_arr[] = sprintf('`%s` = :%s', $field, $field);
                $bind[$field] = $val;
            }

            $where_arr = array_filter($where_arr);
            return array(
                'where' => implode(' AND ', $where_arr),
                'bind' => $bind,
            );
        }
    }

    /**
     * @param array $options
     * @return string
     */
    public function prepareOption(array $options)
    {
        $option_arr = [];
        if (!empty($options['group'])) {
            if (is_array($options['group'])) {
                $group = implode(', ', $options['group']);
            } else {
                $group = $options['group'];
            }
            $option_arr[] = 'group by ' . $group;
        }
        if (!empty($options['order'])) {
            if (is_array($options['order'])) {
                $orders = [];
                foreach ($options['order'] as $sort_field => $sort_type) {
                    if ($sort_field && $sort_type) {
                        $orders[] = ' ' . $sort_field . ' ' . $sort_type . ' ';
                    }
                }
                if ($orders) {
                    $option_arr[] = 'order by ' . implode(', ', $orders);
                }
            } else {
                $option_arr[] = $options['order'];
            }
        }
        if (!empty($options['limit'])) {
            if (is_array($options['limit'])) {
                $option_arr[] = Page::getLimitString($options['limit']);
            } else {
                $option_arr[] = $options['limit'];
            }
        }

        return implode(' ', $option_arr);
    }

    /**
     * @param string $sql
     * @param array $bind
     * @return mixed
     * @throws CoreException
     */
    public function selectForJoin(string $sql, array $bind = [])
    {
        return Db::doSql(self::DB_NODE_MASTER_KEY, $sql, $bind);
    }
}