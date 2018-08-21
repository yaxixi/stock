<?php
check_priv("priv_gm_info");
include_once '../include/config.php';
include_once '../include/network.php';

check_login();

$success = (float)$_REQUEST['success'];
$gain = (float)$_REQUEST['gain'];
$lose = (float)$_REQUEST['lose'];


$f = ($success * $gain - (1 - $success) * $lose) / ($gain * $lose);
$g = $success * log(1 + $f * $gain) + (1 - $success) * log(1 - $f * $lose);
$r = round((exp($g) - 1) * 100, 2);

$Smarty->assign(array(
    'f'=>$f * 100,
    'r'=>$r,
    'success'=>$success,
    'gain'=>$gain,
    'lose'=>$lose,
	)
);
