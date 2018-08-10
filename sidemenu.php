<?php
require_once "include/config.php";
check_login();

$id = $_SESSION[SESSION_USERID];
$query = "select * from user where uid='$id'";
$result = mysql_query($query);
$row = mysql_fetch_array($result);
$priv = $row['priv'];
$priv = explode(',',$priv);
require_once "templates/sidemenu.php";
?>
