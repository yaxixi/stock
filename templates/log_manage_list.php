<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理中心_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function db_select() {
	var db_name_from_type = '<?php echo substr($db_name,0,5);?>';
	var db_name = document.getElementById('db_name').value;
	var db_name_to_type = db_name.substr(0,5);
	var db_name_to_date = db_name.substr(6,6);
	db_name_to_date = '20'+db_name_to_date.substr(0,2)+'-'+db_name_to_date.substr(2,2)+'-'+db_name_to_date.substr(4,2);
	if (db_name_from_type != db_name_to_type) {//切换到不同种表
		window.location='?host_name=<?php echo $host_name;?>&db_name='+document.getElementById('db_name').options[document.getElementById('db_name').selectedIndex].value;
	} else {//切换到同种表
		document.getElementById('ldate').value = db_name_to_date;
		document.getElementById('udate').value = db_name_to_date;
	}
}
</script>
</head>

<body id="main">
<div class="title">
	<h3>
	  <form action="log_manage.php" method="get">
	  <input type="hidden" name="ac" value="query_log_info" />
	    主机:
	    <select name="host_name" onchange="window.location='?host_name='+this.options[this.selectedIndex].value">
	    	<option value="1"<?php if($host_name == 1) {echo "selected='selected'";}?>>线上</option>
	    </select>
		库名:
		<select id="db_name" name="db_name" onchange="db_select();">
		<?php
			foreach ($log_db_list as $v) {
				if ($db_name == $v) {
					echo "<option value='$v' selected='selected'>$v</option>";
				} else {
					echo "<option value='$v'>$v</option>";
				}
			}
		?>
	    </select>
		表名:
		<select name="table_name" onchange="window.location='?host_name=<?php echo $host_name;?>&db_name=<?php echo $db_name;?>&table_name='+this.options[this.selectedIndex].value">
	    <?php
	    	mysql_data_seek($result1,0);//再执行mysql_fetch_array时，移动内部结果的指针到首位。
			while ($row = mysql_fetch_array($result1)) {
				if ($table_name == $row[0]) {
					echo "<option value='$row[0]' selected='selected'>$row[0]</option>";
				} else {
					echo "<option value='$row[0]'>$row[0]</option>";
				}
			}
		?>
		</select>
		类型:
		<select name="log_type" onchange="window.location='?host_name=<?php echo $host_name;?>&db_name=<?php echo $db_name;?>&table_name=<?php echo $table_name;?>&log_type='+this.options[this.selectedIndex].value">
		<option></option>
		<?php
			foreach ($log_type_list as $k => $v) {
				if ($log_type == $k) {
					echo "<option value='$k' selected='selected'>$v</option>";
				} else {
					echo "<option value='$k'>$v</option>";
				}
			}
		?>
	    </select>
		开始日期
		<script type="text/javascript" src="js/calendar.js"></script>
		<input type="text" id="ldate" name="ldate" value="<?php echo $ldate;?>" onclick="calendar.show(this);" size="10" maxlength="10" readonly="readonly" />
		时间:
		<input type="text" name="time_start" value="<?php echo $time_start;?>" size="10" maxlength="8" />
		=&gt;
		结束日期
		<input type="text" id="udate" name="udate" value="<?php echo $udate;?>" onclick="calendar.show(this);" size="10" maxlength="10" readonly="readonly" />
		时间:
		<input type="text" name="time_end" value="<?php echo $time_end;?>" size="10" maxlength="8" /><br />
		  p1(<?php if($log_type) {echo $log_data[$log_type][2];} ?>):
          <input name="p1" type="text" value="<?php echo $p1;?>" style="width:200px;" />
		  p2(<?php if($log_type) {echo $log_data[$log_type][3];} ?>):
          <input name="p2" type="text" value="<?php echo $p2;?>" style="width:200px;" />
		  p3(<?php if($log_type) {echo $log_data[$log_type][4];} ?>):
          <input name="p3" type="text" value="<?php echo $p3;?>" style="width:300px;" />
		  <input type="submit" value="查询" />
		</form>
	</h3>
</div>

<div class="info">
	<table cellpadding="0" cellspacing="0" width="100%" class="fix">
		<tr class="infobg">
			<th width="112">日期时间</th>
			<th width="98">日志类型</th>
			<th width="143">详细1</th>
			<th width="180">详细2</th>
			<th width="168">详细3</th>
			<th width="165">备注</th>
		</tr>
		<?php
		foreach ($data as $key => $value) {
		?>
		<tr>
			<td><?php echo $value['time'];?></td>
			<td><?php echo $log_data[$value['id']][1];?></td>
			<td title="<?php echo $log_data[$value['id']][2];?>"><?php echo analysis_log_arg($log_data[$value['id']][2],$value['p1']);?></td>
			<td title="<?php echo $log_data[$value['id']][3];?>"><?php echo analysis_log_arg($log_data[$value['id']][3],$value['p2']);?></td>
			<td title="<?php echo $log_data[$value['id']][4];?>"><?php echo analysis_log_arg($log_data[$value['id']][4],$value['p3']);?></td>
			<td title="<?php echo $log_data[$value['id']][5];?>"><?php echo analysis_log_arg($log_data[$value['id']][5],$value['memo']);?></td>
		</tr>
		<?php
		}
		?>
	</table>
</div>

<?php
//if ($ldate == $udate) {
	echo page_ex($page_size,$page,"ac=query_log_info&host_name=$host_name&db_name=$db_name&table_name=$table_name&log_type=$log_type&ldate=$ldate&time_start=$time_start&udate=$udate&time_end=$time_end&p1=$p1&p2=$p2&p3=$p3");
//}
require_once "footer.php";
?>
</body>
</html>
