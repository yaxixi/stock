<?php
check_priv("priv_gm_info");
include_once '../include/config.php';
include_once '../include/network.php';

$code = trim($_REQUEST['code']);
$count = (int)trim($_REQUEST['count']);
$price = trim($_REQUEST['price']);
$curr_date = trim($_REQUEST['date']);
$curr_time = trim($_REQUEST['time']);
$oper = trim($_REQUEST['oper']);
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;

if ($code) {
    $stockInfo = fetchStockInfo(array($code));
    if (isset($stockInfo[$code]) && $stockInfo[$code]['name'] != "") {
        db("stock");
        $name = $stockInfo[$code]['name'];
        $curr_price = $stockInfo[$code]['curr_price'];
        $name = iconv('GB2312', 'UTF-8', $name);
        $time = strtotime($curr_date . " ". $curr_time);
        if ($oper == "buy") {
            $profit = ($curr_price - $price) * 100 / $price;
            $profit_money = ($curr_price - $price) * $count;
            $sql = "insert into trade (`code`,`name`,`position`,`buy_price`,`buy_time`,`oper_time`,`curr_price`,`profit`,`profit_money`) values ('$code', '$name', $count, $price, $time, $time, $curr_price, $profit, $profit_money)";
            mysql_query($sql);
        }
        else {
            // 卖出
            // 取得剩余持仓的记录
            $sql = "select * from trade where code='$code' and sell_time=0 and position>0 order by oper_time desc";
            $res = mysql_query($sql);
            while($row = mysql_fetch_assoc($res)){
                $data_arr[] = $row;
            }
            $flag = 0;
            foreach($data_arr as $row) {
                $flag = 1;
                if ($count <= 0)
                    break;
                if ($count >= (int)$row['position']) {
                    // 插入新记录
                    $profit = ((float)$price - (float)$row['buy_price']) * 100 / (float)$row['buy_price'];
                    $profit_money = ((float)$price - (float)$row['buy_price']) * (int)$row['position'];
                    $position = $row['position'];
                    $buy_price = $row['buy_price'];
                    $buy_time = $row['buy_time'];
                    $sql = "insert into trade (`code`,`name`,`position`,`buy_price`,`buy_time`,`curr_price`,`sell_price`,`sell_time`,`oper_time`,`profit`,`profit_money`) values ('$code', '$name', $position, $buy_price, $buy_time, $curr_price, $price, $time, $time, $profit, $profit_money)";
                    mysql_query($sql);

                    // 删除旧记录
                    $id = $row['id'];
                    $sql = "delete from trade where id=$id";
                    mysql_query($sql);

                    $count = $count - (int)$row['position'];
                }
                else {
                    // 插入新记录
                    $profit = ((float)$price - (float)$row['buy_price']) * 100 / (float)$row['buy_price'];
                    $profit_money = ((float)$price - (float)$row['buy_price']) * $count;
                    $buy_price = $row['buy_price'];
                    $buy_time = $row['buy_time'];
                    $sql = "insert into trade (`code`,`name`,`position`,`buy_price`,`buy_time`,`curr_price`,`sell_price`,`sell_time`,`oper_time`,`profit`,`profit_money`) values ('$code', '$name', $count, $buy_price, $buy_time, $curr_price, $price, $time, $time, $profit, $profit_money)";
                    mysql_query($sql);

                    // 更新旧记录的 position
                    $id = $row['id'];
                    $sql = "update trade set position = position - $count where id=$id";
                    mysql_query($sql);

                    break;
                }
            }
            if ($flag == 0) {
                alert("没有该代码对应的持仓！");
            }
        }
    }
    else
        alert("找不到指定代码！");
}

$page_size = 20;
$start_index = ($page - 1) * $page_size;
$sql_cond = "order by oper_time desc limit $start_index, $page_size";

$sql = "select * from trade " . $sql_cond;
$res = mysql_query($sql);
$code_list = array();
while($row = mysql_fetch_assoc($res)){
    if (!isset($code_list[$row['code']]))
        $code_list[$row['code']] = 1;
    $row['buy_time'] = date('Y/m/d H:i', $row['buy_time']);
    if ((int) $row['sell_time'] > 0)
        $row['sell_time'] = date('Y/m/d H:i', $row['sell_time']);
    $order_arr[] = $row;
}

$stockInfo = fetchStockInfo(array_keys($code_list));
foreach($order_arr as $row) {
    $code = $row['code'];
    if (isset($stockInfo[$code]) && $stockInfo[$code]['name'] != "") {
        $curr_price = $stockInfo[$code]['curr_price'];
        $row['curr_price'] = $curr_price;
    }
}

$Smarty->register_function('page_ex','page_ex');
$Smarty->assign(array(
    'data'=>array("date"=>date("Y-m-d"), "time"=>"00:00:00"),
    'data_arr'=>$order_arr,
    'page'=>$page,
    'page_size'=>$page_size,
	)
);
