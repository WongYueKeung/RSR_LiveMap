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
        $data_blue_airbase = $this->read_state_blue_airbase($data_RSRState);
        $data_red_airbase = $this->read_state_red_airbase($data_RSRState);

        $json_red_airbase = $this->red_json_generator_airbase($data_red_airbase);
        $json_blue_airbase= $this->blue_json_generator_airbase($data_blue_airbase);


        //dump($json_red);
        //halt($json_blue);

        $this->assign('json_red', $json_red_airbase);
        $this->assign('json_blue', $json_blue_airbase);


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

    public function read_state_blue_airbase($data){
        //$data  = $this -> read_state();
        //dump($data['baseOwnership']['airbases']['blue']);
        $data = $data['baseOwnership']['airbases']['blue'];
        return $data;
    }
    public function read_state_red_airbase($data){
        //$data  = $this -> read_state();
        //dump($data['baseOwnership']['airbases']['red']);
        $data = $data['baseOwnership']['airbases']['red'];
        return $data;

    }

    public function update_state($data_RSRState){
        $db_live_map = CaucasusLivemap::all() ;
        //dump($db_live_map);

        $blue_current_airbase = $this->read_state_blue_airbase($data_RSRState);
        $red_current_airbase = $this->read_state_red_airbase($data_RSRState);

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

    /**
     * @param $blue_current_airbase an array contain Blue current airbases, has to be from RSRstate.json file
     * @return String json string for the mapbox,  inside the js
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function blue_json_generator_airbase($blue_current_airbase){
        //$blue_current_airbase = $this->read_state_blue_airbase();


        foreach ($blue_current_airbase as $key => $value){

            $db_temp = CaucasusLivemap::where('name', $value)->find();
            //dump($value);
            //dump($db_temp);
            $data[$key]['type'] = 'Feature';
            $data_properties['cap'] = $db_temp['display_name'];
            $data_properties['uncap'] = 'small text';

            $data[$key]['properties'] = $data_properties;

            $data_geometry['type'] = "Point";
            $data_coordinates = array($db_temp['x'],$db_temp['y']);
            $data_geometry['coordinates'] = $data_coordinates;

            $data[$key]['geometry'] = $data_geometry;


            unset($data_properties, $data_geometry,$data_coordinates);
        }
        //dump(json_encode($data));


        //return $data;

        //start to merge this array in to json for mapbox web GL
        $json =  '{
            "id":"blue",
            "type":"symbol",
            "source":{
                "type":"geojson",
                "data":{
                    "type":"FeatureCollection",
                    "features":[
                        {
                            "type":"Feature",
                            "properties":{
                                "cap":"Sochi",
                                "uncap":"small text"
                            },
                            "geometry":{
                                "type":"Point",
                                "coordinates":[
                                    39.729996,
                                    43.58835
                                ]
                            }
                        },
                        {
                            "type":"Feature",
                            "properties":{
                                "cap":"Krasnodar",
                                "uncap":"small text"
                            },
                            "geometry":{
                                "type":"Point",
                                "coordinates":[
                                    38.97626,
                                    45.057762
                                ]
                            }
                        }
                    ]
                }
            },
            "layout":{
                "icon-image": "pulsing-dot1",
                "text-field":[
                    "format",
                    [
                        "upcase",
                        [
                            "get",
                            "cap"
                        ]
                    ],
                    {
                        "font-scale":0.8
                    },
                    "\n",
                    {

                    },
                    [
                        "downcase",
                        [
                            "get",
                            "uncap"
                        ]
                    ],
                    {
                        "font-scale":0.6
                    }
                ],
                "text-font":[
                    "Open Sans Semibold",
                    "Arial Unicode MS Bold"
                ],
                "text-offset":[
                    0,
                    0.6
                ],
                "text-anchor":"top"
            }
        }';

        //make json data for key:features
        $json_decode = json_decode($json, 1);
        $json_decode['source']['data']['features'] = $data;


        //temp fix json decode issue
        $result = str_replace('[]' , '{}',json_encode($json_decode));
        return $result;

    }

    /**
     * @param $red_current_airbase an array contain Blue current airbases, has to be from RSRstate.json file
     * @return String json string for the mapbox,  inside the js
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function red_json_generator_airbase($red_current_airbase){
        //$blue_current_airbase = $this->read_state_red_airbase();

        foreach ($red_current_airbase as $key => $value){

            $db_temp = CaucasusLivemap::where('name', $value)->find();
            //dump($value);
            //dump($db_temp);
            $data[$key]['type'] = 'Feature';
            $data_properties['cap'] = $db_temp['display_name'];
            $data_properties['uncap'] = 'small text';

            $data[$key]['properties'] = $data_properties;

            $data_geometry['type'] = "Point";
            $data_coordinates = array($db_temp['x'],$db_temp['y']);
            $data_geometry['coordinates'] = $data_coordinates;

            $data[$key]['geometry'] = $data_geometry;


            unset($data_properties, $data_geometry,$data_coordinates);
        }
        //dump(json_encode($data));


        //return $data;

        $json =  '{
            "id":"red",
            "type":"symbol",
            "source":{
                "type":"geojson",
                "data":{
                    "type":"FeatureCollection",
                    "features":[
                        {
                            "type":"Feature",
                            "properties":{
                                "cap":"Sochi",
                                "uncap":"small text"
                            },
                            "geometry":{
                                "type":"Point",
                                "coordinates":[
                                    39.729996,
                                    43.58835
                                ]
                            }
                        },
                        {
                            "type":"Feature",
                            "properties":{
                                "cap":"Krasnodar",
                                "uncap":"small text"
                            },
                            "geometry":{
                                "type":"Point",
                                "coordinates":[
                                    38.97626,
                                    45.057762
                                ]
                            }
                        }
                    ]
                }
            },
            "layout":{
                "icon-image":"pulsing-dot",
                "text-field":[
                    "format",
                    [
                        "upcase",
                        [
                            "get",
                            "cap"
                        ]
                    ],
                    {
                        "font-scale":0.8
                    },
                    "\n",
                    {

                    },
                    [
                        "downcase",
                        [
                            "get",
                            "uncap"
                        ]
                    ],
                    {
                        "font-scale":0.6
                    }
                ],
                "text-font":[
                    "Open Sans Semibold",
                    "Arial Unicode MS Bold"
                ],
                "text-offset":[
                    0,
                    0.6
                ],
                "text-anchor":"top"
            }
        }';

        //make json data for key:features
        $json_decode = json_decode($json, 1);
        $json_decode['source']['data']['features'] = $data;

        $result = str_replace('[]' , '{}',json_encode($json_decode));

        return $result;

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
