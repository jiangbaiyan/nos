# Nos—基于Yaf && 专注于接口开发的PHP框架
### 在yaf框架基础上为中小型接口开发业务场景做了适配
### 示例项目：https://github.com/jiangbaiyan/bangnos
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
 - 在使用前请确保安装了yaf、pdo、redis、curl等PHP扩展
 - 最好在PHP7环境下使用，本框架全程在PHP7环境下测试通过
 - yaf框架文档请参阅：http://php.net/manual/en/book.yaf.php
 - 配置nginx等服务器rewrite到/public/index.php入口文件即可
 - 请赋予logs目录足够的权限，否则无法正常写入日志
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
## 接下来的计划  
  - 整合swoole部分优良特性
  - 接口文档自动生成
  - 添加连接池
  - 数据库迁移
  - 多进程离线脚本处理
  - ...
## 题外话
  欢迎大家提出问题，也渴望大家能提出好的建议；新人初来驾到，望各位多多指教
    
