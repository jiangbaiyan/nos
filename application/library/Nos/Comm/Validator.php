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
     * @param $params
     * @param $rules
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public static function make($params, $rules){
        if (!is_array($params) || !is_array($rules)){
            Log::fatal('validator|illegal_params_or_rules|params:' . json_encode($params) . '|rules:' . json_encode($rules));
            throw new CoreException('请求参数格式或校验规则不合法');
        }
        foreach ($rules as $k => $v){
            self::required($params[$k]);
            $arr = explode('|', $v);
            foreach ($arr as $item){
                if (empty($item)){
                    Log::fatal('validator|rule_is_empty');
                    throw new CoreException('校验规则不能为空');
                }
                if (!method_exists(__CLASS__, $item)){
                    Log::fatal('validator|rule_not_defined|rule:' . $item);
                    throw new CoreException('校验规则未定义');
                }
                call_user_func(array(__CLASS__, $item), $params[$k]);
            }
        }
    }

    /**
     * 手机号验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function phone($v){
        if (strlen($v) != 11 || !preg_match('/^[1][3,4,5,7,8][0-9]{9}$/
', $v)){
            throw new ParamValidateFailedException('手机号格式不正确');
        }
    }

    /**
     * 邮箱验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function email($v){
        if (!preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/', $v)){
            throw new ParamValidateFailedException('邮箱格式不正确');
        }
    }

    /**
     * 身份证验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function idCard($v){
        if (!preg_match('/(^([\d]{15}|[\d]{18}|[\d]{17}x)$)/', $v)){
            throw new ParamValidateFailedException('身份证格式不正确');
        }
    }

    /**
     * 必填验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    private static function required($v){
        if (!isset($v)){
            throw new ParamValidateFailedException('参数缺失');
        }
    }

}