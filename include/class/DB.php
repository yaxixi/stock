<?php
class DB{
	private static $_db=array();
	private static $_con="";
	private static function echo_error($error,$str){
			echo $error."\n";
			echo "SQL:\t".$str."\n";
			echo "Time:\t".date("Y-m-d H:i:s")."\n";
	}
	public static function AddDB($db_arr){
		foreach ($db_arr as $key => $val){
			self::$_db[$key]=$val;
		}
	}
	public static function Connect($host,$database='default_db',$Names='default_charset'){		
		$database=($database=='default_db')?self::$_db[$host]['dtbs']:$database;
		$Names=($Names=='default_charset')?self::$_db[$host]['charts']:$Names;
		$con=@mysql_connect(self::$_db[$host]['host'],self::$_db[$host]['user'],self::$_db[$host]['pass']);
		while(mysql_error() && ++$c<10){
			echo mysql_error();
			sleep(60);
			$con=@mysql_connect(self::$_db[$host]['host'],self::$_db[$host]['user'],self::$_db[$host]['pass']);
		}
		if(mysql_error()){
			echo "Connect to $host failed at ".date("Y-m-d H:i:s")."\n";
			echo mysql_error();
		}
		mysql_select_db($database,$con);
		mysql_query('set Names '.$Names,$con);
		self::$_con=$con;
		return $con;		
	}
	public static function Query_one($str,$con='null'){//单结果查询
		$con=($con == 'null')?$con=self::$_con:$con;
		$res=mysql_query($str,$con);
		$error=mysql_error();
		if($error){
			self::echo_error($error,$str);
			$re===FALSE;
		}else{
			$re=mysql_result($res,0);	
		}
		return $re;
	}
	public static function Query_array($str,$key='null',$con='null'){//返回结果数组
		$con=($con == 'null')?$con=self::$_con:$con;
		$res=mysql_query($str,$con);
		$error=mysql_error();
		if($error){
			self::echo_error($error,$str);
			$re=FALSE;
		}else{
			while ($row=mysql_fetch_assoc($res)) {
				if($key=='null'){
					$re[]=$row;
				}else{
					$re[$row[$key]]=$row;
					unset($re[$row[$key]][$key]);
				}
			}
		}
		return $re;
	}
	public static function Query_arr($str,$key,$val,$con='null'){//给定键，值，返回一维数组
		$con=($con == 'null')?$con=self::$_con:$con;
		$res=mysql_query($str,$con);
		$error=mysql_error();
		$re=FALSE;
		if($error){
			self::echo_error($error,$str);			
		}else{
			while ($row=mysql_fetch_assoc($res)) {
				$re[$row[$key]]=$row[$val];
			}
		}
		return $re;
	}
	public static function Query($str,$con='null'){//返回资源符
		$con=($con == 'null')?$con=self::$_con:$con;
		$res=mysql_query($str,$con);
		$error=mysql_error();
		if($error){
			self::echo_error($error,$str);
			$res = FALSE;
		}
		return $res;
	}
}
?>

