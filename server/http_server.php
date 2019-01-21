<?php
/**
 * swoole http server
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2019-01-17
 * Time: 08:28
 */

use Nos\Comm\Log;
use Nos\Http\Response;
use Yaf\Application;
use Yaf\Request\Http;

class HttpServer {

    public static $instance;

    private $dispatcher;

    public $app;

    public function __construct(){
        $http = new swoole_http_server('0.0.0.0', 8811);
        $http->set(array(
            'max_request' => 10000,
            'dispatch_mode' => 1,
            'daemonize' => 1,
            'worker_num' => 16
        ));
        $http->on('workerStart' , array( $this , 'onWorkerStart'));
        $http->on('request', array($this, 'onRequest'));
        $http->start();
    }

    public function onWorkerStart(){
        define('APP_PATH', dirname(__DIR__));
        define('ROOT_PATH',  APP_PATH . '/application');
        $this->app = new Application(APP_PATH . '/config/application.ini');
        $dispatcher = $this->app->getDispatcher();
        $dispatcher->registerPlugin(new RoutePlugin());
        $dispatcher->catchException('true');
        $dispatcher->disableView();
        include APP_PATH . '/vendor/autoload.php';
    }

    public function onRequest($request, $response){
        $_SERVER = array();
        if (isset($request->server)){
            foreach ($request->server as $k => $v){
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if ($_SERVER['REQUEST_URI'] == '/favicon.ico'){
            $response->status(404);
            return;
        }
        $_GET = array();
        if (isset($request->get)){
            foreach ($request->get as $k => $v){
                $_GET[$k] = $v;
            }
        }
        $_POST = array();
        if (isset($request->post)){
            foreach ($request->post as $k => $v){
                $_POST[$k] = $v;
            }
        }
        $response->header('Content-Type', 'application/json', false);
        try{
            $requestObj = new Http($_SERVER['REQUEST_URI']);
            $this->dispatcher->dispatch($requestObj);
            $result = ob_get_contents();
            ob_end_clean();
            $response->end($result);
        } catch (\Exception $e){
            Log::fatal('swoole|yaf_route_dispatch_error|msg:' . json_encode($e->getMessage()));
            Response::apiCoreError();
            $result = ob_get_contents();
            ob_end_clean();
            $response->end($result);
        }
    }

    public static function getInstance(){
        if (!self::$instance instanceof HttpServer){
            self::$instance = new HttpServer();
        }
        return self::$instance;
    }
}

HttpServer::getInstance();