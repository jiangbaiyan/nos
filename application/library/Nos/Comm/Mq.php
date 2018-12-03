<?php
/**
 * 队列操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-03
 * Time: 10:28
 */

namespace Nos\Comm;

use Nos\Exception\OperateFailedException;

class Mq{

    /**
     * 入队
     * @param $key
     * @param $data
     * @return bool
     * @throws OperateFailedException
     */
    public static function enQueue($key, $data){
        if (empty($key) ||!is_string($key) || empty($data)){
            Log::fatal('mq|empty_key_or_data|key' . $key . '|data:' . json_encode($data));
            throw new OperateFailedException('队列写入失败');
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
            throw new OperateFailedException('入队失败');
        }
        return false;
    }


    /**
     * 出队
     * @param $key
     * @return bool
     * @throws OperateFailedException
     */
    public static function deQueue($key){
        if (empty($key)){
            Log::fatal('mq|empty_key|key:' . $key);
            throw new OperateFailedException('队列数据有误');
        }
        try{
            $data = Redis::rpop($key);
            if (!empty($data)){
                Log::notice('mq|mq_pop_succ|data:' . json_encode($data) . '|key:' . $key);
                return $data;
            }
        } catch (\Exception $e){
            Log::notice('mq|rpop_failed|msg:' . json_encode($e->getMessage()) . '|key:' . $key);
            throw new OperateFailedException('获取数据失败');
        }
        return false;
    }
}