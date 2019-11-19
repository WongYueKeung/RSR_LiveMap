<?php

namespace app\index\controller;

use app\index\model\Menu as menuDb;
use think\Controller;
use think\Session;
use View;


use RapidApi\RapidApiConnect;
use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;
use Ivory\GoogleMap\Map;

class Menu extends Controller
{

    public function index()
    {
        error_reporting(0);
        $menu = menuDb::all();
        //var_dump($menu);
        $menu = $menu->toArray();
// test:
        $menuData = new Menu;
        //var_dump($menuData->parseToTree($menu));
        return $menuData->parseToTree($menu);
    }

    public function parseToTree($data)
    {
// 数据处理
        foreach ($data as $i => $item) {
            $path = $this->parsePath($item['path']);
            $item['children'] = [];
            $data[$path] = $item;
            unset($data[$i]);
        }
// 键排序
        ksort($data);

        $result = [];
// 树形结构处理
        foreach ($data as &$value) {
            $list = explode('/', $value['path']);
            if (isset($list[1])) {
// 可能有父级
                while (isset($list[0])) {
                    array_pop($list);
                    $key = implode('/', $list);
                    if (isset($data[$key])) {
                        $data[$key]['children'][] = &$value;
                        break;
                    }
                }
// 一个父级都没就顶级
                if (!isset($list[0])) {
                    $result[] = &$value;
                }
            } else {
// 顶级
                $result[] = &$value;
            }
        }

        $this->sortResult($data);
        return $result;
    }

    /**
     * 结果排序
     * @param array $data
     * @return void
     */
    private function sortResult(&$data)
    {
        $this->sortItem($data);
        foreach ($data as &$value) {
            if (!isset($value['children'][0])) {
                continue;
            }
            $this->sortItem($value['children']);
        }
    }

    /**
     * 排序项
     * @param array $data
     * @return void
     */
    private function sortItem(&$data)
    {
        if (null !== $data[0]['rank']) {
// 根据rank排序
            usort($data, function ($a, $b) {
                if ($a['rank'] > $b['rank']) {
                    return 1;
                } else {
                    return -1;
                }
            });
        } else {
// 根据path最后一个路径排序
            usort($data, function ($a, $b) {
                $list = explode('/', $a['path']);
                $aValue = array_pop($list);
                $list = explode('/', $b['path']);
                $bValue = array_pop($list);
                if (strcmp($aValue, $bValue) > 0) {
                    return 1;
                } else {
                    return -1;
                }
            });
        }
    }

    /**
     * 处理path，去除两头的/
     * @param string $path
     * @return string
     */
    private function parsePath($path)
    {
        return trim($path, '/');
    }


}
