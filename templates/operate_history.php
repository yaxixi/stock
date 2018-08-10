<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理中心_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/func.js"></script>
<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	<?php
		if (isset($_SESSION["op_keyword"]))
			echo "$('#op').val('".$_SESSION["op_keyword"]."');";

		if (isset($_SESSION["request_keyword"]))
			echo "$('#request').val('".$_SESSION["request_keyword"]."');";

        if (isset($_SESSION["admin_username"]))
			echo "$('#admin_username').val('".$_SESSION["admin_username"]."');";
	?>
});

function on_click_ok() {
	// 过滤条件
	var op_keyword = $('#op').val() || "";
	var request_keyword = $('#request').val() || "";
    var admin_username = $('#admin_username').val() || "";

	// 查询
	post("operate_history.php", {
		ac:"list",
		op_keyword:op_keyword,
        request_keyword:request_keyword,
        admin_username:admin_username});
}
</script>

<body id="main">
<div class="title">
	<h3 class="welH3">
		<span>操作记录
		</span>
	</h3>
</div>
<div class="info">
    <tr>
		<td width="60">管理员帐号</td>
		<td><input type="text" id="admin_username" class="inpw" /></td>
	</tr>
	<tr>
		<td width="60">操作关键字</td>
		<td><input type="text" id="op" class="inpw" /></td>
	</tr>
	<tr>
		<td width="60">请求关键字</td>
		<td><input type="text" id="request" class="inpw" /></td>
	</tr>
	<tr>
		<td>
		    <button class="inpw" onclick="on_click_ok();">查询</button>
		</td>
	</tr>
    </br>
    </br>
	<table cellpadding="0" cellspacing="0" width="100%" class="fix">
		<tr class="infobg">
			<th width="100" >管理员账号</th>
			<th width="200" >操作</th>
			<th width="50%" >请求</th>
			<th width="20%" >结果</th>
			<th width="200" >时间</th>
		</tr>
		<?php
		while ($row = mysql_fetch_array($result)) {
		?>
		<tr>
			<td><?php echo $row['admin_username'];?></td>
			<td align="center"><?php echo $row['op'];?></td>
			<td align="center"><?php echo $row['request'];?></td>
			<td align="center"><?php echo $row['response'];?></td>
			<td align="center"><?php echo $row['time'];?></td>
		</tr>
		<?php
		}
		?>
	</table>
</div>
<?php
echo page($num,$page_size,$page_count,$page,"");
require "footer.php";
?>
</body>
</html>
