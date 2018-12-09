<?php
/**
 * 日志操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 12:05
 */

namespace Nos\Comm;

class Log{

    /*
     * 日志级别由高到低
     */
    const LEVEL_FATAL  = 'FATAL';
    const LEVEL_NOTICE = 'NOTICE';
    const LEVEL_DEBUG  = 'DEBUG';

    const PREFIX = 'nos_';

    /**
     * 写日志
     * @param $level
     * @param $msg
     */
    private static function writeLog($level, $msg){
        $fileName = self::PREFIX . date('Y-m-d') . '.log';//按天划分
        $path = APP_PATH . '/logs/' . $fileName;
        $time = date('Y-m-d H:i:s');
        if (!is_string($msg)){
            $msg = json_encode($msg);
        }
        $str = '[' . $time . ']' . "[{$level}]". $msg . PHP_EOL;
        $handle = fopen($path, 'a');
        flock($handle, LOCK_EX|LOCK_NB);
        fwrite($handle, $str);
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    /**
     * 严重错误日志
     * @param $msg
     */
    public static function fatal($msg){
        self::writeLog(self::LEVEL_FATAL, $msg);
    }

    /**
     * 一般错误日志
     * @param $msg
     */
    public static function notice($msg){
        self::writeLog(self::LEVEL_NOTICE, $msg);
    }

    /**
     * 调试日志
     * @param $msg
     */
    public static function debug($msg){
        self::writeLog(self::LEVEL_DEBUG, $msg);
    }

}