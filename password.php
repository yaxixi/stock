<?php
require_once "include/config.php";
check_login();

if (isset($_REQUEST['ac'])) {
	$ac = strtolower(trim($_REQUEST['ac']));
	switch ($ac) {
		case "edit" : edit();break;
		case "edit_post" : edit_post();break;
		default : edit();
	}
} else {
	edit();
}
function edit() {
	require_once "templates/password_edit.php";
}

function edit_post() {
	$username = $_SESSION[SESSION_USERNAME];
	$oldpassword = str_html($_POST['oldpassword']);
	$newpassword = str_html($_POST['newpassword']);
	if (strlen($newpassword) < 6) {
		echo "<script type=\"text/javascript\">alert('新密码不符合规定！');history.go(-1);</script>";
		exit;
	}
	$query = "select * from user where username='$username'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$salt = $row['salt'];
	$oldpassword = md5(md5($oldpassword).$salt);
	$newpassword = md5(md5($newpassword).$salt);
	$query = "select count(*) from user where username='$username' and password='$oldpassword'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if ($row[0] < 1) {
		echo "<script type=\"text/javascript\">alert('原密码错误！');history.go(-1);</script>";
		exit;
	}
	$query = "update user set password='$newpassword' where username='$username'";
	$result = mysql_query($query);
	processing($result,"修改密码","password.php","1");
}
?>