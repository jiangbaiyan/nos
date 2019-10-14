<?php
/**
 * 文件操作类
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2019-10-14
 * Time: 22:05
 */

namespace Nos\Comm;

class File
{

    /*
     * 文件名
     */
    private $fileName = '';

    /*
     * 文件类型
     */
    private $mimeType = '';

    /*
     * 文件大小
     */
    private $size = 0;

    /*
     * 文件路径
     */
    private $path = '';

    /**
     * 构造函数
     * @param array $fileArr
     */
    public function __construct(array $fileArr)
    {
        $this->fileName = $fileArr['name'] ?? '';
        $this->mimeType = $fileArr['type'] ?? '';
        $this->path     = $fileArr['tmp_name'] ?? '';
        $this->size     = $fileArr['size'] ?? 0;
    }

    /**
     * 获取文件路径
     * @return mixed|string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * 获取文件名
     * @return mixed|string
     */
    public function getName()
    {
        return $this->fileName;
    }

    /**
     * 获取扩展名
     * @return mixed
     */
    public function getExtension()
    {
        $nameArr = explode('.', $this->getName());
        return end($nameArr);
    }

    /**
     * 获取文件类型
     * @return mixed
     */
    public function getType()
    {
        return $this->getType();
    }

    /**
     * 获取文件大小
     * @return int|mixed
     */
    public function getSize()
    {
        return $this->size;
    }
}