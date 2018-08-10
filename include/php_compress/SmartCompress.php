<?php
    error_reporting(E_ALL^E_NOTICE^E_WARNING);

	require_once dirname (__FILE__)."/class/BigEndianBuffer.php";

	class SmartCompress extends BigEndianBuffer
	{
		/*定义一个用于通信的buffer*/
		private $buffer;//用于读通信的buffer
		private $recv_bytes;//用于解析的buffer

		/*定义一个用于通信的socket*/
		private $socket;

		/*定义一个用于打包解包参数的buff*/
		private $bytes; //用于写通信的buffer
		private $readerIndex = 0;
		private $writeIndex = 0;

		/*文件路径变量*/
		private $comm_path;
		private $map_path;
		private $send_path;

		/*文件读取后保存在内存中*/
		private $arr_comm = array();
		private $arr_map = array();
		private $arr_map_backup = array();
		private $arr_send = array();
		private $arr_send_backup = array();

		public function __construct($host, $port)
		{
			$this->socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );

			if (socket_connect ( $this->socket, $host, $port ) == false)
			{
				echo $host."\n";
				echo $port."\n";
				echo "socket_connect() failed. Reason: ".socket_strerror(socket_last_error());
				return;
			}
			socket_set_option ( $this->socket, SOL_TCP, 1, 1 );

			//socket_set_option ( $this->socket, SOL_TCP, SO_SNDBUF, 1024 * 1024 );
            //数据初始化
            $this->init();
            $this->analyzeComm();
            $this->analyzeMap();
            $this->analyzeSend();
		}

		public function closeSocket()
		{
			$this->send_clear();
			$this->recv_clear();
			socket_close ($this->socket);
		}

		/*获取配置文件路径*/
		public function init()
		{
			$this->comm_path = COMM_PATH;
			$this->map_path = MAP_PATH;
			$this->send_path = SEND_PATH;
		}

		public function send_clear()
		{
			$this->bytes = null;
			$this->writeIndex = 0;
		}

		public function recv_clear()
		{
			$this->recv_bytes = null;
			$this->readerIndex = 0;
		}

		public function select()
		{
			$socket_read = array($this->socket);

			while (true)
			{
				$read = $socket_read;

				if ( (socket_select ($read, $socket_write = NULL, $socket_except = NULL, 0, 10) < 1) )
				{
					echo "no read.\n";
					continue;
				}

				foreach ($read as $socket)
				{
					$ret_bytes = $this->socketReadBytes($socket, 6);
					echo $ret_bytes."\n";
					$this->getBuffer($ret_bytes);
				}
			}
		}

		/*socket读取信息*/
		public function socketReadBytes($socket, $len)
		{
			if (is_null ( $len ) || $len < 1) {
				return false;
			}
			$str = "";
			$bufferLen = strlen ( $this->buffer );
			if ($bufferLen > 0) {
				if ($len > $bufferLen) {
					$str = $this->buffer;
					$this->buffer = null;
					$len = $len - $bufferLen;
				} else {
					$str = substr ( $this->buffer, 0, $len );
					$this->buffer = substr ( $this->buffer, $len );
					return $str;
				}

			}
			if (($rec = socket_recv ( $socket, $strSocket, $len, 0 )) <= 0) {
				return false;
			}
			$str .= $strSocket;
			if (strlen ( $str ) == $len) {
				return $str;
			}
			$len -= strlen ( $str );
			while ( $len > 0 ) {
				$tstr = "";
				if (($rec = socket_recv ( $socket, $tstr, $len, 0 )) <= 0) {
					return false;
				}
				$len -= strlen ( $tstr );
				$str .= $tstr;
			}
			return $str;
		}

		/*socket写入消息*/
		public function socketWriteBytes($bytes)
		{
			socket_write ( $this->socket, $bytes );
		}

		/*comm文件读取分析保存到数组中*/
		public function analyzeComm()
		{
			$comm_handle = fopen($this->comm_path, "r");

			while (!feof($comm_handle))
			{
				$comm_line = fgets($comm_handle);

				$kongge_pox = strpos($comm_line, " ");
				$zuokuohao_pox = strpos($comm_line, "(");
				if ($zuokuohao_pox <= 0)
				{
					continue;
				}
				$youkuohao_pox = strpos($comm_line, ")");

				$str_cmd = substr($comm_line, $kongge_pox+1, $zuokuohao_pox-$kongge_pox-1);
				$str_param = substr($comm_line, $zuokuohao_pox+1, $youkuohao_pox-$zuokuohao_pox-1);

				$arr_param = array();
				$pos = strpos($str_param, ",");
				while ($pos > 0)
				{
					$param = substr($str_param, 0, $pos);
					$n_pos = strrpos($param, " ");
					$type = substr($param, 0, $n_pos);
					$name = substr($param, $n_pos, strlen($param));
					$arr_param[] = array("type"=>trim($type), "name"=>trim($name));
					$str_param = substr($str_param, $pos+1, strlen($str_param));
					$pos = strpos($str_param, ",");
				}
				$n_pos = strrpos($str_param, " ");
				if ($n_pos > 0)
				{
					$type = substr($str_param, 0, $n_pos);
					$name = substr($str_param, $n_pos, strlen($str_param));
					$arr_param[] = array("type"=>trim($type), "name"=>trim($name));
				}

				$this->arr_comm[$str_cmd] = $arr_param;
			}

			fclose($comm_handle);
		}

		/*map文件读取分析保存到数组中*/
		public function analyzeMap()
		{
			$map_handle = fopen($this->map_path, "r");
			while (!feof($map_handle))
			{
				$map_line = fgets($map_handle);

				$denghao_pox = strrpos($map_line, "=");
				if ($denghao_pox <= 0)
				{
					continue;
				}
				$fenhao_pox = strrpos($map_line, ";");

				$str_type_name = substr($map_line, 0, $denghao_pox);
				$str_value = substr($map_line, $denghao_pox+1, $fenhao_pox-$denghao_pox-1);

				$kongge_pox = strpos($str_type_name, " ");
				$str_type = substr($str_type_name, 0, $kongge_pox);
				$str_name = substr($str_type_name, $kongge_pox, strlen($str_type_name)-$kongge_pox-1);

				$this->arr_map[trim($str_name)] = array("type"=>trim($str_type), "value"=>(trim($str_value)+0));
				$this->arr_map_backup[(trim($str_value)+0)] = array("type"=>trim($str_type), "name"=>trim($str_name));
			}
			fclose($map_handle);
		}

		/*send 文件读取分析保存到数组中*/
		public function analyzeSend()
		{
			$send_handle = fopen($this->send_path, "r");
			while (!feof($send_handle))
			{
				$send_line = fgets($send_handle);
				$denghao_pox = strrpos($send_line, "=");
				if ($denghao_pox <= 0)
				{
					continue;
				}
				$fenhao_pox = strrpos($send_line, ";");

				$str_cmd_name = substr($send_line, 0, $denghao_pox);
				$str_value = substr($send_line, $denghao_pox+1, $fenhao_pox-$denghao_pox-1);
				$this->arr_send[trim(strtolower($str_cmd_name))] = (trim($str_value))+0;
				$this->arr_send_backup[(trim($str_value))+0] = trim(strtolower($str_cmd_name));
			}
			fclose($send_handle);
		}

		/*实现继承抽象类的2个抽象方法*/
		public function readBytes($len)
		{
			if ($len < 1)
			{
				return false;
			}
			$str = substr ( $this->recv_bytes, $this->readerIndex, $len );
			$this->readerIndex += $len;
			return $str;
		}

		public function writeBytes($bytes)
		{
			$len = strlen($this->bytes);
			if ($this->writeIndex < $len)
			{
				$this->bytes = substr_replace($this->bytes, $bytes, $this->writeIndex, 0);
				$this->writeIndex = strlen($this->bytes);
			}
			else
			{
				$this->bytes .= $bytes;
				$this->writeIndex += strlen ( $bytes );
			}
		}

        /*push string类型字符串*/
		public function writeString($stringl)
		{
			$len = strlen($stringl);
			$this->writeUnsignedChar($len);
			$this->bytes .= $stringl;
			$this->writeIndex += $len;
		}

		/*get string类型字符串*/
		public function readString()
		{
			$len = $this->readUnsignedChar();
			return $this->readBytes($len);
        }

		/*push string2类型字符串*/
		public function writeString2($stringl)
		{
			$len = strlen($stringl);
			$this->writeShort($len);
			$this->bytes .= $stringl;
			$this->writeIndex += $len;
		}

		/*get string2类型字符串*/
		public function readString2()
		{
			$len = $this->readUnsignedShort();
			return $this->readBytes($len);
		}

        /*push string4类型字符串*/
		public function writeString4($stringl)
		{
			$len = strlen($stringl);
			$this->writeUnsignedInt($len);
			$this->bytes .= $stringl;
			$this->writeIndex += $len;
		}

		/*get string4类型字符串*/
		public function readString4()
		{
			$len = $this->readUnsignedInt();
			return $this->readBytes($len);
        }

		/*push map类型字符串*/
		# $map = array();
		# $map[] = array($name=>$value);
		public function writeMap($map)
		{
			$this->putValue("uint16", count($map));
			foreach($map as $key=>$value)
			{
				$type_name = $this->arr_map[$key]["type"];
				$value_name = $this->arr_map[$key]["value"];

				$this->putValue("uint16", $value_name);
				$this->putValue($type_name, $value);
			}
		}

        public function GetSaveBinaryIntSize($len)
        {
            if ($len == 0)
                return 0;
            $i = 1;
            while ($len < -128 || $len > 127)
            {
                $len = (int)floor((double)$len / 256);
                $i = $i + 1;
            }

            return $i;
        }

        public function SaveType($type, $len)
        {
            $size = $this->GetSaveBinaryIntSize($len);
            if ($size > 15)
            {
                echo "SaveType size error!\n";
                return 0;
            }
            $first = ($type << 4) | $size;
            $this->writeUnsignedChar($first);

            $i = $size;
            $curPos = $this->writeIndex;
            while($i--)
            {
                $value = $len & 0xFF;
                $this->writeIndex = $curPos;
                $this->writeUnsignedChar($value);
                $len = $len >> 8;
            }
            return $size + 1;
        }

        /*push mixed类型字符串*/
        public function writeMixed($value)
        {
            if (is_int($value) || is_bool($value))
            {
                $len = $this->SaveType(2, (int)$value);
            }
            elseif (is_string($value))
            {
                $len = $this->SaveType(4, strlen($value));
                $this->writeBytes($value);
                $len += strlen($value);
            }
            elseif (is_array($value))
            {
                $count = count($value);
                $len = $this->SaveType(7, $count);

                foreach($value as $key=>$val)
                {
                    if (is_string($key))
                    {
                        $len += $this->SaveType(4, strlen($key));
                        $this->writeBytes($key);
                        $len += strlen($key);
                    }
                    elseif (is_int($key))
                    {
                        $len += $this->SaveType(2, $key);
                    }
                    else
                    {
                        echo "writeMixed array key error!";
                        return 0;
                    }

                    $len += $this->writeMixed($val);
                }
            }
            return $len;
        }

		/*get map类型字符串*/
		# $result = array();
		# $result[] = array($name=>$value);
		public function readMap()
		{
			$arr_value = array();
			$key_count = $this->readUnsignedShort();
            while($key_count > 0)
            {
                $name_value = $this->readUnsignedShort();
                $name = $this->arr_map_backup[$name_value]["name"];
				$type = $this->arr_map_backup[$name_value]["type"];
				$value = $this->getValue($type);
				$arr_value[$name] = $value;

                $key_count -= 1;
            }
			return $arr_value;
		}

        /*get mixed类型字符串*/
		public function readMixed()
		{
			$arr_value = array();
            $typeSize = $this->readUnsignedChar();
            $type = $typeSize >> 4;
            $size = $typeSize & 0x0F;
            $data = 0;
            if ($size > 0)
            {
                $byte = $this->readUnsignedChar();
                if (($btye & 0x80) != 0)
                    $data = -1;
                else
                    $data = 0;
                $data = 256 * $data + $byte;
                $size -= 1;
                while($size--)
                {
                    $byte = $this->readUnsignedChar();
                    $data = 256 * $data + $byte;
                }
            }

            if ($type == 2)
                return $data;
            elseif ($type == 4)
                return $this->readBytes($data);
            elseif ($type == 7)
            {
                while($data--)
                {
                    $key = $this->readMixed();
                    $value = $this->readMixed();
                    $arr_value[$key] = $value;
                }
                return $arr_value;
            }
            else
                echo "readMixed type error";

            return 0;
        }

		/*打包数组*/
		public function putArray($type, $arr)
		{
			$count = count($arr);

			$this->writeShort($count);

			foreach($arr as $key=>$value)
			{
				$this->putValue($type, $value);
			}
		}

		/*解包数组*/
		public function getArray($type)
		{
			$result = array();

			$count = $this->readUnsignedShort();

			for($i=1; $i<=$count; $i+=1)
			{
				$result[] = $this->getValue($type);
			}

			return $result;
		}

		/*根据参数类型将参数push到buffer中*/
		public function putValue($type, $value)
		{
			$pos = strpos($type, "[]");
			if ($pos > 0)
			{
				$the_type = substr($type, 0, $pos);
				$this->putArray($the_type, $value);
			}
			elseif ($type == "uint8")
			{
				$this->writeUnsignedChar($value);
			}
			elseif ($type == "int8")
			{
				$this->writeChar($value);
			}
			elseif ($type == "uint16")
			{
				$this->writeUnsignedShort($value);
			}
			elseif ($type == "int16")
			{
				$this->writeShort($value);
			}
			elseif ($type == "uint32")
			{
				$this->writeUnsignedInt($value);
			}
			elseif ($type == "int32")
			{
				$this->writeInt($value);
			}
			elseif ($type == "string")
			{
				$this->writeString($value);
			}
            elseif ($type == "string2")
			{
				$this->writeString2($value);
            }
            elseif ($type == "string4")
			{
				$this->writeString4($value);
			}
			elseif ($type == "map")
			{
				$this->writeMap($value);
			}
            elseif ($type == "mixed")
            {
                $begin_pos = $this->writeIndex;
                $this->writeMixed($value);
                $len = $this->writeIndex - $begin_pos;
                $this->writeIndex = $begin_pos;
                $this->writeUnsignedInt($len);
            }
			else
			{
				echo "putValue type is error, the type is ".$type." !\n";
			}
		}

		/*根据参数类型从buffer中读出正确的值*/
		public function getValue($type)
		{
			$pos = strpos($type, "[]");
			if ($pos > 0)
			{
				$the_type = substr($type, 0, $pos);
				return $this->getArray($the_type);
			}
			elseif ($type == "uint8")
			{
				return $this->readUnsignedChar();
			}
			elseif ($type == "int8")
			{
				return $this->readChar();
			}
			elseif ($type == "uint16")
			{
				return $this->readUnsignedShort();
			}
			elseif ($type == "int16")
			{
				return $this->readShort();
			}
			elseif ($type == "uint32")
			{
				return $this->readUnsignedInt();
			}
			elseif ($type == "int32")
			{
				return $this->readInt();
			}
			elseif ($type == "string")
			{
				return $this->readString();
			}
            elseif ($type == "string2")
			{
				return $this->readString2();
            }
            elseif ($type == "string4")
			{
				return $this->readString4();
			}
			elseif ($type == "map")
			{
				return $this->readMap();
			}
            elseif ($type == "mixed")
			{
                $this->readUnsignedInt();
				return $this->readMixed();
			}
			else
			{
				echo "getValue type ".$type." is error!\n";
			}
		}

		/*构造通信buffer*/
		public function sendMessage($param)
		{
			$this->send_clear();

			$cmd = $param[0];

			/*获取命令编号*/
			$cmd_no = $this->arr_send[$cmd];

			/*获取命令参数存放的数组*/
			$cmd_param = $this->arr_comm[$cmd];

			$count = count($cmd_param);

            $this->putValue("uint8", 77);
            $this->putValue("uint8", 90);
            $this->putValue("uint16", 0);
            $this->putValue("uint32", 0);

            $lenIndex = $this->writeIndex;
            $this->putValue("uint16", 0);

			/*打包命令编号*/
			$this->putValue("uint16", $cmd_no);

			/*打包参数*/
			for ($i=0;$i<$count;$i+=1)
			{
				$this->putValue($cmd_param[$i]["type"], $param[$i+1]);
			}

			/*计算除命令头以外的长度*/
			$param_len = strlen($this->bytes)-12 + 2;

			/*打包该长度*/
			$this->writeIndex = $lenIndex;
			$this->putValue("uint32", $param_len);

            /*
            echo "send buffer : ";
            print_r(unpack("H*", $this->bytes));
            echo "\n";
             */
			$this->socketWriteBytes($this->bytes);

			$this->send_clear();
		}

		/*接收消息*/
		public function recvMessage()
		{
			$this->recv_clear();

			$ret_array = array();

			if (($rec = socket_recv ( $this->socket, $strSocket, 16, 0)) < 16)
			{
				echo "recvMessage the len of message is ".$rec."\n";
				return false;
			}

			$this->recv_bytes = $strSocket;
			$M = $this->getValue("uint8");
            $Z = $this->getValue("uint8");
            $checksum = $this->getValue("uint16");
            $tickcount = $this->getValue("uint32");
            $cmd_len = $this->getValue("uint32");
            $align = $this->getValue("uint16");
			$cmd = $this->getValue("uint16");

			//echo $this->arr_send_backup[$cmd]."\n";

			while ($cmd == 65281)
            {
                if (($rec = socket_recv ( $this->socket, $strSocket, $cmd_len - 2, 0 )) < $cmd_len - 2)
                {
                    echo "recvMessage error cmd_echo\n";
					return false;
				}

                $this->recv_bytes = substr_replace($this->recv_bytes, pack("n", 65282), -2, 2);
                $this->recv_bytes .= $strSocket;
				$this->socketWriteBytes($this->recv_bytes);
				$this->send_clear();
				$this->recv_clear();

				if (($rec = socket_recv ( $this->socket, $strSocket, 16, 0 )) < 16)
				{
					echo "recvMessage the len of message is ".$rec."\n";
					return false;
				}

				$this->recv_bytes = $strSocket;
				$M = $this->getValue("uint8");
                $Z = $this->getValue("uint8");
                $checksum = $this->getValue("uint16");
                $tickcount = $this->getValue("uint32");
                $cmd_len = $this->getValue("uint32");
                $align = $this->getValue("uint16");
                $cmd = $this->getValue("uint16");

				//echo $this->arr_send_backup[$cmd]."\n";
			}

			$ret_array[] = $this->arr_send_backup[$cmd];
			$param_array = $this->arr_comm[$this->arr_send_backup[$cmd]];

			$this->recv_clear();

			if ($cmd_len > 2)
			{
				if (($rec = socket_recv ( $this->socket, $strSocket, $cmd_len - 2, 0 )) < $cmd_len - 2)
				{
                    echo "recvMessage error the len of message is ".$rec."\n";
					return false;
				}

				$this->recv_bytes = $strSocket;

                /*
                echo "recv buffer : ";
                print_r(unpack("H*", $this->recv_bytes));
                echo "\n";
                 */
				foreach($param_array as $key=>$value)
				{
					$ret_array[$value["name"]] = $this->getValue($value["type"]);
				}
			}

			$this->recv_clear();

			return $ret_array;
		}

		/*获取通信buffer*/
		public function getBuffer($bytes)
		{
			$this->recv_bytes = $bytes;
			$this->readerIndex = 0;
			$cmd_flag = $this->getValue("uint16");
			$cmd_len = $this->getValue("uint16");
			$cmd = $this->getValue("uint16");

			echo $cmc_flag."\n";
			echo $cmd_len."\n";
			echo $cmd."\n";

			if ($cmd == 6)
			{
				$this->socketWriteBytes($bytes);
			}
		}

		/*测试*/
		public function test()
		{
			$this->send_clear();
			$this->init();
			$this->analyzeMap();

			$map_test = array();

			$map_test_one = array();
			//$map_test_one[] = array("name"=>"num", "value"=>1);
			//$map_test_one[] = array("name"=>"name", "value"=>"ljm");
			$map_test_one[] = array("num"=>1,"name"=>"ljm");

			$map_test_two = array();
			//$map_test_two[] = array("name"=>"attachment", "value"=>1);
			//$map_test_two[] = array("name"=>"task_log_des", "value"=>"log");
			$map_test_two[] = array("attachment"=>1, "task_log_des"=>"log");

			$map_test[] = $map_test_one;
			$map_test[] = $map_test_two;

			$num1 = 10;
			$this->putValue("uint8", $num1);
			$this->putValue("stringl", "ababababab");
			$this->putValue("uint16", $num1);
			$this->putValue("uint32", $num1);
			$this->putValue("map[]", $map_test);

			echo $this->getValue("uint8");
			echo "\n";
			echo $this->getValue("stringl");
			echo "\n";
			echo $this->getValue("uint16");
			echo "\n";
			echo $this->getValue("uint32");
			echo "\n";

			$test = $this->getValue("map[]");
			foreach($test as $key=>$value)
			{
				foreach($value as $a=>$b)
				{
					echo $b["name"]." ".$b["value"]."\n";
				}
			}
		}

		public function test_map()
		{
			$this->send_clear();
			$this->init();
			$this->analyzeMap();
			foreach($this->arr_map as $key=>$value)
			{
				echo $key." ".$value["type"]." ".$value["value"]."\n";
			}
		}

		public function test_array()
		{
			$this->send_clear();
			$this->init();
			$this->analyzeMap();

			$test = array();
			$test[] = "aaa";
			$test[] = "bbb";
			$test[] = "ccc";
			$this->putValue("stringl[]", $test);

			$result = $this->getValue("stringl[]");
			foreach($result as $key=>$value)
			{
				echo $key." ".$value."\n";
			}
		}

		public function test_arr()
		{
			$count = 10;
			for ($i=1; $i<=10; $i+=1)
			{
				echo $i."";
			}
		}

		public function cmd_internal_auth()
		{
			$cmd = array();
			$cmd[] = "cmd_internal_auth";
			$cmd[] = 3;
			$cmd[] = "";
            $cmd[] = "";
            $cmd[] = 3333;
			$this->sendMessage($cmd);
		}

		public function test_cmds()
		{
			//构造通信命令
            $cmd = array();
            $cmd[] = "cmd_test";
            $cmd[] = "test_cookie";
            $cmd[] = array(
                'account'=>'yaxixi',
                'port'=>1234,
            );
            $cmd[] = array(
                0=> array(
                    'account'=>'yaxixi',
                    'port'=>1234,
                ),
                1=> array(
                    'rid'=>'yaxixi',
                    'port'=>1234,
                ),
            );
            $cmd[] = array(
                0=> array(
                    'account'=>'yaxixi',
                    'port'=>1234,
                ),
                1=> array(
                    'rid'=>'yaxixi',
                    'port'=>1234,
                ),
            );
            $this->sendMessage($cmd);

            $result = $this->recvMessage();
            $this->closeSocket();

            print_r($result);
            $this->result_show($result);
		}

		public function result_show($result)
		{
			if (is_array($result))
			{
				echo "\n{ \n";
				foreach ($result as $key=>$value)
				{
                    echo $key . ' : ';
					$this->result_show($value);
				}
				echo " ";
				echo "\n }  ";
			}
			else
			{
				echo $result." ";
			}
		}
	}

	/*
	$object = new SmartCompress("localhost", 12011);

	//初始化
	$object->init();
	$object->analyzeComm();
	$object->analyzeMap();
	$object->analyzeSend();

	//发送服务器验证命令
	$object->test_cmd();

	$object->test_cmds();

	//
	//$object->select();
	$result = $object->recvMessage();
	$object->result_show($result);
	$object->closeSocket();
	echo "\n the end!\n";
	*/
?>
