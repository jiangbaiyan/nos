<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-27
 * Time: 11:52
 */

class IndexController extends Yaf_Controller_Abstract{

    public function indexAction(){
        $this->getView()->assign('content','hello world');
    }


}