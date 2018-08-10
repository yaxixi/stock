<?php
require_once "include/config.php";
check_login();

$user_id = $_SESSION[SESSION_USERID];
$query = "select * from user where uid='$user_id'";
$result = mysql_query($query);
$row = mysql_fetch_array($result);
require_once "templates/welcome.php";
?>