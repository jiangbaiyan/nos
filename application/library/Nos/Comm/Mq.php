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

class Mq
{

    /**
     * 入队
     * @param string $key
     * @param mixed $data
     * @return bool
     * @throws CoreException
     */
    public static function enQueue(string $key, string $data)
    {
        if (empty($key) || empty($data)){
            throw new CoreException('mq|empty_key_or_data|key' . $key . '|data:' . json_encode($data));
        }
        $key = strtolower(trim($key));
        try{
            $res = Redis::getInstance()->lPush($key, $data);
            if ($res){
                Log::notice('mq|push_mq_success|data:' . json_encode($data) . '|key:' . $key);
                return true;
            }
        } catch (\Exception $e){
            throw new CoreException('mq|push_mq_failed|msg:' . json_encode($e->getMessage()) . '|key:' . $key . '|data:' . json_encode($data));
        }
        return false;
    }


    /**
     * 出队
     * @param string $key
     * @return bool
     * @throws CoreException
     */
    public static function deQueue(string $key)
    {
        if (empty($key)){
            throw new CoreException('mq|empty_key|key:' . $key);
        }
        try{
            $data = Redis::getInstance()->rPop($key);
            if (!empty($data)){
                Log::notice('mq|mq_pop_succ|data:' . json_encode($data) . '|key:' . $key);
                return $data;
            }
        } catch (\Exception $e){
            throw new CoreException('mq|rpop_failed|msg:' . json_encode($e->getMessage()) . '|key:' . $key);
        }
        return false;
    }
}