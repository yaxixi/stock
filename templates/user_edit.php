<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理中心_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function juge(theform) {
	if (theform.username.value == "") {
		alert("请输入用户名！");
		theform.username.focus();
		return false;
	}
	if (theform.password.value != "" && theform.password.value.length < 6) {
		alert("密码长度至少要6位！");
		theform.password.focus();
		return false;
	}
}
</script>
</head>

<body id="main">
<div class="title">
	<h3 class="welH3"><span>编辑用户</span></h3>
</div>
<div class="info">
	<form action="user.php" method="post" onsubmit="return juge(this)">
		<input type="hidden" name="ac" value="edit_post" />
		<input type="hidden" name="id" value="<?php echo $row['id'];?>" />
		<?php if ($row['id'] == 1) {?>
			<input type="hidden" name="username" value="<?php echo $row['username'];?>" />
			<input type="hidden" name="flag" value="1" />
		<?php }?>
		<table cellpadding="0" cellspacing="0" width="100%" class="fix">
			<tr>
				<td align="right" width="140" class="infobg">用户名：</td>
				<td><input type="text" name="username" class="inpw" value="<?php echo $row['username']?>"
					<?php if ($row['id'] == 1) echo "disabled='true'" ?> /><span class="redit">*</span></td>
			</tr>
			<tr>
				<td align="right" class="infobg">密码：</td>
				<td><input type="text" name="password" class="inpw" /><span class="redit">*（不少于6位数的英文字母或者数字，为空表示不修改密码）</span></td>
			</tr>
			<tr>
				<td align="right" width="140" class="infobg">姓名：</td>
				<td><input type="text" name="realname" class="inpw" value="<?php echo $row['realname']?>" /></td>
			</tr>
			<tr>
				<td align="right" width="140" class="infobg">状态：</td>
				<td>
					<input type="radio" name="flag" <?php if ($row['flag'] == 1) echo ' checked="checked"';
				        if ($row['id'] == 1) echo "disabled='true'"?> value="1" />正常
				    <input type="radio" name="flag" <?php if ($row['flag'] == 0) echo ' checked="checked"';
				        if ($row['id'] == 1) echo "disabled='true'"?> value="0" />停用
				</td>
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
