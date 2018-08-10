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
		<span>用户列表
		<select id="flag" onchange="window.location='?flag='+this.options[this.selectedIndex].value"><option value="1"<?php if ($flag == 1) echo ' selected="selected"';?>>正常</option><option value="0"<?php if ($flag == 0) echo ' selected="selected"';?>>停用</option></select>
		</span>
	</h3>
</div>
<div class="info">
	<table cellpadding="0" cellspacing="0" width="100%" class="fix">
		<tr class="infobg">
			<th>用户名</th>
			<th width="120">姓名</th>
			<th width="140">最后登录时间</th>
			<th width="120">最后登录IP</th>
			<th width="120">操作</th>
		</tr>
		<?php
		while ($row = mysql_fetch_array($result)) {
		?>
		<tr>
			<td><?php echo $row['username'];?></td>
			<td align="center"><?php echo $row['realname'];?></td>
			<td align="center"><?php echo $row['login_time'];?></td>
			<td align="center"><?php echo $row['login_ip'];?></td>
			<td align="center"><a href="user.php?ac=edit&id=<?php echo $row['id'];?>">编辑</a> <a href="user.php?ac=priv_edit&id=<?php echo $row['id'];?>">设置权限</a> <a href="javascript:if(confirm('确定要删除吗？'))window.location='user.php?ac=delete&id=<?php echo $row['id'];?>'">删除</a></td>
		</tr>
		<?php
		}
		?>
	</table>
</div>
<?php
echo page($num,$page_size,$page_count,$page,"flag=$flag");
require "footer.php";
?>
</body>
</html>
