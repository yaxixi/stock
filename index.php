<?php
require_once "include/config.php";
if(!isset($_SESSION))
    session_start();

/*
require_once "allow_ips.php";
$allow_ips = explode('|',$allow_ips);

if (strpos($_SERVER['HTTP_USER_AGENT'],'4399xpdl') === false) {
	if (!in_array($_SERVER['REMOTE_ADDR'],$allow_ips)) {
		echo "无法操作，您的IP(".$_SERVER['REMOTE_ADDR'].")不在允许范围内";
		exit;
    }
}*/

if (isset($_REQUEST['ac'])) {
	$ac = strtolower(trim($_REQUEST['ac']));
	switch ($ac) {
		case "login" : login();break;
		case "login_post" : login_post();break;
		case "logout" : logout();break;
	}
} else {
	login();
}

function login() {
	if (isset($_SESSION[SESSION_USERID])) {
		header("location:admin.php");
		exit;
	} else {
		require_once "templates/index.php";
	}
}

function login_post() {
	$username = str_html($_POST['username']);
	$password = str_html($_POST['password']);
	$query = "select * from user where username='$username'";//取该用户的salt
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if ($row) {
		$salt = $row['salt'];
	} else {
		echo "<script type=\"text/javascript\">alert('用户名或者密码错误，登录失败1！');history.go(-1);</script>";
		exit;
	}
	$password = md5(md5($password).$salt);
	$query = "select * from user where username='$username' and password='$password'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if ($row) {
		if ($row['flag'] == 0) {
			alert_back("该账号已停用");
			exit;
		}

		$time = date("Y-m-d H:i:s");
		$ip = get_online_ip();
		$query = "update user set login_time='$time',login_ip='$ip' where username='$username'";
		mysql_query($query);
		$_SESSION[SESSION_USERID] = $row['uid'];
		$_SESSION[SESSION_USERNAME] = $row['username'];
		//$_SESSION[SESSION_USERGROUP] = $row['usergroup_id'];
		header("location:admin.php");
	} else {
		echo "<script type=\"text/javascript\">alert('用户名或者密码错误，登录失败2！');history.go(-1);</script>";
	}
}

function logout() {
	unset($_SESSION[SESSION_USERID]);
	unset($_SESSION[SESSION_USERNAME]);
	//unset($_SESSION[SESSION_USERGROUP]);
	//session_destroy();
	header("location:index.php");
}
?>
