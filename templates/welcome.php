<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理中心_<?php echo TITLE;?></title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body id="main">
<div class="title">
	<h3 class="welH3"><span><?php echo TITLE;?></span></h3>
</div>
<div class="tip">
    <ul>
    	<li>欢迎进入管理中心！</li>
        <li>用户名：<?php echo $_SESSION[SESSION_USERNAME];?></li>
		<li>姓名：<?php echo $row['realname'];?></li>
        <li>当前时间：<?php echo date("Y-m-d H:i:s");?></li>

        <?php clear_result(); ?>
    </ul>
</div>
</body>
</html>
