<?php
require_once "include/config.php";
require_once "include/network.php";
require_once "include/php_compress/SmartCompress.php";
check_login();

// 处理玩家账号相关操作
$ac = strtolower(trim($_REQUEST['ac']));
switch ($ac) {
    case "account_take_over": check_priv("priv_gm_info");account_take_over();break;
    case "account_take_over_post": check_priv("priv_gm_info");account_take_over_post();break;
	default : check_priv("priv_gm_info");account_take_over();
}

// 显示账号接管UI
function account_take_over() {
    clear_result();
    require_once "templates/account_take_over.php";
}

// 发送账号接管请求
function account_take_over_post() {
    $account = trim($_POST['account']);
	if (!$account) {
		alert_back("请输入账号！");
	}
	$password = trim($_POST['password']);
	if (!$password) {
		$password = "";
    }

    //链接游戏服务器
    $object = new SmartCompress(SERVER_IP, SERVER_PORT);

    //发送服务器验证命令
    $object->cmd_internal_auth();

    // 构造消息
    $cmd = array();
    $cmd[] = "cmd_gm_command";
    $cmd[] = "take_over_account";
    $cmd[] = array(
        'account'=>$account,
        'password'=>$password,
    );

    $object->sendMessage($cmd);

    $result = $object->recvMessage();
    $object->closeSocket();

    if ($result['ret'] != 0) {
        alert("操作失败！");
    }
    else
    {
        alert("操作成功！");
    }
    $_SESSION["result"] = $result['ret'];
	history_add("take_over_account", $account);
    redirect("account.php?ac=account_take_over");
}

?>
