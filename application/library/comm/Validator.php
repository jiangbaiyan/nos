<?php
/**
 * 常用参数验证类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 16:57
 */

class Comm_Validator{

    /**
     * 手机号验证
     * @param $phone
     * @throws Exception_ParamValidateFailed
     */
    public static function phone($phone){
        if (empty($phone) || strlen($phone) != 11 || !preg_match('/1\d{10}/', $phone)){
            throw new Exception_ParamValidateFailed('手机号格式不正确');
        }
    }

}