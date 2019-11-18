<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


function PageSetSiteInfo($title)
{
    //设置应该位于每个页面头部的<head>标签内容
    $site_info['title'] = $title . ' - ' . Env::get('site.name');
    $site_info['header_title'] = $title;
    $site_info['description'] = Env::get('site.description');
    $site_info['author'] = Env::get('site.author');
    View::share('site_info', $site_info);

    return $site_info;
}

function PageSetBasicInfo($page_heading)
{
    //设置应该位于每个页面顶部的page header顶部导航栏
    //todo:记得把这个转换成数组
    $page_basic_info['page_heading'] = $page_heading;
    View::share('page_basic_info', $page_basic_info);

    return $page_basic_info;
}

function PageSetAllInfo($title, $page_header){
    $site_info = PageSetSiteInfo($title);
    PageSetBasicInfo($page_header);
}
function GetIpLookup($ip = '')
{
    if (empty($ip)) {
        $ip = GetIp();
    }
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if (empty($res)) {
        return false;
    }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if (!isset($jsonMatches[0])) {
        return false;
    }
    $json = json_decode($jsonMatches[0], true);
    if (isset($json['ret']) && $json['ret'] == 1) {
        $json['ip'] = $ip;
        unset($json['ret']);
    } else {
        return false;
    }
    return $json;
}


/**
 * 通过URL获取页面信息
 * @param string $url 地址
 * @return string 返回页面信息
 */
function get_url_decode($url)
{
    $flag = false;
    while(!$flag) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);  //设置访问的url地址
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不输出内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);
        halt(isset($result));
        if (isset($result)){
            $flag = true;
        }
    }
    return json_decode($result);
}