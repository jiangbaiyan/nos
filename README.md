# Nos—基于Yaf && 专注于接口开发的PHP框架
<a href="https://packagist.org/packages/jiangbaiyan/nos"><img src="https://poser.pugx.org/jiangbaiyan/nos/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/jiangbaiyan/nos"><img src="https://poser.pugx.org/jiangbaiyan/nos/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/jiangbaiyan/nos"><img src="https://poser.pugx.org/jiangbaiyan/nos/v/unstable" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/jiangbaiyan/nos"><img src="https://poser.pugx.org/jiangbaiyan/nos/license" alt="License"></a>
### 在yaf框架基础上为中小型接口开发业务场景做了适配
## 安装
 - 支持composer直接安装，命令行执行：composer create-project --prefer-dist jiangbaiyan/nos
 - PHP版本 >= 7.0
 - 安装yaf、pdo、curl扩展
 - 在php.ini中添加配置项
   - yaf.use_namespace = 1;
   - yaf.environ = product（默认为生产环境）或yaf.environ = dev（测试环境）
 - 赋予logs目录读写权限
 - 若要自行编写工具类库，可到application/library目录下编写
 - 配置nginx等服务器rewrite到/public/index.php入口文件
```nginx
server {
  listen ****;
  server_name  domain.com;
  root   document_root;
  index  index.php index.html index.htm;

  if (!-e $request_filename) {
    rewrite ^/(.*)  /index.php/$1 last;
  }
}
```
## Controller层使用
### 命名规范
 - 路由：http://localhost/article/query
 - 目录：controllers/Article/Query.php
 - 类名：Article_QueryController
 - 注意路由和文件命名规则要相同，类名必须是一级目录_二级目录_...文件名Controller，必须继承BaseController并实现相应抽象方法indexAction()
```php
<?php

use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;
use Nos\Comm\Validator;
use Common\TestModel;

class Article_QueryController extends BaseController
{
    /**
     * 业务逻辑
     * @throws CoreException
     * @throws ParamValidateFailedException
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
    public static $table = 'test';

    /**
     * 模型层查询示例
     * @return mixed
     * @throws \Exception
     */
    public function getData()
    {
        // 要更新的数据
        $params = [
            'name' => 'grapes'
        ];
        // 条件过滤
        $wheres = [
            'id'   => 222
        ];
        // 附加选项
        $option = [
            'id'  => 'asc'
        ];
        // 更新操作
        $row = self::update($params, $wheres);
        // 查询操作
        $data = self::select(['id','name'], $wheres, $option);
        // 返回数据
        return $data;
    }

}
```
## 异常处理
 - 框架内部定义了6种异常，分别对应不同的默认状态码和提示信息
 - 抛出异常后，框架会自动路由到Error.php
 - Error.php会做两件事：写日志、返回json
 - 异常返回的json内容依赖抛异常时的状态码和提示信息
## 依赖引入
 - 如果需要引入库，请直接编辑composer.json并添加需要的库
 - 然后执行composer install/update即可