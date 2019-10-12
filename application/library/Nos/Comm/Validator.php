<?php
/**
 * 常用参数验证类，可自行新建方法自定义验证规则，注意方法名要和验证规则名称一致
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 16:57
 */

namespace Nos\Comm;

use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;

class Validator{

    /**
     * 请求参数校验入口方法
     * @param array $params
     * @param array $rules
     * @throws CoreException
     */
    public static function make(array $params, array $rules)
    {
        foreach ($rules as $k => $v){
            if (!isset($params[$k]) && strpos($v, 'required') === false){
                continue;
            }
            $arr = explode('|', $v);
            foreach ($arr as $item){
                if (empty($item)){
                    Log::fatal('validator|rule_is_empty');
                    throw new CoreException();
                }
                if (!method_exists(__CLASS__, $item)){
                    Log::fatal('validator|rule_not_defined|rule:' . $item);
                    throw new CoreException();
                }
                @call_user_func(array(__CLASS__, $item), $params[$k]);
            }
        }
    }

    /**
     * 手机号验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function phone($v)
    {
        if (strlen($v) != 11 || !preg_match('/^[1][3,4,5,7,8][0-9]{9}$/
', $v)){
            throw new ParamValidateFailedException();
        }
    }

    /**
     * 邮箱验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function email($v)
    {
        if (!preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/', $v)){
            throw new ParamValidateFailedException();
        }
    }

    /**
     * 身份证验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function idCard($v)
    {
        if (!preg_match('/(^([\d]{15}|[\d]{18}|[\d]{17}x)$)/', $v)){
            throw new ParamValidateFailedException();
        }
    }

    /**
     * 必填验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function required($v)
    {
        if (!isset($v)){
            throw new ParamValidateFailedException();
        }
    }

    /**
     * 判断日期时间格式是否合法
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function dateTime($v)
    {
        if (strtotime(date('Y-m-d H:i:s', strtotime($v))) != strtotime($v)){
            throw new ParamValidateFailedException();
        }
    }

    /**
     * 判断日期格式是否合法
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function date($v)
    {
        if (strtotime(date('Y-m-d H:i:s', strtotime($v))) != strtotime($v)){
            throw new ParamValidateFailedException();
        }
    }
}