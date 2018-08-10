<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理中心_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
<script type="text/javascript">
function selectall(obj){
	var m = $('#'+obj+' input[name="priv[]"]');
	for (var i=0;i<m.length;i++) {
		m[i].checked = true;
	}
}
function selectother(obj){
	var m = $('#'+obj+' input[name="priv[]"]');
	for (var i=0;i<m.length;i++) {
		if (m[i].checked == true) {
			m[i].checked = false;
		} else {
			m[i].checked = true;
		}
	}
}
function clearall(obj) {
	var m = $('#'+obj+' input[name="priv[]"]');
	for (var i=0;i<m.length;i++) {
		m[i].checked = false;
	}
}
</script>
</head>

<body id="main">
<div class="title">
	<h3 class="welH3"><span>设置权限&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:selectall('all');">全选</a>/<a href="javascript:selectother('all');">反选</a>/<a href="javascript:clearall('all');">不选</a></span></h3>
</div>
<div class="info">
	<form action="user.php" method="post">
		<input type="hidden" name="ac" value="priv_edit_post" />
		<input type="hidden" name="id" value="<?php echo $row['id'];?>" />
		<table cellpadding="0" cellspacing="0" width="100%" class="fix" id="all">
			<tr>
				<td class="infobg">GM工具&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:selectall('gmgj');">全选</a>/<a href="javascript:selectother('gmgj');">反选</a>/<a href="javascript:clearall('gmgj');">不选</a></td>
			</tr>
			<tr>
				<td id="gmgj">
                    <input type="checkbox" name="priv[]" value="tongji_main"<?php show_checked($priv,'tongji_main');?> />
					运营数据查询
					<input type="checkbox" name="priv[]" value="priv_gm_info"<?php show_checked($priv,'priv_gm_info');?> />
					GM工具操作
					<input type="checkbox" name="priv[]" value="log_manage"<?php show_checked($priv,'log_manage');?> />
					日志查询
				</td>
			</tr>
			<tr>
				<td><div class="div_submit"><input type="submit" value="确定" class="btn" /> <input type="reset" value="重置" class="btn" /></div></td>
			</tr>
		</table>
	</form>
</div>
<?php
require "footer.php";
?>
</body>
</html>
