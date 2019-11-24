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


class Farp extends Controller
{
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

    public function read_state_neutral_airbase($data){
        //$data  = $this -> read_state();
        //dump($data['baseOwnership']['airbases']['red']);
        $data = $data['baseOwnership']['airbases']['neutral'];
        return $data;

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
            $data_properties['uncap'] = 'captured ' . format_date($db_temp['capcture_unix_time']);

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
                "icon-allow-overlap": true,
                "icon-ignore-placement": true,
                "text-allow-overlap": true,
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
            $data_properties['uncap'] = 'captured ' . format_date($db_temp['capcture_unix_time']);

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
                "icon-allow-overlap": true,
                "icon-ignore-placement": true,
                "text-allow-overlap": true,
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

    public function neutral_json_generator_airbase($neutral_current_airbase){
        //$blue_current_airbase = $this->read_state_red_airbase();

        foreach ($neutral_current_airbase as $key => $value){

            $db_temp = CaucasusLivemap::where('name', $value)->find();
            //dump($value);
            //dump($db_temp);
            $data[$key]['type'] = 'Feature';
            $data_properties['cap'] = $db_temp['display_name'];
            $data_properties['uncap'] = 'Neutral since ' . format_date($db_temp['capcture_unix_time']);

            $data[$key]['properties'] = $data_properties;

            $data_geometry['type'] = "Point";
            $data_coordinates = array($db_temp['x'],$db_temp['y']);
            $data_geometry['coordinates'] = $data_coordinates;

            $data[$key]['geometry'] = $data_geometry;


            unset($data_properties, $data_geometry,$data_coordinates);
            //unset($data_geometry,$data_coordinates);

        }
        //halt($data);


        //return $data;

        $json =  '{
                    "id": "airbase_neutral",
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
                        "icon-image": "airbase-neutral",
                        "icon-allow-overlap": true,
                        "icon-ignore-placement": true,
                        "text-allow-overlap": true,
                        "icon-size": 0.04,
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
        //halt($json_decode);

        $json_decode['source']['data']['features'] = $data;


        $result = str_replace('[]' , '{}',json_encode($json_decode));

        return $result;

    }

}