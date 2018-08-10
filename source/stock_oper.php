<?php
check_priv("priv_gm_info");
include_once '../include/config.php';
include_once '../include/network.php';

$code = trim($_REQUEST['code']);
$count = trim($_REQUEST['count']);
$price = trim($_REQUEST['price']);
$curr_date = trim($_REQUEST['date']);
$curr_time = trim($_REQUEST['time']);
$oper = trim($_REQUEST['oper']);

if ($code) {
	fetchStockInfo(array($code));
}

$Smarty->assign(array(
	'data'=>array("date"=>date("Y-m-d"), "time"=>"00:00:00"),
	)
);
