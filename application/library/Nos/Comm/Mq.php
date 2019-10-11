<?php
/**
 * 队列操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-03
 * Time: 10:28
 */

namespace Nos\Comm;

use Nos\Exception\CoreException;

class Mq{

    /**
     * 入队
     * @param string $key
     * @param mixed $data
     * @return bool
     * @throws CoreException
     */
    public static function enQueue(string $key, $data){
        if (empty($key) || empty($data)){
            Log::fatal('mq|empty_key_or_data|key' . $key . '|data:' . json_encode($data));
            throw new CoreException();
        }
        $key = strtolower(trim($key));
        if (!is_string($data)){
            $data = json_encode($data);
        }
        try{
            $res = Redis::lpush($key, $data);
            if ($res){
                Log::notice('mq|push_mq_succ|data:' . json_encode($data) . '|key:' . $key);
                return true;
            }
        } catch (\Exception $e){
            Log::fatal('mq|push_mq_failed|msg:' . json_encode($e->getMessage()) . '|key:' . $key . '|data:' . json_encode($data));
            throw new CoreException();
        }
        return false;
    }


    /**
     * 出队
     * @param string $key
     * @return bool
     * @throws CoreException
     */
    public static function deQueue(string $key){
        if (empty($key)){
            Log::fatal('mq|empty_key|key:' . $key);
            throw new CoreException();
        }
        try{
            $data = Redis::rpop($key);
            if (!empty($data)){
                Log::notice('mq|mq_pop_succ|data:' . json_encode($data) . '|key:' . $key);
                return $data;
            }
        } catch (\Exception $e){
            Log::fatal('mq|rpop_failed|msg:' . json_encode($e->getMessage()) . '|key:' . $key);
            throw new CoreException();
        }
        return false;
    }
}