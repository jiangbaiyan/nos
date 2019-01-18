# Nos—基于Yaf和Swoole && 专注于接口开发的PHP框架
<a href="https://packagist.org/packages/jiangbaiyan/nos"><img src="https://poser.pugx.org/jiangbaiyan/nos/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/jiangbaiyan/nos"><img src="https://poser.pugx.org/jiangbaiyan/nos/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/jiangbaiyan/nos"><img src="https://poser.pugx.org/jiangbaiyan/nos/v/unstable" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/jiangbaiyan/nos"><img src="https://poser.pugx.org/jiangbaiyan/nos/license" alt="License"></a>
### 适配中小型业务场景
### 示例项目：https://github.com/jiangbaiyan/bangnos
 - Server
   - Swoole Http Server
 - 基本类库
   - 业务配置操作类
   - Db操作类
   - 日志处理类
   - 简单队列类
   - 分页操作类
   - redis操作类
   - 表单验证器类
 - 异常处理
   - 框架底层异常
   - 操作失败异常
   - 参数校验异常
   - 权限不足异常
   - 资源不存在异常
   - 未授权异常
 - 请求响应
   - 发送请求
   - 接收请求参数
   - 返回json响应
## 安装
 - 支持composer直接安装，命令行执行：composer create-project --prefer-dist jiangbaiyan/nos
 - 确保PHP版本>=7.0，本框架全程在PHP7环境下测试通过
 - 安装swoole、yaf、pdo、redis、curl扩展
 - 在php.ini中添加配置项：yaf.use_namespace=1;
 - 赋予logs目录读写权限
 - 确保8811端口没有被占用
 - 启动框架:
```
   cd server
   php http_server.php
```
## Controller层使用
### 控制器执行流程
 - 若$needAuth，执行auth()：接口认证
 - 执行checkParam()：请求参数校验
 - 执行loadModel()：加载模型
 - 执行indexAction()：执行业务逻辑
### 命名规范
 - 路由：http://localhost/common/getCode
 - 目录：controllers/common/getCode.php
 - 类名：Common_GetCodeController
 - 注意路由和文件命名规则要相同，类名必须是一级目录_二级目录_...文件名Controller，必须继承BaseController并实现相应抽象方法
```php
<?php

use Nos\Http\Request;
use Nos\Http\Response;
use Nos\Comm\Validator;

class Common_GetCodeController extends BaseController {

    /**
     * 是否需要登录授权
     * @var bool 
     */
    public $needAuth = true;

    /*
     * 当前登录用户
     */
    public $user;
    
    /*
     * 模型
     */
    private $testModel;

    /*
     * 参数校验
     */
    public function checkParam(){
        Validator::make($this->params = Request::all(), array(
            'id'    => 'required',
            'phone' => 'phone|required',
        ));
        $this->params['phone'] = Request::get('phone');//获取get参数
        $this->params['name']  = Request::post('name');//获取post参数
    }

    /**
     * 加载模型
     */
    public function loadModel()
    {
        $this->testModel = new \Common\TestModel();
    }

    /**
     * 业务逻辑
     */
    public function indexAction()
    {
        $this->output['data'] = $this->testModel->getData();
        Response::apiSuccess($this->output);
    }

}
```
## Model层使用
 - 注意：目录和文件名必须大写。类名必须为：文件名Model，如果有上级目录必须加上namespace，可继承BaseModel
 - 目录：models/Common/Test.php
```php
<?php

namespace Common;

class TestModel extends \BaseModel {

    /*
     * 表名
     */
    public $table = 'test';
    
    /**
     * @return mixed
     * @throws \Nos\Exception\CoreException
     */
    public function getData(){
        $select = array('id', 'name');
        $ext = 'where id = ?';
        $bind = array(2);
        $data = $this->getList($select, $ext, $bind);
        return $data;
    }

}
```
## 异常处理
 - 框架内部定义了6种异常，分别对应不同的默认状态码和提示信息
 - 抛出异常后，框架会自动路由到Error.php
 - Error.php会做两件事：写日志、返回json
 - 异常返回的json内容依赖抛异常时的状态码和提示信息
## 业务配置
 - 所有和业务场景有关的配置均写在/application/config目录下
 - 调用Nos\Comm\Config::get($key)去获取配置信息
 - 注意参数名的格式：(文件名.key1.key2）目前只支持到二维数组
 - 全局配置请写到application.ini中即可
## 依赖引入
 - 如果需要引入库，请直接编辑composer.json
 - 然后执行composer update即可
## 题外话
  欢迎大家提出问题，也渴望大家能提出好的建议；新人初来驾到，望各位多多指教
    
