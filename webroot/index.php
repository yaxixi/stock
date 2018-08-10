<?php
header("Content-Type:text/html; charset=utf-8");
require_once '../include/config.php';
require_once '../include/smarty/view.php';
$Smarty = new ViewTemplates();

check_login();

if (!$_SESSION[SESSION_USERID]) {
	echo "<script type=\"text/javascript\">alert('请登录！');top.location='../index.php';</script>";
	exit;
} else {
	$sfile=isset($_REQUEST['s'])?$_REQUEST['s']:'main';
}

	include(CORE_DIR.'/source/'.$sfile.'.php');
    $Smarty->assign('sfile',$sfile);
    $Smarty->display(CORE_DIR.'/view/'.$sfile.'.htm');
    //$Smarty->display('../view/'.$sfile.'.htm');
?>
