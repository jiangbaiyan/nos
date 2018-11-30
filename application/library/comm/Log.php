<?php
/**
 * 日志操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 12:05
 */

namespace comm;
class Log{

    const
        LEVEL_FATAL = 0,
        LEVEL_NOTICE = 1;

    const PREFIX = 'yake_';

    /**
     * 写日志
     * @param int $level
     * @param $msg
     */
    private static function writeLog(int $level, $msg){
        $fileName = self::PREFIX . date('Y-m-d') . '.log';//按天划分
        $path = APP_PATH . '/log/' . $fileName;
        $time = date('Y-m-d H:i:s');
        if (!is_string($msg)){
            $msg = json_encode($msg);
        }
        $str = '';
        switch ($level){
            case self::LEVEL_NOTICE:
                $str = '[' . $time . ']' . '[' . 'NOTICE' . ']'. $msg . PHP_EOL;
                break;
            case self::LEVEL_FATAL:
                $str = '[' . $time . ']' . '[' . 'FATAL' . ']'. $msg . PHP_EOL;
                break;
        }
        file_put_contents($path, $str, FILE_APPEND);
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

}