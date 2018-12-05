# Nos—基于yaf专注于接口开发的高性能框架
## 控制器使用示例
### 路由：http://localhost/common/test
#### 注意：类名必须是一级目录_二级目录_...文件名Controller，必须继承BaseController，具体流程可参考BaseController
```php
<?php

use Nos\Http\Request;
use Nos\Http\Response;
use Nos\Comm\Validator;

class Common_TestController extends BaseController {

    private $needAuth = true;
    
    private $testModel;

    public function checkParam(){
        $this->params['phone'] = Request::param('phone');
        Validator::phone($this->params['phone']);
    }

    protected function loadModel()
    {
        $this->testModel = new \Common\TestModel();
    }

    public function indexAction()
    {
        $data = $this->testModel->getData();
        $this->output = $data;
        Response::apiSuccess($this->output);
    }

}
```
## model层使用示例：
注意：类名必须为文件名Model，如果有目录必须加上namespace
```php
<?php

namespace Common;

use Nos\Comm\Db;

class TestModel{

    public function getData(){
        $sql = 'select * from test';
        $data = Db::fetchAll($sql);
        return $data;
    }

}
```
## 业务配置
 - 所有和业务场景有关的配置均写在application/config目录下
 - 调用Nos\Comm\Config::get($key)去获取配置信息
 - 注意参数名的格式：文件名.key1.key2）目前只支持到二维数组
 - 全局配置请写到application.ini中即可
## 异常处理：
 - 框架内部定义了6种异常，分别对应不同的默认状态码和提示信息。
 - 抛出异常之后，框架会自动路由到Error.php
 - Error.php会做两个动作：写日志、返回json
 - 返回的json依据抛异常时的状态码和提示信息
## composer：
 - 如果需要引入库，请直接编辑composer,json
 - 执行composer update即可，框架会自动引入