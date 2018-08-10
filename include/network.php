<?php

/**
 *
 * @param type $method 请求方式
 * @param type $url 地址
 * @param type $fields 附带参数，可以是数组，也可以是字符串
 * @param type $userAgent 浏览器UA
 * @param type $httpHeaders header头部，数组形式
 * @param type $username 用户名
 * @param type $password 密码
 * @return boolean
 */
function curl_execute($method, $url, $fields = '', $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
    $ch = curl_init();
    if (false === $ch) {
        return false;
    }

    if (is_string($url) && strlen($url)) {
        $ret = curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        return false;
    }
    //是否显示头部信息
    curl_setopt($ch, CURLOPT_HEADER, false);
    //
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($username != '') {
        curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
    }

    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }

    $method = strtolower($method);
    if ('post' == $method) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($fields)) {
            $sets = array();
            foreach ($fields AS $key => $val) {
                $sets[] = $key . '=' . urlencode($val);
            }
            $fields = implode('&', $sets);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    } else if ('put' == $method) {
        curl_setopt($ch, CURLOPT_PUT, true);
    }
    //curl_setopt($ch, CURLOPT_PROGRESS, true);
    //curl_setopt($ch, CURLOPT_VERBOSE, true);
    //curl_setopt($ch, CURLOPT_MUTE, false);
    //curl_setopt($ch, CURLOPT_TIMEOUT, 10); //设置curl超时秒数
    if (strlen($userAgent)) {
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    }
    if (is_array($httpHeaders)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
    }
    $ret = curl_exec($ch);
    if (curl_errno($ch)) {
        $err = array(curl_error($ch), curl_errno($ch));
        curl_close($ch);
        return $err;
    } else {
        curl_close($ch);
        if (!is_string($ret) || !strlen($ret)) {
            return false;
        }
        return $ret;
    }
}

/**
 * 发送POST请求
 * @param type $url 地址
 * @param type $fields 附带参数，可以是数组，也可以是字符串
 * @param type $userAgent 浏览器UA
 * @param type $httpHeaders header头部，数组形式
 * @param type $username 用户名
 * @param type $password 密码
 * @return boolean
 */
function curl_post($url, $fields, $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
    $ret = curl_execute('POST', $url, $fields, $userAgent, $httpHeaders, $username, $password);
    if (false === $ret) {
        return false;
    }
    if (is_array($ret)) {
        return false;
    }
    return $ret;
}

/**
 * GET
 * @param type $url 地址
 * @param type $userAgent 浏览器UA
 * @param type $httpHeaders header头部，数组形式
 * @param type $username 用户名
 * @param type $password 密码
 * @return boolean
 */
function curl_get($url, $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
    $ret = curl_execute('GET', $url, "", $userAgent, $httpHeaders, $username, $password);
    if (false === $ret) {
        return false;
    }
    if (is_array($ret)) {
        return false;
    }
    return $ret;
}

function sendHttpMessage($host, $port, $param) {
    $url = "http://{$host}:{$port}/index.php";
    $msg = "MYBODY:".$param;
    $ret = curl_execute("post", $url, $msg);
    return $ret;
}

function fetchStockInfo($code_list) {

    $code_list = array('600000','630001','300059','4322233');
    $list = array();
    foreach($code_list as $code) {
        if (substr($code, 0, 1) == "6") {
            $code = "sh" . $code;
        }
        else {
            $code = "sz" . $code;
        }
        $list[] = $code;
    }
    $url = "http://hq.sinajs.cn/list=" . implode(",",$list);
    $ret = curl_get($url);
    echo "url:" . $url. " ret:" . $ret;
    $stockInfo = array();
    foreach($code_list as $code) {
        $arr = array();
        if (preg_match('/'.$code.'=\"(.*?)\"/s', $ret, $arr) == 1) {
            $code_info = explode(",", $arr[1]);
            $stockInfo[$code] = array(
                "name"=>$code_info[0],
                "curr_price"=>$code_info[3],
            );
        }
    }
    var_dump($stockInfo);
    return $stockInfo;
}

/**
 * 发送数据到目标服务器
 * ip - 服务器的IP地址
 * port - 服务器的端口号
 * rule - 执行的GM指令
 * data - 详细的数据，为k-v，例如：
 *   ( "name" => "weism", "year" => 1981 )
 * wait - 如果设置为true，则发送后会等待目标返回值
 */
function sendMessage($ip, $port, $data, $wait)
{
    // 打成json字符串
    $jsonRaw = json_encode($data);


    $msg['data'] = $jsonRaw;
    $msg['sign'] = strtoupper(md5($jsonRaw."qcplay_yyht"));
    $msg = json_encode($msg);
    $msg = $msg."\0";

    // 设置超时时间
    set_time_limit(15);

    // 连接服务器
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    $connection = socket_connect($socket, $ip, $port);
    if (!$connection)
    {
        alert('连接服务器失败');
        return false;
    }

    // 发送数据
    socket_send($socket, $msg, strlen($msg), 0);

    // 不需要等待返回值，直接返回
    if (! $wait) return;

    // 等待返回值
    $recv_data = "";
    $buffer = "";
    $i = 0;
    do
    {
        $buffer = socket_read($socket, 1024*8, PHP_NORMAL_READ);
        $buffer = trim($buffer);
        if (!$buffer || $buffer == "")
            break;
        if ($buffer == "END")
            break;

        // 认为读取结束了
        $recv_data .= $buffer;
    }
    while ($buffer);

    // 关闭连接
    socket_close($socket);

    // 返回返回值
    return $recv_data;
}



