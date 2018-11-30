<?php
/**
 * 配置操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 10:55
 */

namespace Comm;

use Exception\OperateFailedException;
use Exception\ParamValidateFailedException;

class Config{

    /**
     * 获取指定配置项（格式:文件名.key1.key2）
     * @param $key
     * @param string $default
     * @return mixed|string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public static function get($key, $default = ''){
        $pathArr = array();
        if (strpos($key, '.') !== false) {
            $pathArr = explode('.',$key);
            $len = count($pathArr);
            if ($len  > 3){
                throw new ParamValidateFailedException('暂不支持二维以上配置数组的访问');
            }
            $file = $pathArr[0];
        }else{
            $file = $key;
        }
        $file = ROOT_PATH . '/config/' . ucfirst($file) . '.php';
        if (!file_exists($file)){
            throw new OperateFailedException('配置文件不存在');
        }
        $config = include $file;
        if (count($pathArr) == 2){
            $ret = isset($config[$pathArr[1]]) ? $config[$pathArr[1]] : $default;
        } else if (count($pathArr) == 3){
            $ret = isset($config[$pathArr[1]][$pathArr[2]]) ? $config[$pathArr[1]][$pathArr[2]] : $default;
        } else{
            $ret = $config;
        }
        if ($ret == ''){
            return false;
        }
        return isset($ret) ? $ret : $default;
    }

}