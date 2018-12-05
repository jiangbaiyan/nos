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

class Page{

    /**
     * 生成分页数据
     * @param $count
     * @param $curPage
     * @param $pageSize
     * @return array
     */
    public static function paginate($count, $curPage, $pageSize){
        $baseUrl = Request::getFullUrl();
        if (strpos($baseUrl, 'page=') === false){
            if (strpos($baseUrl, '?') === false){
                $baseUrl .= '?page=1';
            } else{
                $baseUrl .= '&page=1';
            }
        }
        if ($count <= 0 || $curPage <= 0 || $pageSize <= 0 || empty($baseUrl)){
            return array();
        }
        $totalPage =  ceil($count / $pageSize);
        if ($totalPage <= 0){
            $totalPage = 1;
        }
        $pattern = '/page=\d+/';
        $firstPageUrl = preg_replace($pattern, 'page=1', $baseUrl);
        $lastPageUrl = preg_replace($pattern, 'page=' . $totalPage, $baseUrl);
        $nextPageUrl =  $curPage == $totalPage ? "" : preg_replace($pattern, 'page=' .($curPage + 1), $baseUrl);
        $prevPageUrl =  $curPage ==  1 ? '' : preg_replace($pattern, 'page=' .($curPage - 1), $baseUrl);

        return array(
            'first_page_url' => $firstPageUrl,
            'last_page_url' => $lastPageUrl,
            'current_page' => $curPage,
            'next_page_url' => $nextPageUrl,
            'prev_page_url' => $prevPageUrl,
            'data_count' => $count,
            'total_page' => $totalPage
        );
    }

    /**
     * 获取查询数据库的limit两个参数
     * @param $curPage
     * @param $pageSize
     * @return array
     */
    public static function getLimitData($curPage, $pageSize){
        $offset = empty($curPage) ? 0 : ($curPage - 1) * $pageSize;
        return array($offset, $pageSize);
    }
}