<?php
check_priv("priv_gm_info");
include_once '../include/config.php';
include_once '../include/network.php';

check_login();

$uid = $_SESSION[SESSION_USERID];

$sql_cond = "uid='$uid' and sell_time=0";

$sql = "select * from trade where uid='$uid' and sell_time=0";
$res = mysql_query($sql);
$code_map = array();
while($row = mysql_fetch_assoc($res)){
    $code = $row['code'];
    if (!isset($code_map[$code]))
        $code_map[$code] = array();
    $code_map[$code][] = $row;
}

$data_arr = array();
$total_money = 0;
$total_profit_money = 0;
$stockInfo = fetchStockInfo(array_keys($code_map));
foreach($code_map as $code=>$row_list) {
    if (isset($stockInfo[$code]) && $stockInfo[$code]['name'] != "") {
        $curr_price = (float)$stockInfo[$code]['curr_price'];
        $name = $stockInfo[$code]['name'];
        $name = iconv('GB2312', 'UTF-8', $name);

        // 计算平均成本
        $total_position = 0;
        $avg_price = 0;
        foreach($row_list as $row) {
            $total_position += (int)$row['position'];
        }
        foreach($row_list as $row) {
            $position = (int)$row['position'];
            $buy_price = (float)$row['buy_price'];
            $avg_price += $buy_price * $position / $total_position;
        }
        $avg_price = round($avg_price, 3);

        $money = round($total_position * $curr_price, 3);
        $total_money += $money;

        // 计算盈亏
        $profit = round(($curr_price - $avg_price) * 100 / $avg_price, 3);
        $profit_money = round(($curr_price - $avg_price) * $total_position, 3);
        $total_profit_money += $profit_money;
        $data_arr[] = array(
            'code'=>$code,
            'name'=>$name,
            'position'=>$total_position,
            'curr_price'=>$curr_price,
            'avg_price'=>$avg_price,
            'profit'=>$profit,
            'profit_money'=>$profit_money,
            'money'=>$money,
        );
    }
}

$total_profit = round($total_profit_money * 100 / $total_money, 3);

function my_sort($a,$b)
{
    //返回 0 -1 1
    if ($a['money'] == $b['money'])
        return 0;
    return ($a['money'] >= $b['money']) ? -1 : 1;
}

usort($data_arr, 'my_sort');

$Smarty->assign(array(
    'data_arr'=>$data_arr,
    'total_money'=>$total_money,
    'total_profit_money'=>$total_profit_money,
    'total_profit'=>$total_profit,
	)
);
