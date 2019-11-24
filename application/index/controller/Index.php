<?php

namespace app\index\controller;

use app\index\model\CaucasusLivemap;
use think\Controller;
use think\Session;
use View;
use think\model;


use app\index\model\PgLivemap;

//助手时间Time函数，位于手册的杂项->Time
use think\helper\Time;

use app\index\controller\Farp;
use app\index\controller\Airbase;


class Index extends Controller
{
    public function index()
    {
        PageSetSiteInfo('Main');
        $this->assign('base_filename', 'index/base');



        //获取菜单的数据
        $topnav_menu = new Menu();
        $topnav_menu = $topnav_menu->index();
        //halt($topnav_menu);
        $this->assign('topnav_menu', $topnav_menu);

        $this->assign([
            'title' => 'ThinkPHP',
            'list' => array('id' => 'content1', 'key2' => 'content2')
        ]);
        $data_RSRState = $this->read_state();

        //Update db once from RSRState.json b4 showin the map
        $this->update_state($data_RSRState);
        //read the RSRState.json file content and spilt them, covert into PHP array(reuse the RSRState.json content from updating DB)
        $data_blue_airbase = Airbase::read_state_blue_airbase($data_RSRState);
        $data_red_airbase = Airbase::read_state_red_airbase($data_RSRState);
        $data_neutral_airbase = Airbase::read_state_neutral_airbase($data_RSRState);

        //halt($data_neutral_airbase);


        $json_red_airbase = Airbase::red_json_generator_airbase($data_red_airbase);
        $json_blue_airbase= Airbase::blue_json_generator_airbase($data_blue_airbase);
        $json_neutral_airbase= Airbase::neutral_json_generator_airbase($data_neutral_airbase);


        //dump($json_red);
        //halt($json_neutral_airbase);

        $this->assign('json_red_airbase', $json_red_airbase);
        $this->assign('json_blue_airbase', $json_blue_airbase);
        $this->assign('json_neutral_airbase', $json_neutral_airbase);


        return $this->fetch('');
        //        return $this->fetch('/index/index_Inspinia_basic');

    }

    public function read_state(){
        $data_state = file_get_contents('C:\Users\mikeh\Saved Games\DCS\Scripts\RSR\rsrState.json');
        //        $data_state = file_get_contents('C:\Users\Ash\Saved Games\DCS.DS_openbeta\Scripts\RSR\rsrState.json');
        //dump($data_state);
        $data_state_decode = json_decode($data_state,1);
        //dump($data_state_decode);
        return $data_state_decode;
    }


    public function update_state($data_RSRState){
        $db_live_map = CaucasusLivemap::all() ;
        //dump($db_live_map);

        $blue_current_airbase = Airbase::read_state_blue_airbase($data_RSRState);
        $red_current_airbase = Airbase::read_state_red_airbase($data_RSRState);

        //dump($red_current_airbase);


        //compare and update blue team airbase captures info
        foreach ($blue_current_airbase as $key => $value){

            $db_temp = CaucasusLivemap::where('name', $value)->find();
            //dump($value);
            //dump($db_temp);


            if ($db_temp['side'] != 'blue'){
                $db_temp->side = 'blue';
                $db_temp->capcture_unix_time = time();
                $db_temp->save();
            }

        }

        foreach ($red_current_airbase as $key => $value){

            $db_temp = CaucasusLivemap::where('name', $value)->find();
            //dump($value);
            //dump($db_temp);


            if ($db_temp['side'] != 'red'){
                $db_temp->side = 'red';
                $db_temp->capcture_unix_time = time();
                $db_temp->save();
            }

        }


    }


    public function map_json_blue_frap(){
        $this->update_state();
        $json =  '{
                            "id": "points",
                            "type": "symbol",
                            "source": {
                                "type": "geojson",
                                "data": {
                                    "type": "FeatureCollection",
                                    "features": [{
                                        "type": "Feature",
                                        "geometry": {
                                            "type": "Point",
                                            "coordinates": [0, 0]
                                        }
                                    }]
                                }
                            },
                            "layout": {
                                "icon-image": "cat",
                                "icon-size": 0.25
                            }
                        }';

        //make json data for key:features
        $json_decode = json_decode($json, 1);
        //$json_decode['source']['data']['features'] = $this->blue_temp();

        $farp = new Farp();
        $farp->index();

        //temp fix json decode issue
        $result = str_replace('[]' , '{}',json_encode($json_decode));
        return $result;
    }


}
