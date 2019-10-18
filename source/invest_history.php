<?php
check_priv("tongji_main");
include_once '../include/config.php';
include_once '../include/global_init.php';

// 构造页面数据
foreach($invest_list as $value)
{
    $category_arr[] = array(
        "label"=> $value['date'],
    );
    $profit_percent_arr[] = array(
        "value"=> $value['profit_percent'],
    );
    $profit_money_arr[] = array(
        "value"=> $value['profit_money'],
    );
    $total_money_arr[] = array(
        "value"=> $value['total_money'],
    );
}

$chart_value=array(
     "caption"=> "投资曲线",
                "subCaption"=> "Investment Curve",
                "rotatevalues"=> "0",
                "divlineColor"=> "#999999",
                "paletteColors"=> "#0075c2",
                "baseFontColor"=> "#333333",
                "plotToolText"=> '<div><b>$label</b><br/>收益 : <b>$value</b></div>',
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
                "showValues"=>"0",
            );
$categories[] = array(
    'category'=> $category_arr,
);
$dataset[] = array(
    'data'=>$profit_percent_arr,
    "seriesname"=> "投资收益",
    "color"=> "008ee4",
);
/*
$dataset[] = array(
    'data'=>$new_arr,
    "seriesname"=> "当日新增人数",
    "color"=> "f8bd19",
);*/
$data_source=array(
    'chart'=>$chart_value,
    'categories'=>$categories,
    'dataset'=>$dataset,
);
$data_source=json_encode($data_source);

$Smarty->assign(
	array(
	'profit_percent_arr'=>$profit_percent_arr,
	'profit_money_arr'=>$profit_money_arr,
	'total_money_arr'=>$total_money_arr,
    'data_source'=>$data_source,
	)
);
?>
