<?php
/**
 * 分页处理
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2018-12-02
 * Time: 11:21
 */

namespace Nos\Comm;

use Nos\Http\Request;

class Page
{

    /**
     * 生成分页数据
     * @param int $count
     * @param int $curPage
     * @param int $pageSize
     * @return array
     */
    public static function paginate(int $count, int $curPage, int $pageSize)
    {
        $baseUrl = Request::getFullUrl();
        if (strpos($baseUrl, 'page=') === false){
            if (strpos($baseUrl, '?') === false){
                $baseUrl .= '?page=1';
            } else{
                $baseUrl .= '&page=1';
            }
        }
        if ($count <= 0 || $curPage <= 0 || $pageSize <= 0 || empty($baseUrl)){
            return [];
        }
        $totalPage =  ceil($count / $pageSize);
        if ($totalPage <= 0){
            $totalPage = 1;
        }
        $pattern = '/page=\d+/';
        $firstPageUrl = preg_replace($pattern, 'page=1', $baseUrl);
        $lastPageUrl = preg_replace($pattern, 'page=' . $totalPage, $baseUrl);
        $nextPageUrl =  $curPage == $totalPage ? '' : preg_replace($pattern, 'page=' .($curPage + 1), $baseUrl);
        $prevPageUrl =  $curPage ==  1 ? '' : preg_replace($pattern, 'page=' .($curPage - 1), $baseUrl);

        return [
            'first_page_url' => $firstPageUrl,
            'last_page_url' => $lastPageUrl,
            'current_page' => $curPage,
            'next_page_url' => $nextPageUrl,
            'prev_page_url' => $prevPageUrl,
            'data_count' => $count,
            'total_page' => $totalPage
        ];
    }

    /**
     * 获取分页查询偏移量
     * @param int $curPage
     * @param int $pageSize
     * @return float|int
     */
    public static function getLimitData(int $curPage, int $pageSize)
    {
        $offset = empty($curPage) ? 0 : ($curPage - 1) * $pageSize;
        return $offset;
    }

    public static function getPaging($page, $page_size, $as_string = true)
    {
        $data           = [];
        if($page < 1){
            $page = 1;
        }
        $data['offset'] = ($page - 1) * $page_size;
        $data['rows']   = $page_size;
        if ($as_string) {
            return ' limit ' . implode(',', $data) . ' ';
        }
        return $data;
    }

    /**
     * 获取分页字符串表示
     * @param array $page_info 如: ['page' => 1, 'page_size' => 10]
     * @return array|string
     */
    public static function getLimitString($page_info)
    {
        if(isset($page_info['page']) && isset($page_info['page_size'])){
            return self::getPaging($page_info['page'], $page_info['page_size']);
        }
        return '';
    }
}