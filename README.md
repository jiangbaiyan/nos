# Nos—基于Yaf，专注于接口开发的框架
## 目前处于起步阶段，后续会继续维护并加入新特性
## 安装
 - 支持composer直接安装，命令行执行：composer create-project --prefer-dist jiangbaiyan/nos
 - 在使用前请确保安装了yaf、pdo、redis、curl扩展
 - 详情请参阅：http://php.net/manual/en/book.yaf.php
 - 配置nginx等服务器rewrite到public/index.php入口文件即可
## Controller层使用
### 路由：http://localhost/common/test
#### 注意：类名必须是一级目录_二级目录_...文件名Controller，必须继承BaseController
### 控制器方法执行流程
 - 判断$needAuth是否为true，若为true，执行auth()方法：接口认证
 - 执行checkParam()：请求参数校验
 - 执行loadModel()：加载模型
 - 执行indexAction()：执行业务逻辑
```php
<?php

use Nos\Http\Request;
use Nos\Http\Response;
use Nos\Comm\Validator;

class Common_TestController extends BaseController {

    public $needAuth = false;
    
    private $testModel;

    public function checkParam(){
        Validator::phone($this->params['phone']);
        $this->params['phone'] = Request::param('phone');
    }

    public function loadModel()
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
## Model层使用
注意：类名必须为文件名Model，如果有目录必须加上namespace
```php
<?php

namespace Common;

use Nos\Comm\Db;

class TestModel{

    public function getData(){
        $sql = 'select * from test where id = ?';
        $data = Db::fetchAll($sql, array(2));
        return $data;
    }

}
```
## 业务配置
 - 所有和业务场景有关的配置均写在application/config目录下
 - 调用Nos\Comm\Config::get($key)去获取配置信息
 - 注意参数名的格式：(文件名.key1.key2）目前只支持到二维数组
 - 全局配置请写到application.ini中即可
## 异常处理
 - 框架内部定义了6种异常，分别对应不同的默认状态码和提示信息。
 - 抛出异常之后，框架会自动路由到Error.php
 - Error.php会做两个动作：写日志、返回json
 - 返回的json依据抛异常时的状态码和提示信息
## composer
 - 如果需要引入库，请直接编辑composer,json
 - 执行composer update即可，框架会自动引入