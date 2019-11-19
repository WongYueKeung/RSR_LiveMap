<?php

namespace app\index\controller;

use think\Controller;
use think\Session;
use View;

//助手时间Time函数，位于手册的杂项->Time
use think\helper\Time;


//获取菜单信息
use app\index\controller\Menu;


class InspiniaBasic extends Controller
{
    public function index()
    {
        PageSetSiteInfo('首页');
        $this->assign('base_filename', 'index/base');

        //获取页眉状态栏的数据
        $topnav_info = $this->load_topnav_info();
        $this->assign('topnav_info', $topnav_info);


        //获取菜单的数据
        $topnav_menu = new Menu();
        $topnav_menu = $topnav_menu->index();
        //halt($topnav_menu);
        $this->assign('topnav_menu', $topnav_menu);

        $this->assign([
            'title' => 'ThinkPHP',
            'list' => array('id' => 'content1', 'key2' => 'content2')
        ]);
        return $this->fetch('/index/index_Inspinia_basic');
    }


    /**
     * 生成用于动态渲染topnav的信息
     * @param
     * @return array $result
     */
    public function load_topnav_info(){
        if (session('login_time') >= Time::daysAgo(1) && session('logon')){
            $topnav_info['logon'] = true;
        }else{
            $topnav_info['logon'] = false;
        }


        return $topnav_info;
    }

}
