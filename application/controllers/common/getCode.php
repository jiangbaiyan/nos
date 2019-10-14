<?php
/**
 * 控制器示例
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-11-28
 * Time: 15:37
 */

use Nos\Exception\CoreException;
use Nos\Http\Request;
use Nos\Http\Response;
use Nos\Comm\Validator;
use Common\TestModel;

class Common_GetCodeController extends BaseController
{
    /**
     * 业务逻辑
     * @throws CoreException
     */
    public function indexAction()
    {
        Validator::make($params = Request::all(), [
            'id'    => 'required',
            'phone' => 'phone|required',
        ]);
        $testModel = new TestModel();
        $data = $testModel->getData();
        Response::apiSuccess($data);
    }

}