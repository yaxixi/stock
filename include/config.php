<?php
//error_reporting(E_ALL);
error_reporting(E_ALL^E_NOTICE^E_WARNING);
date_default_timezone_set('Asia/Shanghai');
header("Content-Type:text/html; charset=utf-8");

///需要重新配置的变量///////////////////////////////////////////////////////////////////////////////
define("SERVER_IP", "127.0.0.1");//定义PP服务器IP
define("SERVER_PORT", 9001);//定义PP游戏服务器PORT
define("CC_ID", 1);//定义CC服务器ID。举例：配置为1时，pp会将消息发往cc1
define("BS_GROUP", "normal");//定义BS_GROUP名称
define('DIST_NAME',"测试"); // 连接区服名称
define('TITLE',"管理后台（".DIST_NAME."）"); // 后台工具名称
define('BGCOLOR',"CAE8CC"); // 左侧边栏背景颜色
define("LANG", "zh"); //默认语言
define("TIME_ZONE", "Asia/Shanghai"); //默认时区 具体值参考 http://php.net/manual/zh/timezones.asia.php
define("PLATFORM", "dh"); // 渠道名称，需要和sdkConst.lua中的渠道标识一致（电魂版本礼包推送持续时间单位为小时）
define('IS_CHANNEL_SERVER', 1); // 是否是渠道服。1：是  0：否（目前只有雷霆渠道服填1，其他服都填0）
define('TONGJI_DB', "mydb");

/*
$conninfo = array(//管理数据库
	"host" => "127.0.0.1",
	"username" => "root",
	"password" => "usbw",
);*/
$conninfo = array(//管理数据库
	"host" => "172.105.202.151",
	"username" => "root",
	"password" => "3981895",
);

$db_map=array(
	'tongji'=>array(
        'host'=>'172.105.202.151',
        'user'=>'root',
        'pass'=>'3981895',
        'dtbs'=>'mydb',
		'charts'=>'utf8',
	),
	'mpay'=>array(
        'host'=>'172.105.202.151',
        'user'=>'root',
        'pass'=>'3981895',
        'dtbs'=>'mydb',
		'charts'=>'utf8',
	),
    'stock'=>array(
        'host'=>'172.105.202.151',
        'user'=>'root',
        'pass'=>'3981895',
        'dtbs'=>'mydb',
		'charts'=>'utf8',
	),
);

function db($dbname){
	global $db_map;
	$con=mysql_connect($db_map[$dbname]['host'],$db_map[$dbname]['user'],$db_map[$dbname]['pass']);
	if(mysql_error()){
		$con=mysql_connect($db_map[$dbname]['host'],$db_map[$dbname]['user'],$db_map[$dbname]['pass']);
	}
	if(isset($db_map[$dbname]['dtbs'])){
		mysql_select_db($db_map[$dbname]['dtbs'],$con);
	}
	if(isset($db_map[$dbname]['charts'])){
		mysql_query("set names ".$db_map[$dbname]['charts'],$con);
	}
	mysql_query("set interactive_timeout=24*3600");
	mysql_query("set wait_timeout=24*3600");
    mysql_select_db($db_map[$dbname]['dtbs'], $con);
	return $con;
}

function connect_tongji_db()
{
    global $conninfo;
    $conn = mysql_connect($conninfo['host'].":3306",$conninfo['username'],$conninfo['password']) or die("<font color=red>不能连接管理数据库1！</font>".mysql_error());
    $db = mysql_select_db(TONGJI_DB,$conn);
    mysql_query("set names utf8");
}

// 切换数据库
function connect_db($conninfo, $database) {
	$conn = mysql_connect($conninfo['host'].":".$conninfo['port'], $conninfo['username'],$conninfo['password']) or die("<font color=red>无法连接目标区组</font>".mysql_error());
	$db = mysql_select_db($conninfo[$database], $conn);
    echo mysql_error();
	mysql_query("set names utf8");
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
$conn = mysql_connect($conninfo['host'],$conninfo['username'],$conninfo['password']) or die("<font color=red>不能连接管理数据库1！</font>".mysql_error());
$db = mysql_select_db(TONGJI_DB,$conn);
require_once "func.php";

$sqlversion = mysql_get_server_info();
if ($sqlversion >= '4.1') {
	mysql_query("set names utf8");
	if ($sqlversion > '5.0.1') {
		mysql_query("SET sql_mode=''");
	}
}

/* 对用户传入的变量进行转义操作*/
if (!get_magic_quotes_gpc()) {
	$_GET = addslashes_deep($_GET);
	$_POST = addslashes_deep($_POST);
	$_COOKIE = addslashes_deep($_COOKIE);
}

define('SESSION_USERID',"game_userid");
define('SESSION_USERNAME',"game_username");
define('SESSION_USERGROUP',"game_usergroup");
define("ROOT",dirname(dirname(__FILE__))."/");//定义文件夹根目录
define('CORE_DIR', ROOT);
define('FUNCTION_DIR',CORE_DIR.'/include/');
define('CLASS_DIR',CORE_DIR.'/include/class/');

define('CACHE_DIR',CORE_DIR."/cache");
define('SITE_URL',CORE_DIR.'/webroot');
define('COMM_PATH', FUNCTION_DIR."/php_compress/comm.txt");//comm.txt 文件路径
define('MAP_PATH', FUNCTION_DIR."/php_compress/map.txt");//map.txt 文件路径
define('SEND_PATH', FUNCTION_DIR."/php_compress/send.lua");//send.lua 文件路径

$now = time();
$today = date("Y-m-d");
$yestoday = date("Y-m-d",$now-86400);

set_time_limit(30);

date_default_timezone_set(TIME_ZONE);

?>
