<?php
/**
 * 响应操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-02
 * Time: 11:31
 */

namespace Nos\Http;

class Response
{

    /**
     * @var string 成功状态码
     */
    const CODE_SUCCESS = 200;

    /**
     * @var string 失败状态码
     */
    const CODE_PARAM_ERROR       = 400; // 未授权

    const CODE_UNAUTHORIZED       = 401; // 未授权（未登录）

    const CODE_OPERATE_FAILED     = 402; // 操作失败

    const CODE_PERMISSION_DENIED  = 403; // 无权限

    const CODE_RESOURCE_NOT_FOUND = 404; // 资源未找到

    const CODE_CORE_ERROR         = 500; // 框架底层错误

    /**
     * @var string 提示信息
     */
    const MSG_SUCCESS            = 'success';

    const MSG_OPERATE_FAILED     = '操作失败';

    const MSG_RESOURCE_NOT_FOUND = '请求资源未找到';

    const MSG_PARAM_ERROR        = '参数缺失或参数错误';

    const MSG_UNAUTHORIZED       = '您还未拥有授权,请重新登录';

    const MSG_PERMISSION_DENIED  = '您没有该操作的权限';

    const MSG_CORE_ERROR         = '框架底层错误';


    /**
     * 接口通用返回
     * @param int $status 状态码
     * @param string $msg 返回信息
     * @param $data
     */
    public static function apiResponse(int $status, string $msg, $data = [])
    {
        ob_clean();
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode([
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data
        ]);
        exit;
    }

    /**
     * 请求成功
     * @param $data
     * @param string $msg
     * @return string
     */
    public static function apiSuccess($data = [] , string $msg = '')
    {
        self::apiResponse(self::CODE_SUCCESS,$msg ? $msg :self::MSG_SUCCESS,$data);
    }

    /**
     * 资源未找到
     * @param string $msg
     * @return string
     */
    public static function apiResourceNotFound(string $msg = '')
    {
        self::apiResponse(self::CODE_RESOURCE_NOT_FOUND,$msg ? $msg : self::MSG_RESOURCE_NOT_FOUND);
    }

    /**
     * 参数缺失
     * @param string $msg
     * @return string
     */
    public static function apiParamValidateFailed(string $msg = '')
    {
        self::apiResponse(self::CODE_PARAM_ERROR,$msg ? $msg :self::MSG_PARAM_ERROR);
    }

    /**
     * 操作失败
     * @param string $msg
     * @return string
     */
    public static function apiOperateFailed(string $msg = '')
    {
        self::apiResponse(self::CODE_OPERATE_FAILED,$msg ? $msg :self::MSG_OPERATE_FAILED);
    }

    /**
     * 用户未授权
     * @param string $msg
     * @return string
     */
    public static function apiUnauthorized(string $msg = '')
    {
        self::apiResponse(self::CODE_UNAUTHORIZED,$msg ? $msg :self::MSG_UNAUTHORIZED);
    }

    /**
     * 已授权，但是没有权限
     * @param string $msg
     * @return string
     */
    public static function apiPermissionDenied(string $msg = '')
    {
        self::apiResponse(self::CODE_PERMISSION_DENIED,$msg ? $msg :self::MSG_PERMISSION_DENIED);
    }

}