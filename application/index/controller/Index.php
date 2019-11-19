<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
<<<<<<< Updated upstream
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
=======
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
        $json_red = $this->map_json_red_airbase();
        $json_blue= $this->map_json_blue_airbase();

        $this->assign('json_red', $json_red);
        $this->assign('json_blue', $json_blue);


        return $this->fetch('');
        //        return $this->fetch('/index/index_Inspinia_basic');

    }

    public function read_state(){
        //$data_state = file_get_contents('C:\Users\mikeh\Saved Games\DCS\Scripts\RSR\rsrState.json');
                $data_state = file_get_contents('C:\Users\Ash\Saved Games\DCS.DS_openbeta\Scripts\RSR\rsrState.json');
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

    public function blue_temp(){
        $blue_current_airbase = $this->read_state_blue_airbase();


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


        return $data;

    }

    public function red_temp(){
        $blue_current_airbase = $this->read_state_red_airbase();


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


        return $data;

    }

    public function map_json_blue_airbase(){
        $this->update_state();
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
        $json_decode['source']['data']['features'] = $this->blue_temp();


        //temp fix json decode issue
        $result = str_replace('[]' , '{}',json_encode($json_decode));
        return $result;
    }

    public function map_json_red_airbase(){
        $this->update_state();

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
        $json_decode['source']['data']['features'] = $this->red_temp();

        $result = str_replace('[]' , '{}',json_encode($json_decode));

        return $result;
>>>>>>> Stashed changes
    }
}
