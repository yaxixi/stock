<?php

 $english = array(
  "News"      => "新闻",
  "Events"      => "事件",
  "Member Registration"=>"会员注册",
  "Online Service"=>'在线帮助',
  "English Diagnostics"=>'语言诊断',
  "English Training"=>"英语培训",
  "Study in Canada"=>"留学加拿大",
  "Working in Canada"=>"就业加拿大",
  "Immigration to Canada"=>"移民加拿大",
  "more"=>"更多",
  "Welcometo"=>'欢迎访问加拿大海外英语培训学院网站',
  "Search:"=>"搜索：",
  "Home"=>"首页",
  "address1"=>"<strong>总部地址</strong>： 加拿大曼尼托巴省R3V 1T7 温尼伯格市彭比纳高速路401-3285， <strong>电话</strong>：1-204-275-8763，<strong>传真</strong>：1-204-275-0275",
  "address2"=>"<strong>亚洲分部</strong>： 中国福建省厦门市厦禾路844号 中厦国际大厦8-B， <strong>电话</strong>：86-592-5176138/5176523，<strong>传真</strong>：86-592-5176133",
  "address_email"=>"<strong>邮箱</strong>：info.coeti@gmail.com",
  "title"=>"加拿大海外英语培训学院",
  "You are here:"=> "您所在的位置:"
 );

 add_translation("zh-cn",$english);


 function add_translation($country_code, $language_array)
 {

	  $country_code = strtolower($country_code);
	  $country_code = trim($country_code);
	  if (is_array($language_array) && sizeof($language_array) > 0 && $country_code != "")
	  {
		   if (!isset($GLOBALS[$country_code])) {
		    $GLOBALS[$country_code] = $language_array;
		   } else {
		    $GLOBALS[$country_code] = array_merge($GLOBALS[$country_code],$language_array);
		   }
		   return true;

	  }
	  return false;
 }
?>