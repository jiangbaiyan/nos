<?php
/**
 * Created by PhpStorm.
 * User: guobutao001
 * Date: 2018/11/30
 * Time: 10:15
 */

use Yaf\Application;
use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Registry;

class Bootstrap extends Bootstrap_Abstract
{
    public function _initLoader(Dispatcher $dispatcher)
    {
        //加载composer
        //include_once ROOT_PATH . '/vendor/autoload.php';
    }

    public function _initRequest(Dispatcher $dispatcher)
    {

    }

    public function _initConfig(Dispatcher $dispatcher)
    {
        $config = Application::app()->getConfig();
        Registry::set('log_use_buffer', (boolean) $config->get('log.use_buffer'));
        Registry::set('config', $config);
    }

    public function _initAuthority(Dispatcher $dispatcher)
    {
        try {
            if (!Authority::checkIp()) {
                throw new Exception('authority_ip_forbidden');
            }

            #token校验
            #if (!Authority::validateAuth()) {
            #   throw new Exception('authority_checksign_failed');
            #}
        }catch (\Exception $exception) {
            Log::error('Exception:' . $exception->getMessage(), [
                'line'               => $exception->getLine(),
                'file'               => $exception->getFile(),
                'http_authorization' => Server::get('http_authorization'),
            ], 'error');


            $requestId = Registry::get('request_id');
            headers_sent();
            header('Content-Type: application/json; charset=utf-8;');
            header("Request-ID: $requestId");
            echo json_encode(
                [
                    'request_id' => $requestId,
                    'errno'      => $exception->getCode() ?? 0,
                    'error'      => $exception->getMessage() ?? '',
                    'data'       => new \stdClass(),
                    'cost'       => ceil((microtime(true) - $_SERVER['request_time_float']) * 1000),
                ],
                true
            );
            Log::info('input', $_REQUEST, 'request');
            die;
        }
    }

    public function _initPlugin(Dispatcher $dispatcher)
    {
        //$dispatcher->registerPlugin(new Default_Plugin());
    }

    public function _initView(Dispatcher $dispatcher)
    {
        Dispatcher::getInstance()->disableView();
    }
}
