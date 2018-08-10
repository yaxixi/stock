<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理中心_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function juge(theform) {
	if (theform.oldpassword.value == "") {
		alert("请输入原密码！");
		theform.oldpassword.focus();
		return false;
	}
	if (theform.newpassword.value == "") {
		alert("请输入新密码！");
		theform.newpassword.focus();
		return false;
	}
	if (theform.newpassword.value.length < 6) {
		alert("密码长度至少要6位！");
		theform.newpassword.focus();
		return false;
	}
	if (theform.checkpassword.value != theform.newpassword.value) {
		alert("确认密码与新密码不一致！");
		theform.checkpassword.focus();
		return false;
	}
}
</script>
</head>

<body id="main">
<div class="title">
	<h3 class="welH3"><span>修改密码</span></h3>
</div>
<div class="info">
	<form action="password.php" method="post" onsubmit="return juge(this)">
		<input type="hidden" name="ac" value="edit_post" />
		<table cellpadding="0" cellspacing="0" width="100%" class="fix">
			<tr>
				<td align="right" width="140" class="infobg">用户名：</td>
				<td><?php echo $_SESSION[SESSION_USERNAME];?></td>
			</tr>
			<tr>
				<td align="right" class="infobg">原密码：</td>
				<td><input type="password" name="oldpassword" size="20" maxlength="20" class="inpw" /><span class="redit">*</span></td>
			</tr>
			<tr>
				<td align="right" class="infobg">新密码：</td>
				<td><input type="password" name="newpassword" size="20" maxlength="20" class="inpw" /><span class="redit">*</span></td>
			</tr>
			<tr>
				<td align="right" class="infobg">确认密码：</td>
				<td><input type="password" name="checkpassword" size="20" maxlength="20" class="inpw" /><span class="redit">*</span></td>
			</tr>
			<tr>
				<td colspan="2"><div class="div_submit"><input type="submit" value="确定" class="btn" /> <input type="reset" value="重置" class="btn" /></div></td>
			</tr>
		</table>
	</form>
</div>
<?php
require "footer.php";
?>
</body>
</html>
