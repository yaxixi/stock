<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理中心_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function juge(theForm) {
	if (theForm.account.value=="") {
		alert("请输入账号！");
		theForm.account.focus();
		return false;
	}
    return true;
}
</script>
</head>

<body id="main">
<div class="title">
	<h3 class="welH3">
		<span>接管账号</span>
	</h3>
</div>
<div class="info clearfix">
	<form action="account.php" method="post" onsubmit="return juge(this)">
		<input type="hidden" name="ac" value="account_take_over_post" />
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100">接管账号</td>
				<td><input type="text" name="account" class="inpw" /></td>
			</tr>
			<tr>
				<td>接管密码</td>
				<td><input type="text" name="password" class="inpw" value="1" /><span class="redit">当密码为空时，就是取消接管密码</span></td>
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
