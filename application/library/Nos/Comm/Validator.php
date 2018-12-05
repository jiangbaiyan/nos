<?php
/**
 * 常用参数验证类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 16:57
 */

namespace Nos\Comm;

use Nos\Exception\ParamValidateFailedException;

class Validator{

    /**
     * 手机号验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    public static function phone($v){
        if (empty($v) || strlen($v) != 11 || !preg_match('/^[1][3,4,5,7,8][0-9]{9}$/;
', $v)){
            throw new ParamValidateFailedException('手机号格式不正确');
        }
    }

    /**
     * 邮箱验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    public static function email($v){
        if (empty($v) || !preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/', $v)){
            throw new ParamValidateFailedException('邮箱格式不正确');
        }
    }

    /**
     * 身份证验证
     * @param $v
     * @throws ParamValidateFailedException
     */
    public static function idCard($v){
        if (empty($v) || !preg_match('/(^([\d]{15}|[\d]{18}|[\d]{17}x)$)/', $v)){
            throw new ParamValidateFailedException('身份证格式不正确');
        }
    }

}