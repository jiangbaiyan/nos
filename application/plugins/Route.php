<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-12
 * Time: 09:39
 */

use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

class RoutePlugin extends Plugin_Abstract
{
    public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
    {
        $request->controller = str_replace('/', '_', substr($request->getRequestUri(), 1));
    }
}