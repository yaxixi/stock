<?php
require_once "include/config.php";
check_login();

$ac = strtolower(trim($_REQUEST['ac']));
switch ($ac) {
	default : check_priv("log_manage");show_log_list($ac);
}

// 日志参数解析
function analysis_log_arg($log_string, $value) {
	$tbl = explode(":", $log_string);
	if ($tbl[0] and $tbl[1]) {
		$csv_list = explode("+", $tbl[1]);//得到csv对应关系
		//print_r($csv_list);
		foreach ($csv_list as $k => $v) {
			$csv_list_exp = explode(".", $v);
			if (!$csv_list_exp[1]) {
				$csv_list_exp[1] = 1;//默认为1
			}
			//print_r($csv_list_exp);
			$file_name = "tongji/include/data/" . $csv_list_exp[0] . ".csv";
			//echo $file_name.' ';
			$handle = fopen($file_name,"r");//读取日志配置信息
			$num_row = 0;
			while ($data_csv = fgetcsv($handle, 1000, ",")) {
				if ($num_row > 0) {
					if ($data_csv[0] == $value) {
						$data_value = $data_csv[$csv_list_exp[1]];
						break;
					}
				}
				$num_row++;
			}
			fclose($handle);
		}
	}

	if ($data_value) {
		return $data_value;
	}else {
		return $value;
	}
}

function show_log_list($ac) {
	$host_name = (int)$_GET['host_name'];
	$db_name = str_html($_GET['db_name']);
	$table_name = str_html($_GET['table_name']);
	$ldate = str_html($_GET['ldate']);
	$udate = str_html($_GET['udate']);
	$time_start = str_html($_GET['time_start']);
	$time_end = str_html($_GET['time_end']);
	$log_type = str_html($_GET['log_type']);
	$p1 = str_html($_GET['p1']);
	$p2 = str_html($_GET['p2']);
	$p3 = str_html($_GET['p3']);

	$host_name = $host_name ? $host_name : '1';
	$time_start = $time_start ? $time_start : '00:00:00';
	$time_end = $time_end ? $time_end : '23:59:59';

	if ($host_name == 1) {
		db('hldb1');
	} else {
		db('hldb1');
	}

	$db_list = mysql_list_dbs();
	while ($db = mysql_fetch_object($db_list)){
		if (strstr($db->Database, "hldb")) {
			$log_db_list[] = $db->Database;//该主机下所有数据库名称
		}
	}

	if (!$db_name) {
		$db_name = $log_db_list[0];
	}
	mysql_select_db($db_name);
	if (!$ldate) {
        $ldate = date("Y-m-d");
	}
	if (!$udate) {
        $udate = date("Y-m-d");
	}

	$result1 = mysql_query("show tables");
	if (!$table_name) {
		$table_name = mysql_result($result1,0);
	}

	if ($ldate > $udate) {
		alert_back("开始日期不能大于结束日期！");
	}
	$ldate_unix = strtotime($ldate);
	$udate_unix = strtotime($udate);
	$num_day = ($udate_unix - $ldate_unix) / 86400;
	for ($i = 0; $i <= $num_day; $i++) {
		$day_list[date("Y-m-d",$ldate_unix + $i *86400)] = date("ymd",$ldate_unix + $i *86400);
	}
	//print_r($day_list);

    setlocale(LC_ALL, 'en_US.UTF-8');
	$handle = fopen("include/data/log_type_info.csv","r");//读取日志配置信息
	$num_row = 0;
	while ($data_csv = fgetcsv($handle, 1000, ",")) {
		if ($num_row > 0) {
			$log_data[$data_csv[0]]= $data_csv;
			if ($data_csv[6] == $table_name) {
				$log_type_list[$data_csv[0]] = $data_csv[1];
			}
		}
		$num_row++;
	}
	fclose($handle);

	//foreach ($day_list as $key => $value) {
		$add_sql = "time between '$ldate $time_start' and '$udate $time_end'";
		if ($log_type) {
			$add_sql .= " and id = $log_type";
		}
		if ($p1) {
			$add_sql .= " and p1 = '$p1'";
		}
		if ($p2) {
			$add_sql .= " and p2 = '$p2'";
		}
		if ($p3) {
			$add_sql .= " and p3 = '$p3'";
		}

	   // if ($ldate == $udate) {//同日期查询，分页
			$page_size = 200;
			if (!$_GET['page']) {
				$page = 1;
			} else {
				$page = (int)$_GET['page'];
				if ($page <= 0) {
					$page = 1;
				}
			}
			if ($ac == "query_log_info") {
				$query = "select * from $table_name where $add_sql limit " . ($page-1)*$page_size . "," . "$page_size";
				//echo $query . "</br>";
				$result = mysql_query($query);
				while ($row = mysql_fetch_assoc($result)) {
					$data[] = $row;
				}
			}
	   /* } else {//跨日期查询，不分页
			$page_size = 1000;
			if ($ac == "query_log_info") {
				if ($key == date("Y-m-d")) {//今天
					db('hldb1');
					$db_name_v = $db_name;
				} else {
					db('hldb1');
					$db_name_v = $db_name;
				}
				$page_size_x = $page_size + 1;
				$query = "select * from $db_name_v.$table_name where $add_sql limit $page_size_x";//先判断是否太多了
				$result = mysql_query($query);
				$num_one = mysql_num_rows($result);
				if ($num_one > $page_size) {
					alert_back("数据超过{$page_size}条，请减少！");
				}
				$query = "select * from $db_name_v.$table_name where $add_sql limit $page_size";
				//echo $query . "</br>";
				$result = mysql_query($query);
				while ($row = mysql_fetch_assoc($result)) {
					$data[] = $row;
				}
				$num_all = count($data);
				if ($num_all > $page_size) {
					alert_back("数据超过{$page_size}条，请减少！");
				}
			}
       }   */
   // }
	//print_r($data);

	require_once "templates/log_manage_list.php";
}
?>
