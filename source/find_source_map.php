<?php
error_reporting(0);
include_once '../include/config.php';
include_once '../include/network.php';

check_login();

$rid = trim($_REQUEST['rid']);
$src_file = trim($_REQUEST['src_file']);
if ($src_file)
{
    $ret = curl_get("http://42.62.118.213/copyPublishSourceMap.php?src_file=$src_file");
    $_SESSION["result"] = $ret;
    history_add('find_source_map', $src_file);
    die($ret);
}

if ($rid)
{
    $ret = curl_get("http://42.62.118.213/listPublishSourceMap.php?rid=$rid");
    if ($ret)
    {
        $ret = json_decode($ret, 1);
        if ($ret['files'])
        {
            foreach($ret['files'] as $key=>$row)
            {
                $time_list[$key] = $row['modifyTime'];
            }

            array_multisort($time_list, SORT_DESC, $ret['files']);
        }
    }
}

$Smarty->assign(array(
	'data'=>$ret,
    'files'=>$ret['files'],
	)
);
