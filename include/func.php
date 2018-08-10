<?php

function print_stack_trace() {
        $html = "\n";
        $array = debug_backtrace();
        //print_r($array);//信息很齐全
        //unset($array[0]);
        foreach($array as $row)
        {
                $html .= '调用方法:'.$row['function']."\t\t".$row['file'].':'.$row['line'].'行'." \n";
        }
        var_dump($html);
}

function characet($data) {
    if( !empty($data) ){
        $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
        if( $fileType != 'UTF-8'){
            $data = mb_convert_encoding($data ,'utf-8' , $fileType);
        }
    }
    return $data;
}

function decodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}

//将内容进行UNICODE编码，编码后的内容格式：\u56fe\u7247
function unicode_encode($name)
{
    $name = iconv('UTF-8', 'UCS-2', $name);
    $len = strlen($name);
    $str = '';
    for ($i = 0; $i < $len - 1; $i = $i + 2)
    {
        $c = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0)
        {    // 两个字节的文字
            $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
        }
        else
        {
            $str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
        }
    }
    return $str;
}

// 将UNICODE编码后的内容进行解码，编码后的内容格式：\u56fe\u7247
function unicode_decode($name)
{
	$name = strtolower($name);
    // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches))
    {
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++)
        {
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0)
            {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            }
            else
            {
                $name .= $str;
            }
        }
    }
    return $name;
}

/*递归方式的对变量中的特殊字符进行转义*/
function addslashes_deep($value) {
    if (empty($value)) {
        return $value;
    } else {
        return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
    }
}

//判断是否登录后台
function check_login() {
	if(!isset($_SESSION))
        session_start();

	if (!$_SESSION[SESSION_USERID]) {
        echo "<script type=\"text/javascript\">alert('请登录！');top.location='../../index.php';</script>";
        exit;
	}
}

//判断权限
function check_priv($str) {
    // 获取tongji数据库的连接
    connect_tongji_db();

	global $conn;
	if(!isset($_SESSION))
        session_start();
	$id = $_SESSION[SESSION_USERID];
	$query = "select priv from user where uid='$id'";
	$result = mysql_query($query,$conn);
	$row = mysql_fetch_array($result);
	$priv = $row['priv'];
	$priv = explode(',',$priv);
	if (!in_array($str,$priv)) {
		echo "<script type=\"text/javascript\">alert('对不起，您没有该操作权限！');history.go(-1);</script>";
		exit;
	}
}

function show_checked($priv,$str) {
	if (!in_array($str,$priv)) {
		echo '';
	} else {
		echo ' checked="checked"';
	}
}

function show_a($priv,$str) {
	if ($priv == "*")
		return true;

	if (!in_array($str,$priv)) {
		return false;
	} else {
		return true;
	}
}

//提示字符、返回地址、操作成功时是否弹出提示
function processing($result,$str="操作",$url,$showmsg="0") {
	if ($result) {
		unset($result);
		if ($showmsg == "0") {
			echo "<script type=\"text/javascript\">window.location='".$url."';</script>";//默认，不弹出提示窗口
		} else {
			echo "<script type=\"text/javascript\">alert('".$str."成功！');window.location='".$url."';</script>";//弹出提示窗口
		}
	} else {
		echo "<script type=\"text/javascript\">alert('".$str."失败！".addslashes(mysql_error())."');history.go(-1);</script>";
		exit;
	}
}

//SQL语句如果执行失败，才弹出提示
function query_check($result,$str="操作") {
	if (!$result) {
		unset($result);
		echo "<script type=\"text/javascript\">alert('".$str."失败！".addslashes(mysql_error())."');history.go(-1);</script>";
		exit;
	}
}

function redirect($url) {
	echo "<script type=\"text/javascript\">window.location='".$url."';</script>";
}

function alert_back($str) {
	echo '<script type="text/javascript">alert("'.$str.'");history.go(-1);</script>';
	exit;
}

function alert($str) {
	echo '<script type="text/javascript">alert("'.$str.'");</script>';
}

function clear_result() {
	$_SESSION["result"] = "";
	$_SESSION["cmd_desc"] = "";
	$_SESSION["cmd"] = "";
}

//处理特殊字符
function str_html($str) {
	return htmlspecialchars(trim($str), ENT_QUOTES);
}

//加强型处理特殊字符，可处理数组
function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
		str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}

//字符串截取
function cut($str, $len = 12, $dot = '...') {
	if (strlen($str) > $len) {
		$len = $len - strlen($dot);
	}
	$i = 0;
	$tlen = 0;
	$tstr = '';
	while ($tlen < $len) {
		$chr = mb_substr($str, $i, 1, 'utf8');
		$chrLen = ord($chr) > 127 ? 3 : 1;
		if ($tlen + $chrLen > $len)
			break;
		$tstr .= $chr;
		$tlen += $chrLen;
		$i++;
	}
	if (substr_count($tstr,'<b') != substr_count($tstr,'r />')) {//仅确保<br />标签完整
		$lastpos = strrpos($tstr,'<b');
		$tstr = substr($tstr,0,$lastpos);
	}
	if ($tstr != $str) {
		$tstr .= $dot;
	}
	return $tstr;
}

//为了统计字节数，换行转换4个字节
function enter_str($str) {
	$str = nl2br($str);
	$str = preg_replace('/\r|\n/', '', $str);
	$str = str_replace("<br />", "aaaa", $str);//一个换行4个字符长度，即\r\n
	return $str;
}

//有的换行是\r，有的换行是\n，有的换行是\r\n
function content_show($str) {
	$str = str_replace("\\r\\n", "\\r", $str);
	$str = str_replace("\\r", "<br />", $str);
	$str = str_replace("\\n", "<br />", $str);
	return $str;
}

//按行转换为数组
function br_array($str) {
	$str = nl2br($str);
	$str = preg_replace('/\r|\n/', '', $str);
	return explode('<br />',$str);
}

//求百分比
function percent($a,$b) {
	if (!$b) {
		$str = '0%';
	} else {
		$str = $a*100/$b;
		$str = round($str,2).'%';
	}
	return $str;
}

/*
 * 分页函数
 *
 * @param   int      $num        总数
 * @param   int      $page_size  每页显示数量
 * @param   int      $page_count 总页数
 * @param   int      $page       当前页
 * @param   string   $mpurl      传递的URL参数
 * @param   int      $distance   左右显示的页数，默认为6
 *
 * @return  string
 */
function page($num,$page_size=20,$page_count,$page,$mpurl='flag=1',$distance=6) {
	if (! isset($pagestr))
		$pagestr = "";

	$pagestr.="<div class=\"page\"><span class=\"totalrecord\">共{$num}条</span><span class=\"totalpages\">第{$page}/{$page_count}页</span>";
	for($i=($page-$distance);$i<=($page+$distance);$i++) {
		if($i<1 || $i>$page_count) {
			continue;
		}
		if($i==$page) {
			if (! isset($str))
				$str = "";

			$str.="<span class=\"page_current\">".$i."</span> ";//使当前页相应的页码数变为灰色
		} else {
			if (! isset($str))
				$str = "";

			$str.="<a href=\"$_SERVER[PHP_SELF]?$mpurl&page=".$i."\">".$i."</a> ";
		}
	}
	if(($i-1-2*$distance)>1) {
		if(($i-1-2*$distance)==2) {
			$str="<a href=\"$_SERVER[PHP_SELF]?$mpurl&page=1\">1</a> ".$str;//第1、2页之间没有...
		} else {
			$str="<a href=\"$_SERVER[PHP_SELF]?$mpurl&page=1\">1</a>...".$str;//加上前省略号...
		}
	}
	if(($i-1)<$page_count) {
		if($i==$page_count) {
			if (! isset($str))
				$str = "";

			$str.=" <a href=\"$_SERVER[PHP_SELF]?$mpurl&page=".$page_count."\">".$page_count."</a>";//倒数第1、2页之间没有...
		} else {
			if (! isset($str))
				$str = "";

			$str.="...<a href=\"$_SERVER[PHP_SELF]?$mpurl&page=".$page_count."\">".$page_count."</a>";//加上后省略号...
		}
	}

	if (isset($str))
		$pagestr.= $str;
	$pagestr.="</div>";
	return $pagestr;
}

function page_ex($page_size=20,$page,$mpurl='flag=1',$distance=10) {
	$prev_page=$page-1;						//定义上一页为该页减1
	$next_page=$page+1;						//定义下一页为该页加1
	$pagestr.="<p class=\"page\">";
	if ($page<=1)							//如果当前页小于等于1只显示灰色文字
	{
		$pagestr.="<font color=gray>首页</font> | ";
	}
	else									//如果当前页大于1显示指向首页的连接
	{
		$pagestr.="<a href='$_SERVER[PHP_SELF]?$mpurl&page=1'>首页</a> | ";
	}

	if ($prev_page<1)						//如果上一页小于1只显示灰色文字
	{
		$pagestr.="<font color=gray>上一页</font> | ";
	}
	else									//如果大于1显示指向上一页的连接
	{
		$pagestr.="<a href='$_SERVER[PHP_SELF]?$mpurl&page=$prev_page'>上一页</a> | ";
	}

	$pagestr.="<a href='$_SERVER[PHP_SELF]?$mpurl&page=$next_page'>下一页</a></p>";

	return $pagestr;
}

//加入历史记录
function history_add($op,$request) {
	// 获取tongji数据库的连接
    connect_tongji_db();

	global $conn;
	$admin_id = $_SESSION[SESSION_USERID];
	$admin_username = mysql_real_escape_string($_SESSION[SESSION_USERNAME]);
	$result = mysql_real_escape_string($_SESSION["result"]);
	$request = mysql_real_escape_string($request);

	$time = date("Y-m-d H:i:s");
	$time_limit = 0;
	if ($time_limit) {
		$query = "insert into history(admin_id,admin_username,op,request,response,time,time_limit) values 	('$admin_id','$admin_username','$op','$request','$result','$time','$time_limit')";
	} else{
		$query = "insert into history(admin_id,admin_username,op,request,response,time) values 	('$admin_id','$admin_username','$op','$request','$result','$time')";
	}
	$result = mysql_query($query,$conn);
	query_check($result,"加入历史记录");
}
//上传文件
function upfile($file) {
	if ($_FILES[$file]['error'] > 0  && $_FILES[$file]['error'] != 4) {
		switch($_FILES[$file]['error']) {
			case 1:
				$errormsg = "文件太大，超过配置环境规定";
			break;
			case 2:
				$errormsg = "文件太大，超过表单规定";
			break;
			case 3:
				$errormsg = "文件只上传一部分";
			break;
			case 5:
				$errormsg = "服务器临时文件夹丢失";
			break;
			case 6:
				$errormsg = "文件写入到临时文件夹出错";
			break;
			case 7:
				$errormsg = "写文件失败";
			break;
			case 8:
				$errormsg = "上传被其它扩展中断";
			break;
			default:
				$errormsg = "无效错误代码";
			break;
		}
		echo "<script type=\"text/javascript\">alert('上传文件失败！原因：".$errormsg."');history.go(-1);</script>";
		exit;
	}
	//如果有上传文件
	if ($_FILES[$file]['name']) {
		$allow_extension = array("txt");//允许的文件扩展名
		$file_extension = strtolower(end(explode(".",$_FILES[$file]['name'])));//文件扩展名
		if (!in_array($file_extension,$allow_extension)) {
			$errormsg = "允许上传的文件扩展名：txt";
			echo "<script type=\"text/javascript\">alert('上传文件失败！原因：".$errormsg."');history.go(-1);</script>";
			exit;
		}
		$allow_type = array("text/plain");//允许的文件mime
		$file_type = $_FILES[$file]['type'];//文件mime
		if (!in_array($file_type,$allow_type)) {
			$errormsg = "允许上传的文件类型：txt";
			echo "<script type=\"text/javascript\">alert('上传文件失败！原因：".$errormsg."');history.go(-1);</script>";
			exit;
		}
		/*if (preg_match("/system|exec|<\s*script/i",file_get_contents($_FILES[$file]['tmp_name']))) {
			$errormsg = "含有敏感词";
			echo "<script type=\"text/javascript\">alert('上传文件失败！原因：".$errormsg."');history.go(-1);</script>";
			exit;
		}*/
	}
	//如果没有上传文件
	if ($_FILES[$file]['error'] == 4) {
		$file_content = "";
	} else {
		$file_content = file_get_contents($_FILES[$file]['tmp_name']);
		$file_content = trim($file_content);
		$file_content = br_array($file_content);
	}
	return $file_content;
}

//上传文件
function upfile_csv($file) {
	if ($_FILES[$file]['error'] > 0) {
		switch($_FILES[$file]['error']) {
			case 1:
				$errormsg = "文件太大，超过配置环境规定";
			break;
			case 2:
				$errormsg = "文件太大，超过表单规定";
			break;
			case 3:
				$errormsg = "文件只上传一部分";
			break;
			case 4:
				$errormsg = "没有上传文件";
			break;
			case 5:
				$errormsg = "服务器临时文件夹丢失";
			break;
			case 6:
				$errormsg = "文件写入到临时文件夹出错";
			break;
			case 7:
				$errormsg = "写文件失败";
			break;
			case 8:
				$errormsg = "上传被其它扩展中断";
			break;
			default:
				$errormsg = "无效错误代码";
			break;
		}
		echo "<script type=\"text/javascript\">alert('上传文件失败！原因：".$errormsg."');history.go(-1);</script>";
		exit;
	}
	$allow_extension = array("csv");//允许的文件扩展名
	$file_extension = strtolower(end(explode(".",$_FILES[$file]['name'])));//文件扩展名
	if (!in_array($file_extension,$allow_extension)) {
		$errormsg = "允许上传的文件扩展名：csv";
		echo "<script type=\"text/javascript\">alert('上传文件失败！原因：".$errormsg."');history.go(-1);</script>";
		exit;
	}
	if (is_uploaded_file($_FILES[$file]['tmp_name'])) {
		$file_dir = ROOT."tongji/include/data/".$_FILES['file']['name'];
		if (!move_uploaded_file($_FILES[$file]['tmp_name'],$file_dir)) {
			echo '移动文件失败!';
			exit;
		}
	}
}

//获得IP
function get_online_ip() {
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
    }
    if(!preg_match("/^[\d\.]{7,15}$/", $onlineip)){
            $onlineip = 'unknown';
    }
    return $onlineip;
}

//根据用户rid得到用户账号
function get_account_by_rid($rid) {
	$db_type = substr($rid,0,1);
	$dbname = "hddb_".$db_type;
	$conninfo_hddb_x = "conninfo_hddb_".$db_type;
	global ${$conninfo_hddb_x};
	mysql_connect(${$conninfo_hddb_x}['host'],${$conninfo_hddb_x}['username'],${$conninfo_hddb_x}['password']);
	mysql_select_db($dbname);
	$query = "select account from user where rid='$rid'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row['account'];
}

//根据用户账号得到用户rid
function get_rid_by_account($account) {
	global $db_all;
	foreach ($db_all as $db_key=>$db_value) {
		global ${$db_key};
		mysql_connect(${$db_key}['host'],${$db_key}['username'],${$db_key}['password']);
		mysql_select_db($db_value);
		$sql = "select rid from user where account='$account'";
		$res = mysql_query($sql);
		$user = mysql_result($res,0);
		if ($user) {
			break;
		}
	}
	return $user;
}

// 生成随机ID
function gen_rand_id(){
   	$char_list = array(
   		"0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
   		"A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
   		"K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
   		"U", "V", "W", "X", "Y", "Z");

   	$size = sizeof($char_list);
   	$ret_str = "";
   	for ($i = 0; $i < 12; $i++)
   	{
   		$char = $char_list[rand(0, $size - 1)];
   		$ret_str = $ret_str.$char;
   	}

    return $ret_str;
}

// 根据RID获取账号信息
function get_char_info_by_rid($rid)
{
	$arr = array(
		'cmd'=>'get_char_info',
		'ccid'=>CC_ID,
		'rid'=>$rid,
		'account'=>'',
		'fields' => 'rid|create_time|last_login_time|nickname|account|language|gem|money|level|exp|gs_id|aaa_id|passed_dungeon|pet_nums|sky_city_open|group_rid|total_charge_amount',
		);

	// 发送给游戏服务器
	$ret = sendMessage(SERVER_IP, SERVER_PORT, $arr, true);
	$ret = json_decode($ret, true);
	return $ret;
}

// 根据账号获取账号信息
function get_char_info_by_acc($account)
{
	$arr = array(
		'cmd'=>'get_char_info',
		'ccid'=>CC_ID,
		'rid'=>'',
		'account'=>$account,
		'fields' => '*',
		);

	// 发送给游戏服务器
	$ret = sendMessage(SERVER_IP, SERVER_PORT, $arr, true);
	$ret = json_decode($ret, true);
	return $ret;
}

// 获取联盟信息
function get_group_info_by_rid($rid)
{
	global $conninfo_bsdb;
	if ($rid == "")
		return array();

	connect_db($conninfo_bsdb, "bsdb");
	$sql = "select gname, short, level, ability, members_num from group_data where rid='$rid'";
	$res = mysql_query($sql);
	$ret = mysql_fetch_array($res);
	return $ret;
}

// 获取昵称
function get_nickname($name)
{
	if (strstr($name, ":b64:")) {
	    $name = substr($name, 5);
	    return base64_decode($name);
	} else {
	    return $name;
	}
}

?>
