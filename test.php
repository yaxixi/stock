<?php

require_once dirname (__FILE__)."/include/php_compress/SmartCompress.php";

define('COMM_PATH', dirname(__FILE__)."/include/php_compress/comm.txt");//comm.txt 文件路径
define('MAP_PATH', dirname(__FILE__)."/include/php_compress/map.txt");//map.txt 文件路径
define('SEND_PATH', dirname(__FILE__)."/include/php_compress/send.lua");//send.lua 文件路径

//链接游戏服务器
$object = new SmartCompress("localhost", 9001);

//发送服务器验证命令
$object->cmd_internal_auth();

$object->test_cmds();
?>
