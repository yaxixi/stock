<?php
check_priv("tongji_main");
include_once '../include/config.php';
$start_date = date("Y-m-d",$now-86400*90);
$yestoday = date("Y-m-d",$now-86400);
$last_week = date("Y-m-d",$now-86400*7);

// 读取最近一个月的 DAU 数据
db('tongji');
mysql_select_db('tongji');
$sql = "select * from game_online where date>='$start_date' order by date";
$res = mysql_query($sql);

while($row = mysql_fetch_assoc($res)){
    $data_list[$row['date']] = array(
        'login'=> $row['login'],
        'new'=> $row['new'],
    );
    $data_arr[] = $row;
}

// 读取历史最高的登录数据
$sql = "SELECT * FROM `game_online` T WHERE NOT EXISTS(SELECT * FROM game_online WHERE login>T.login)";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
$login_top = array(
    'date'=>$row['date'],
    'value'=>$row['login'],
);
// 读取历史最高的新增数据
$sql = "SELECT * FROM `game_online` T WHERE NOT EXISTS(SELECT * FROM game_online WHERE new>T.new)";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
$new_max = array(
    'date'=>$row['date'],
    'value'=>$row['new'],
);

// 取得当日登录和新增数
$login = isset($data_list[$today]) ? $data_list[$today]['login'] : 0;
$new_today = isset($data_list[$today]) ? $data_list[$today]['new'] : 0;

// 取得昨日登录和新增数
$login_yesterday = isset($data_list[$yestoday]) ? $data_list[$yestoday]['login'] : 0;
$new_yesterday = isset($data_list[$yestoday]) ? $data_list[$yestoday]['new'] : 0;

// 取得上周同期的登录和新增数
$login_lday = isset($data_list[$last_week]) ? $data_list[$last_week]['login'] : 0;
$new_lday = isset($data_list[$last_week]) ? $data_list[$last_week]['new'] : 0;

// 构造页面数据
foreach($data_arr as $value)
{
    $category_arr[] = array(
        "label"=> $value['date'],
    );
    $login_arr[] = array(
        "value"=> $value['login'],
    );
    $new_arr[] = array(
        "value"=> $value['new'],
    );
}

$chart_value=array(
     "caption"=> "每日活跃人数",
                "subCaption"=> "DAU",
                "rotatevalues"=> "0",
                "divlineColor"=> "#999999",
                "paletteColors"=> "#0075c2",
                "baseFontColor"=> "#333333",
                "plotToolText"=> '<div><b>$label</b><br/>人数 : <b>$value</b></div>',
                "showShadow"=> "0",
                "divlineAlpha"=> "100",
                "divlineThickness"=> "1",
                "divLineDashed"=> "1",
                "divLineDashLen"=> "1",
                "lineThickness"=> "3",
                "scrollToEnd"=> "1",
                "flatScrollBars"=> "1",
                "scrollheight"=> "10",
                "numVisiblePlot"=> "30",
                "showHoverEffect"=> "1",
            );
$categories[] = array(
    'category'=> $category_arr,
);
$dataset[] = array(
    'data'=>$login_arr,
    "seriesname"=> "当日活跃人数",
    "color"=> "008ee4",
);
$dataset[] = array(
    'data'=>$new_arr,
    "seriesname"=> "当日新增人数",
    "color"=> "f8bd19",
);
$data_source=array(
    'chart'=>$chart_value,
    'categories'=>$categories,
    'dataset'=>$dataset,
);
$data_source=json_encode($data_source);

$Smarty->assign(
	array(
	'login_top'=>$login_top,
	'login'=>$login,
	'new_today'=>$new_today,
	'login_yesterday'=>$login_yesterday,
	'new_yesterday'=>$new_yesterday,
	'login_lday'=>$login_lday,
	'new_lday'=>$new_lday,
    'new_max'=>$new_max,
    'data_source'=>$data_source,
	)
);
?>
