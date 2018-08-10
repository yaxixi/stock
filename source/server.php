<?php
check_priv("tongji_main");
include_once '../include/config.php';
$start_date = date("Y-m-d",$now-86400*30);
$yestoday = date("Y-m-d",$now-86400);
$last_week = date("Y-m-d",$now-86400*7);

$game_id=(isset($_REQUEST['game_id']))?$_REQUEST['game_id']:'total';

// 读取 server 表
db('hgdb');
mysql_select_db('hgdb');
$sql = "select server_id, user_num, room_num, overload from server where status=1";
$res = mysql_query($sql);

$max_cpu = 0;
$max_cpu_id = 1;
$max_user_num = 0;
$max_user_num_id = 1;
$max_room_num = 0;
$max_room_num_id = 1;
while($row = mysql_fetch_assoc($res)){
    $data_arr[] = $row;
    if($max_cpu < (int) $row['overload'])
    {
        $max_cpu = (int) $row['overload'];
        $max_cpu_id = $row['server_id'];
    }
    if($max_user_num < (int) $row['user_num'])
    {
        $max_user_num = (int) $row['user_num'];
        $max_user_num_id = $row['server_id'];
    }
    if($max_room_num < (int) $row['room_num'])
    {
        $max_room_num = (int) $row['room_num'];
        $max_room_num_id = $row['server_id'];
    }
}

// 构造页面数据
foreach($data_arr as $value)
{
    $category_arr[] = array(
        "label"=> 'S'.$value['server_id'],
    );
    $cpu_arr[] = array(
        "value"=> $value['overload'],
    );
    $user_num_arr[] = array(
        "value"=> $value['user_num'],
    );
    $room_num_arr[] = array(
        "value"=> $value['room_num'],
    );
}

$chart_value=array(
     "caption"=> "服务器状态",
                "subCaption"=> "",
                "rotatevalues"=> "0",
                "divlineColor"=> "#999999",
                "paletteColors"=> "#0075c2",
                "baseFontColor"=> "#333333",
                "xAxisname"=>"server_id",
                "pYAxisName"=>"CPU",
                "sYAxisName"=>"Amount",
                "numberSuffix"=>"%",
                "sNumberSuffix"=>"",
                //"plotToolText"=> '<div><b>$label</b><br/>CPU : <b>$value%</b></div>',
                "showShadow"=> "0",
                "divlineAlpha"=> "100",
                "divlineThickness"=> "1",
                "divLineDashed"=> "1",
                "divLineDashLen"=> "1",
                "lineThickness"=> "3",
                "flatScrollBars"=> "1",
                "scrollheight"=> "10",
                "numVisiblePlot"=> "12",
                "showHoverEffect"=> "1",
            );
$categories[] = array(
    'category'=> $category_arr,
);
$dataset[] = array(
    'data'=>$cpu_arr,
    "seriesname"=> "CPU",
    "color"=> "008ee4",
);
$dataset[] = array(
    'data'=>$user_num_arr,
    "seriesname"=> "联网人数",
    "renderAs"=>"column",
    "parentYAxis"=>"S",
    "color"=> "f8bd19",
);
$dataset[] = array(
    'data'=>$room_num_arr,
    "seriesname"=> "房间数",
    "renderAs"=>"column",
    "parentYAxis"=>"S",
    "color"=> "a1d490",
);
$data_source=array(
    'chart'=>$chart_value,
    'categories'=>$categories,
    'dataset'=>$dataset,
);
$data_source=json_encode($data_source);

$Smarty->assign(
	array(
	'max_cpu'=>$max_cpu,
	'max_cpu_id'=>$max_cpu_id,
	'max_user_num'=>$max_user_num,
	'max_user_num_id'=>$max_user_num_id,
	'max_room_num'=>$max_room_num,
	'max_room_num_id'=>$max_room_num_id,
    'data_source'=>$data_source,
	)
);
?>
