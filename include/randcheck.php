<?php
if(!isset($_SESSION))
    session_start();
header("Content-type:image/png");
$width=50;//图片宽度
$height=20;//图片高度
$num=4;//随机数的数量
$dotnum=50;//干扰象素数量

$im=imagecreate($width,$height);
$bgcolor=imagecolorallocate($im,0,0,255);//蓝色
$textcolor=imagecolorallocate($im,255,255,255);//白色

//从数组中取出随机数
$array=array(a,b,c,d,e,f,g,h,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z,2,3,4,5,6,7,8,9);
$key=array_rand($array,$num);
$str="";
for($i=0;$i<$num;$i++)
{
	$str.=$array[$key[$i]];
}

$_SESSION['randstr']=$str;
$font=5;
$x=6;
$y=2;
imagestring($im,$font,$x,$y,$_SESSION['randstr'],$textcolor);

//加入干扰象素
for($i=0;$i<$dotnum;$i++)
{
	$randcolor=imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
	imagesetpixel($im,rand(0,$width),rand(0,$height),$randcolor);
}

imagepng($im);
imagedestroy($im);
?>