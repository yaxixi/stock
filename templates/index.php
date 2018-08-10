<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function juge(theForm) {
	return true;
}
</script>
</head>

<body style="background:#eef8e0;">
<div class="loginarea">
	<div><img src="images/login_01.jpg" width="960" height="150" /></div>
	<div><img src="images/login_02.jpg" width="960" height="30" /></div>

	<div class="clearfix">
		<div class="left"><img src="images/login_03.jpg" width="239" height="355" /></div>
		<div class="left">
			<div><img src="images/login_04.jpg" width="721" height="58" /></div>
			<div style="background:url(images/login_05.jpg) no-repeat; width:621px; height:154px; padding:10px 0px 0px 100px;">
				<form name="form1" action="index.php" method="post" onsubmit="return juge(this)">
					<input type="hidden" name="ac" value="login_post" />
					<table class="loginlabel">
						<tr>
							<td align="right">用户名：</td>
							<td><input type="text" name="username" size="15" maxlength="15" style="width:150px;" /></td>
						</tr>
						<tr>
							<td align="right">密码：</td>
							<td><input type="password" name="password" size="20" maxlength="20" style="width:150px;" /></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><input type="submit" value="登录" class="btn" /> <input type="reset" value="重置" class="btn" /></td>
						</tr>
					</table>
				</form>
			</div>
			<div><img src="images/login_06.jpg" width="721" height="133" /></div>
		</div>
	</div>
</div>
</body>
</html>
