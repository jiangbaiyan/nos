<?php
/**
 * 短信验证码
 * Created by PhpStorm.
 * User: baiyanzzz
 * Date: 2018-11-28
 * Time: 15:37
 */

class Common_Sms_Controller extends Comm_Control{

    /**
     * @throws Exception_ParamValidateFailed
     */
    public function checkParam(){
        $this->params['phone'] = $this->get('phone');
        Comm_Validator::phone($this->params['phone']);
    }


    /**
     * @throws Exception_OperateFailed
     */
    public function indexAction()
    {
        $this->output['phone'] = $this->params['phone'];
        $this->responseSuccess();
    }

}