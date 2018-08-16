<?php
check_priv("priv_gm_info");
include_once '../include/config.php';
include_once '../include/network.php';

check_login();

$uid = $_SESSION[SESSION_USERID];
$begin_date = trim($_REQUEST['begin_date']) ? trim($_REQUEST['begin_date']) : (date("Y") . "-01-01");
$end_date = trim($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : date("Y-m-d");
$begin_time = strtotime($begin_date . " 00:00:00");
$end_time = strtotime($end_date . " 23:59:59");

$start_index = 0;
$size = 100;
while(true) {
    $sql = "select * from trade where uid='$uid' and sell_time>$begin_time and sell_time<$end_time order by oper_time desc limit $start_index, $size";
    $res = mysql_query($sql);
    while($row = mysql_fetch_assoc($res)){
        $order_arr[] = $row;
    }
    if (count($order_arr) >= 100) {
        $start_index += 100;
        continue;
    }
    else
        break;
}

$total_count = 0;
$gain_count = 0;
$total_gain = 0;
$total_lose = 0;
$total_gain_money = 0;
$total_lose_money = 0;
$total_gain_day = 0;
$total_lose_day = 0;
$max_gain = 0;
$max_lose = 0;
$min_time = 9999999999;
$max_time = 0;
foreach($order_arr as $row) {
    $total_count += 1;
    $profit = (float)$row['profit'];
    $profit_money = (float)$row['profit_money'];
    $buy_time = (int)$row['buy_time'];
    $sell_time = (int)$row['sell_time'];
    $day = ceil(($sell_time - $buy_time) / (3600 * 24));
    if ($profit > 0) {
        $gain_count += 1;
        $total_gain += $profit;
        $total_gain_money += $profit_money;
        $max_gain = $profit > $max_gain ? $profit : $max_gain;
        $total_gain_day += $day;
    }
    else if ($profit < 0) {
        $total_lose += $profit;
        $total_lose_money += $profit_money;
        $max_lose = $profit < $max_lose ? $profit : $max_lose;
        $total_lose_day += $day;
    }
    $sell_time = (int)$row['sell_time'];
    if ($sell_time > $max_time)
        $max_time = $sell_time;
    if ($sell_time < $min_time)
        $min_time = $sell_time;
}

if ($total_count == 0) {
    $avg_success = 0;
    $avg_gain = 0;
    $avg_lose = 0;
    $gain_lose = 0;
    $gain_lose_str = "0 : 0";
    $avg_gain_day = 0;
    $avg_lose_day = 0;
}
else {
    $avg_success = round($gain_count * 100 / $total_count, 2);
    $avg_gain = $gain_count == 0 ? 0 : round($total_gain / $gain_count, 3);
    $avg_lose = $gain_count == $total_count ? 0 : round($total_lose / ($total_count - $gain_count), 3);
    $gain_lose = $avg_gain - $avg_lose;
    if ($avg_gain == 0 || $avg_lose == 0)
        $gain_lose_str = $avg_gain . " : " . $gain_lose;
    else if ($gain_lose > 0)
        $gain_lose_str = $gain_lose . " : 1";
    else if ($gain_lose < 0)
        $gain_lose_str = "1 : " . round(1 / $gain_lose, 2);
    else if ($gain_lose == 0)
        $gain_lose_str = "1 : 1";
    $avg_gain_day = $gain_count == 0 ? 0 : ceil($total_gain_day / $gain_count);
    $avg_lose_day = $gain_count == $total_count ? 0 : ceil($total_lose_day / ($total_count - $gain_count));
}

$data_arr[] = array(
    'date'=>$begin_date . "~".$end_date,
    'total_count'=>$total_count,
    'avg_success'=>$avg_success,
    'avg_gain'=>$avg_gain,
    'avg_lose'=>$avg_lose,
    'gain_lose'=>$gain_lose,
    'gain_lose_str'=>$gain_lose_str,
    'avg_gain_day'=>$avg_gain_day,
    'avg_lose_day'=>$avg_lose_day,
    'max_gain'=>$max_gain,
    'max_lose'=>$max_lose,
);

$Smarty->assign(array(
    'begin_date'=>$begin_date,
    'end_date'=>$end_date,
    'data_arr'=>$data_arr,
	)
);
