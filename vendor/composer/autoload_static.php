<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0a2343baf2a47a776d5eab0d8cda445d
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'Nos\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Nos\\' => 
        array (
            0 => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src',
        ),
    );

    public static $classMap = array (
        'Nos\\Comm\\Config' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Comm/Config.php',
        'Nos\\Comm\\Db' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Comm/Db.php',
        'Nos\\Comm\\File' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Comm/File.php',
        'Nos\\Comm\\Log' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Comm/Log.php',
        'Nos\\Comm\\Mq' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Comm/Mq.php',
        'Nos\\Comm\\Page' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Comm/Page.php',
        'Nos\\Comm\\Redis' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Comm/Redis.php',
        'Nos\\Comm\\Validator' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Comm/Validator.php',
        'Nos\\Exception\\CoreException' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Exception/CoreException.php',
        'Nos\\Exception\\OperateFailedException' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Exception/OperateFailedException.php',
        'Nos\\Exception\\ParamValidateFailedException' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Exception/ParamValidateFailedException.php',
        'Nos\\Exception\\PermissionDeniedException' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Exception/PermissionDeniedException.php',
        'Nos\\Exception\\ResourceNotFoundException' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Exception/ResourceNotFoundException.php',
        'Nos\\Exception\\UnauthorizedException' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Exception/UnauthorizedException.php',
        'Nos\\Http\\Request' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Http/Request.php',
        'Nos\\Http\\Response' => __DIR__ . '/..' . '/jiangbaiyan/nos-framework/src/Http/Response.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0a2343baf2a47a776d5eab0d8cda445d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0a2343baf2a47a776d5eab0d8cda445d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0a2343baf2a47a776d5eab0d8cda445d::$classMap;

        }, null, ClassLoader::class);
    }
}
