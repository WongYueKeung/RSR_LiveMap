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



class Temp extends Controller
{
    public function index()
    {
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
        $json_decode = json_decode($json, 1);
        dump($json_decode);
        dump($json);

    }

    public function read_state(){
        $data_state = file_get_contents('C:\Users\mikeh\Saved Games\DCS\Scripts\RSR\rsrState.json');
        //        $data_state = file_get_contents('C:\Users\Ash\Saved Games\DCS.DS_openbeta\Scripts\RSR\rsrState.json');
        //dump($data_state);
        $data_state_decode = json_decode($data_state,1);
        //dump($data_state_decode);
        return $data_state_decode;
    }

    public function read_state_blue_airbase(){
        $data  = $this -> read_state();
        //dump($data['baseOwnership']['airbases']['blue']);
        $data = $data['baseOwnership']['airbases']['blue'];
        return $data;
    }
    public function read_state_red_airbase(){
        $data  = $this -> read_state();
        //dump($data['baseOwnership']['airbases']['red']);
        $data = $data['baseOwnership']['airbases']['red'];

        return $data;

    }

    public function update_state(){
        $db_live_map = CaucasusLivemap::all() ;
        //dump($db_live_map);

        $blue_current_airbase = $this->read_state_blue_airbase();
        $red_current_airbase = $this->read_state_red_airbase();

        dump($red_current_airbase);


        //compare and update blue team airbase captures info
        foreach ($blue_current_airbase as $key => $value){

            $db_temp = CaucasusLivemap::where('name', $value)->find();
            dump($value);
            dump($db_temp);


            if ($db_temp['side'] != 'blue'){
                $db_temp->side = 'blue';
                $db_temp->capcture_unix_time = time();
                $db_temp->save();
            }

        }

        foreach ($red_current_airbase as $key => $value){

            $db_temp = CaucasusLivemap::where('name', $value)->find();
            dump($value);
            dump($db_temp);


            if ($db_temp['side'] != 'red'){
                $db_temp->side = 'red';
                $db_temp->capcture_unix_time = time();
                $db_temp->save();
            }

        }


    }
}
