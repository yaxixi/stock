<?php
check_priv("priv_gm_info");
include_once '../include/config.php';

$author_rid = trim($_REQUEST['author_rid']);
$author_name = trim($_REQUEST['author_name']);
$map_id = trim($_REQUEST['map_id']);
$map_name = trim($_REQUEST['map_name']);

if ($author_rid) {
	$sql = "select * from map where author_rid='$author_rid' order by update_time desc";
    db("hgdb");
    $res = mysql_query($sql);
    while ($row = mysql_fetch_assoc($res)) {
        $data[] = $row;
    }
}

if ($author_name) {
	$sql = "select * from map where author_name='$author_name' order by update_time desc";
	db("hgdb");
    $res = mysql_query($sql);
    while ($row = mysql_fetch_assoc($res)) {
        $data[] = $row;
    }
}

if ($map_id) {
	$sql = "select * from map where id='$map_id'";
	db("hgdb");
    $res = mysql_query($sql);
    $data[] = mysql_fetch_assoc($res);
}

if ($map_name) {
	$sql = "select * from map where map_name='$map_name'";
	db("hgdb");
    $res = mysql_query($sql);
    $data[] = mysql_fetch_assoc($res);
}

$type_map = array(
    1=>"角色扮演",
    2=>"文字冒险",
    3=>"塔防",
    4=>"解谜益智",
    5=>"插件",
    6=>"不思议迷宫风",
    7=>"开源地图",
    8=>"复刻经典",
);

$status_map = array(
    1=>"测试",
    2=>"完善中",
    3=>"完成",
);

$Smarty->assign(array(
	'data'=>$data,
    'type_map'=>$type_map,
    'status_map'=>$status_map,
    )
);
