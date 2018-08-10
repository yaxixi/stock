<?php

 $english = array(
  "News"      => "News",
  "Events"      => "Events",
  "Member Registration"=>"Member Registration",
  "Online Service"=>'Online Service',
  "English Diagnostics"=>'English Diagnostics',
  "English Training"=>"English Training",
  "Study in Canada"=>"Study in Canada",
  "Working in Canada"=>"Working in Canada",
  "Immigration to Canada"=>"Immigration to Canada",
  "more"=>"more",
  "Welcometo"=>'Welcome to Canadian Overseas English Training Institute Inc.',
  "Search:"=>"Search:",
  "Home"=>"Home",
  "address1"=>"<strong>Head Office</strong>： 401-3285 PEMBINA HWY, WINNIPEG, MB, CANADA R3V-1T7 ， <strong>Tel</strong>：1-204-275-8763，<strong>Fax</strong>：1-204-275-0275",
  "address2"=>"<strong>Asia Office</strong> ：8-B ZHONGXIA INT'L BLDG, NO.844 XIAHE RD, XIAMEN, CHINA 361004， <strong>Tel</strong>：86-592-5176138/5176523，<strong>Fax</strong>：86-592-5176133",
  "address_email"=>"<strong>Email</strong>：info.coeti@gmail.com",
  "title"=>"Canadian Overseas English Training Institute Inc.",
  "You are here:"=> "You are here:"
 );

 add_translation("en",$english);


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