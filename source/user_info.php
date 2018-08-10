<?php
check_priv("priv_gm_info");
include_once '../include/config.php';

$account = trim($_REQUEST['account']);
$channel = trim($_REQUEST['channel']);
$rid = trim($_REQUEST['rid']);
$name = trim($_REQUEST['name']);

if ($account) {
	$sql = "select * from account where account='$account'";
    if ($channel)
        $sql = $sql . " and channel='$channel'";
	foreach ($all_adb as $db_key=>$db_value) {
		db($db_key);
		mysql_select_db($db_value);
		$res = mysql_query($sql);
		$data = mysql_fetch_assoc($res);
		if ($data) {
			break;
		}
	}
}

if ($rid) {
	$sql = "select * from account where rid='$rid'";
	foreach ($all_adb as $db_key=>$db_value) {
		db($db_key);
		mysql_select_db($db_value);
		$res = mysql_query($sql);
		$data = mysql_fetch_assoc($res);
		if ($data) {
			break;
		}
	}
}

if ($name) {
	$sql = "select * from account where name='$name'";
	foreach ($all_adb as $db_key=>$db_value) {
		db($db_key);
		mysql_select_db($db_value);
		$res = mysql_query($sql);
		$data = mysql_fetch_assoc($res);
		if ($data) {
			break;
		}
	}
}

$Smarty->assign(array(
	'data'=>$data,
	)
);
