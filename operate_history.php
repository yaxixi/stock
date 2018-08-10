<?php
require_once "include/config.php";
check_login();

if (isset($_REQUEST['ac'])) {
	$ac = strtolower(trim($_REQUEST['ac']));
	switch ($ac) {
		case "list" : check_priv("priv_user");show_list();break;
		default : check_priv("priv_user");show_list();
	}
}
else {
	show_list();
}

function handle_chinese($str)
{
	if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str)>0)
	{
		$str = mysql_real_escape_string($str);
		$str = str_replace("\\", "\\\\", $str);
	}

	return $str;
}

function show_list() {
	connect_tongji_db();

    $admin_username = "";
	if (isset($_REQUEST["admin_username"]))
	{
		$admin_username = mysql_real_escape_string($_REQUEST["admin_username"]);
		$_SESSION["admin_username"] = $_REQUEST["admin_username"];
	}
	elseif (isset($_SESSION["admin_username"]))
        $admin_username = mysql_real_escape_string($_SESSION["admin_username"]);

	$op_keyword = "";
	if (isset($_REQUEST["op_keyword"]))
	{
		$op_keyword = mysql_real_escape_string($_REQUEST["op_keyword"]);
		$_SESSION["op_keyword"] = $_REQUEST["op_keyword"];
	}
	elseif (isset($_SESSION["op_keyword"]))
		$op_keyword = mysql_real_escape_string($_SESSION["op_keyword"]);

	$request_keyword = "";
	if (isset($_REQUEST["request_keyword"]))
	{
		$request_keyword = $_REQUEST["request_keyword"];
		$request_keyword = handle_chinese($request_keyword);

		$_SESSION["request_keyword"] = $_REQUEST["request_keyword"];
	}
	elseif (isset($_SESSION["request_keyword"]))
	{
		$request_keyword = $_SESSION["request_keyword"];
		$request_keyword = handle_chinese($request_keyword);
	}

	$condition = "";

    if (strlen($admin_username) > 0)
    {
        $condition = "where admin_username = '$admin_username'";
    }

    if (strlen($op_keyword) > 0)
    {
        if (strlen($condition) > 0)
        {
            $condition = $condition . " and op like '%$op_keyword%'";
        }
        else
        {
            $condition = "where op like '%$op_keyword%'";
        }
	}

	if (strlen($request_keyword) > 0)
    {
        if (strlen($condition) > 0)
        {
            $condition = $condition . " and request like '%$request_keyword%'";
        }
        else
        {
            $condition = "where request like '%$request_keyword%'";
        }
	}

	(!isset($_GET['flag'])) ? ($flag = 1) : ($flag = (int)$_GET['flag']);
	$page_size = 50;
	$query = "select count(*) from history $condition";
	// var_dump($query);

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
	$query = "select * from history $condition order by id desc limit ".($page-1)*$page_size.","."$page_size";
	$result = mysql_query($query);
	require_once "templates/operate_history.php";
}

?>
