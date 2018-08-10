<?php
require_once "include/config.php";
check_login();

if (isset($_REQUEST['ac'])) {
	$ac = strtolower(trim($_REQUEST['ac']));
	switch ($ac) {
		case "list" : check_priv("priv_user");show_list();break;
		case "add" : check_priv("priv_user");add();break;
		case "add_post" : check_priv("priv_user");add_post();break;
		case "edit" : check_priv("priv_user");edit();break;
		case "edit_post" : check_priv("priv_user");edit_post();break;
		case "priv_edit" : check_priv("priv_user");priv_edit();break;
		case "priv_edit_post" : check_priv("priv_user");priv_edit_post();break;
		case "delete" : check_priv("priv_user");delete();break;
		case "ip_edit" : check_priv("priv_user");ip_edit();break;
		case "ip_edit_post" : check_priv("priv_user");ip_edit_post();break;
		default : check_priv("priv_user");show_list();
	}
}
else {
	show_list();
}

function show_list() {
	(!isset($_GET['flag'])) ? ($flag = 1) : ($flag = (int)$_GET['flag']);
	$page_size = 50;
	$query = "select count(*) from user where flag=$flag";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$num = $row[0];
	$page_count = ceil($num/$page_size);
	$page = isset($_GET['page'])? (int)$_GET['page'] : 0;
	if ($page < 1) {
		$page = 1;
	} else {
		if ($page > $page_count && $page_count > 0) {
			$page = $page_count;
		}
	}
	$query = "select * from user where flag=$flag order by id desc limit ".($page-1)*$page_size.","."$page_size";
	$result = mysql_query($query);
	require_once "templates/user_list.php";
}

function add() {
	require_once "templates/user_add.php";
}

function edit() {
	$id = (int)$_GET['id'];
	$query = "select * from user where id='$id'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if ($row) {
		require_once "templates/user_edit.php";
	}
}

function priv_edit() {
	$id = (int)$_GET['id'];
	$query = "select * from user where id='$id'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$priv = $row['priv'];
	$priv = explode(',',$priv);
	if ($row) {
		require_once "templates/user_priv_edit.php";
	}
}

function add_post() {
	$username = str_html($_POST['username']);
	$password = str_html($_POST['password']);
	$realname = str_html($_POST['realname']);
    $uid = str_html($_POST['uid']);
	$email = str_html($_POST['email']);
	$time = date("Y-m-d H:i:s");
	$flag = 1;
	$salt = rand(100000,999999);
	if (!$username) {
		alert_back("用户名不能为空！");
	}
	if (strlen($password) < 6) {
		alert_back("新密码不符合规定！");
    }
    if (!$uid) {
		alert_back("用户ID不能为空！");
	}
	$password = md5(md5($password).$salt);
	$query = "insert into user(username,password,realname,flag,salt,uid) values ('$username','$password','$realname','$flag','$salt','$uid')";
	$result = mysql_query($query);
	processing($result,"添加","user.php");
}

function edit_post() {
	$id = (int)$_POST['id'];
	$username = str_html($_POST['username']);
	$password = str_html($_POST['password']);
	$realname = str_html($_POST['realname']);
	$flag = (int)$_POST['flag'];
	$salt = rand(100000,999999);
	if ($_SESSION[SESSION_USERID] != "admin") {
		alert_back("你无法修改该用户！");
	}
	if (!$username) {
		alert_back("用户名不能为空！");
	}
	if (strlen($password) == 0) {
		$query ="update user set username='$username',realname='$realname',flag='$flag' where id='$id'";
	} else {
		if (strlen($password) < 6) {
			alert_back("新密码不符合规定！");
		}
		$password = md5(md5($password).$salt);
		$query ="update user set username='$username',password='$password',realname='$realname',flag='$flag',salt='$salt' where id='$id'";
	}
	$result = mysql_query($query);
	processing($result,"编辑","user.php?ac=list");
}

function priv_edit_post() {
	$id = (int)$_POST['id'];
	if ($_SESSION[SESSION_USERID] != "admin") {
		alert_back("你无法修改用户权限！");
	}

	$priv = dhtmlspecialchars($_POST['priv']);
	$priv_txt = '';
	if (is_array($priv)) {
		foreach ($priv as $value) {
			$priv_txt .= $value.',';
		}
	}

	if ($id == 1) {
		$priv_txt .= "priv_user";
	}

	$priv_txt = trim($priv_txt,',');
	$query = "update user set priv='$priv_txt' where id='$id'";
	$result = mysql_query($query);
	processing($result,"设置权限","user.php?ac=list");
}

function delete() {
	$id = (int)$_GET['id'];
	if ($id == 1) {
		alert_back("该用户不可删除！");
	}
	$query = "delete from user where id='$id'";//删除该用户
	$result = mysql_query($query);
	processing($result,"删除","user.php?ac=list");
}

function ip_edit() {
	require_once "allow_ips.php";
	$allow_ips = str_replace('|',chr(10),$allow_ips);
	require_once "templates/user_ip_edit.php";
}

function ip_edit_post() {
	$ip = dhtmlspecialchars($_POST['ip']);
	$ip = str_replace(chr(10),"|",$ip);
	$ip = str_replace(chr(13),"",$ip);
	$ip = '<?php $allow_ips="'.$ip.'";?>';
	$result = file_put_contents("allow_ips.php",$ip);
	if ($result) {
		alert_back("修改成功！");
	} else {
		alert_back("修改失败，请重试！");
	}
}
?>
