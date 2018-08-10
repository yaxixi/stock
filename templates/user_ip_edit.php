<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理中心_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body id="main">
<div class="title">
	<h3 class="welH3">
		<span>允许登录后台的IP</span>
	</h3>
</div>
<div class="info clearfix">
	<form action="user.php" method="post">
		<input type="hidden" name="ac" value="ip_edit_post" />
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="60">IP列表<br />每行一个IP</td>
				<td><textarea name="ip" class="textarea_inp"><?php echo $allow_ips;?></textarea></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="确定" /></td>
			</tr>
		</table>
	</form>
</div>
<?php
require "footer.php";
?>
</body>
</html>
