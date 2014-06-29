<?php
/**
 * Common function file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        function
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

/**
 * 浏览器友好的变量输出
 *
 * @param mixed   $var
 * @param boolean $echo
 * @param string  $label
 * @param boolean $strict
 * @return void|string
 */
function dump($var, $echo = true, $label = null, $strict = true)
{
	$label = ($label === null) ? '' : rtrim($label) . ' ';
	if(!$strict)
	{
		if(ini_get('html_errors'))
		{
			$output = print_r($var, true);
			$output = '<pre>' . $label . dhtmlspecialchars($output) . '</pre>';
		}
		else
		{
			$output = $label . print_r($var, true);
		}
	}
	else
	{
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
		if(!extension_loaded('xdebug'))
		{
			$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
			$output = '<pre>' . $label . dhtmlspecialchars($output) . '</pre>';
		}
	}
	if($echo)
	{
		echo $output;
	}
	else
	{
		return $output;
	}
}

/**
 * Load controller class
 *
 * @param string $class
 * @return object
 */
function C($class)
{
	if(empty($class))
	{
		RcController::halt('The controller name is empty');
	}
	
	$controller = APP_PATH . "controllers/" . $class . "Controller.class.php";

	if(file_exists($controller))
	{
		RcPHP::loadFile($controller);

		$class = $class . 'Controller';

		return RcStructure::singleton($class);
	}
	else
	{
		RcController::halt('The controller file does not exist');
	}
}

/**
 * Load model file
 *
 * @param string $class
 * @return object
 */
function M($class = '')
{	
	$model = APP_PATH . "models/" . (empty($class) ? RcPHP::getController() : $class) . "Model.class.php";

	if(file_exists($model))
	{
		RcPHP::loadFile($model);

		$class = $class . 'Model';

		return RcStructure::singleton($class);
	}
	else
	{
		RcController::halt("The " . $model . " file does not exist");
	}
}

/**
 * A shortcut to the RcRequest::get method
 *
 * @param string $key
 * @return mixed
 */
function G($key)
{
	if(empty($key))
	{
		return false;
	}

	return RcRequest::get($key, true);
}

/**
 * A shortcut to the RcRequest::post method
 *
 * @param string $key
 * @return bool
 */
function P($key)
{
	if(empty($key))
	{
		return false;
	}

	return RcRequest::post($key, true);
}

/**
 * 加载类库
 *
 * @param string $class
 * @param string $lib
 * @return object
 */
function load_class($class, $lib = '')
{
	if(empty($class))
	{
		return false;
	}

	if($lib != 'classes')
	{
		$fileName = RCPHP_PATH . $lib . DS . $class . '.php';
	}
	else
	{
		$fileName = PRO_PATH . 'classes' . DS . $class . '.php';
	}

	if(file_exists($fileName))
	{
		RcPHP::loadFile($fileName);

		return RcStructure::singleton($class);
	}
	else
	{
		RcController::halt('The ' . $fileName . ' file does not exist');
	}
}

/**
 * 获取客户端IP
 *
 * @return string
 */
function getIp()
{
	if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	{
		$ip = getenv("HTTP_CLIENT_IP");
	}
	else
	{
		if(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		{
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		}
		else
		{
			if(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
			{
				$ip = getenv("REMOTE_ADDR");
			}
			else
			{
				if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
				{
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				else
				{
					$ip = "unknown";
				}
			}
		}
	}

	return $ip;
}

/**
 * Size convert.
 *
 * @param number $bytes
 * @return string
 */
function tosize($bytes)
{
	if($bytes >= pow(2, 40))
	{
		$return = round($bytes / pow(1024, 4), 2);
		$suffix = "TB";
	}
	elseif($bytes >= pow(2, 30))
	{
		$return = round($bytes / pow(1024, 3), 2);
		$suffix = "GB";
	}
	elseif($bytes >= pow(2, 20))
	{
		$return = round($bytes / pow(1024, 2), 2);
		$suffix = "MB";
	}
	elseif($bytes >= pow(2, 10))
	{
		$return = round($bytes / pow(1024, 1), 2);
		$suffix = "KB";
	}
	else
	{
		$return = $bytes;
		$suffix = "Byte";
	}

	return $return . "" . $suffix;
}

/**
 * 二维数组排序
 *
 * @param array  $multi_array
 * @param string $sort_key
 * @param string $sort
 * @return array
 */
function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
{
	if(is_array($multi_array))
	{
		foreach($multi_array as $row_array)
		{
			if(is_array($row_array))
			{
				$key_array[] = $row_array[$sort_key];
			}
			else
			{
				return -1;
			}
		}
	}
	else
	{
		return -1;
	}

	array_multisort($key_array, $sort, $multi_array);

	return $multi_array;
}

/**
 * Check the remote file is exists.
 *
 * @param string $url
 * @return bool
 */
function remote_file_exists($url)
{
	$url = trim($url);
	if(empty($url))
	{
		return false;
	}

	if(RcCheck::isUrl($url) === false)
	{
		return false;
	}

	$url_arr = parse_url($url);

	if(!is_array($url_arr) || empty($url_arr))
	{
		return false;
	}

	// 获取请求数据
	$host = $url_arr['host'];
	$path = $url_arr['path'] . "?" . $url_arr['query'];
	$port = isset($url_arr['port']) ? $url_arr['port'] : "80";

	// 连接服务器
	$fp = fsockopen($host, $port, $err_no, $err_str, 30);
	if(!$fp)
	{
		return false;
	}

	// 构造请求协议
	$request_str = "GET " . $path . "HTTP/1.1\r\n";
	$request_str .= "Host:" . $host . "\r\n";
	$request_str .= "Connection:Close\r\n\r\n";

	// 发送请求
	fwrite($fp, $request_str);
	$first_header = fgets($fp, 1024);
	fclose($fp);

	// 判断文件是否存在
	if(trim($first_header) == "")
	{
		return false;
	}
	if(!preg_match("/200/", $first_header))
	{
		return false;
	}

	return true;
}

/**
 * 字符串截取，支持中文和其他编码
 *
 * @param string $str
 * @param number $start
 * @param number $length
 * @param string $charset
 * @param boolen $suffix
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = '...')
{
	if(function_exists("mb_substr"))
	{
		$result = mb_substr($str, $start, $length, $charset);
	}
	else if(function_exists('iconv_substr'))
	{
		$result = iconv_substr($str, $start, $length, $charset);
		if(false === $result)
		{
			$result = '';
		}
	}
	else
	{
		$regExp['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$regExp['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$regExp['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$regExp['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($regExp[$charset], $str, $match);
		$result = join("", array_slice($match[0], $start, $length));
	}

	return $suffix ? $result . $suffix : $result;
}

/**
 * 自动转换字符集 支持数组转换
 *
 * @param string|array $fContents
 * @param string       $from
 * @param string       $to
 * @return string|array
 */
function auto_charset($fContents, $from = 'gbk', $to = 'utf-8')
{
	$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
	$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
	if(strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)))
	{
		//如果编码相同或者非字符串标量则不转换
		return $fContents;
	}

	if(is_string($fContents))
	{
		if(function_exists('mb_convert_encoding'))
		{
			return mb_convert_encoding($fContents, $to, $from);
		}
		else if(function_exists('iconv'))
		{
			return iconv($from, $to, $fContents);
		}
		else
		{
			return $fContents;
		}
	}
	else if(is_array($fContents))
	{
		foreach($fContents as $key => $val)
		{
			$_key = auto_charset($key, $from, $to);
			$fContents[$_key] = auto_charset($val, $from, $to);
			if($key != $_key)
			{
				unset($fContents[$key]);
			}
		}

		return $fContents;
	}
	else
	{
		return $fContents;
	}
}

/**
 * Object into an array
 *
 * @param array $data
 * @return object
 */
function objectToArray($data)
{
	if(is_object($data))
	{
		// Gets the properties of the given object
		// with get_object_vars function
		$data = get_object_vars($data);
	}

	if(is_array($data))
	{
		/*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
		return array_map(__FUNCTION__, $data);
	}
	else
	{
		// Return array
		return $data;
	}
}

/**
 * Array is converted into objects
 *
 * @param object $data
 * @return array
 */
function arrayToObject($data)
{
	if(is_array($data))
	{
		/*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
		return (object)array_map(__FUNCTION__, $data);
	}
	else
	{
		// Return object return $data;
		return $data;
	}
}

/**
 * To prevent XSS attacks
 *
 * @param string $val
 * @return string
 */
function remove_xss($val)
{
	// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	// this prevents some character re-spacing such as <java\0script>
	// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

	// straight replacements, the user should never need these since they're normal characters
	// this prevents like <IMG SRC=@avascript:alert('XSS')>
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for($i = 0; $i < strlen($search); $i++)
	{
		// ;? matches the ;, which is optional
		// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
		// @ @ search for the hex values
		$val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
		// @ @ 0{0,7} matches '0' zero to seven times
		$val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
	}

	// now the only remaining whitespace attacks are \t, \n, and \r
	$ra1 = array(
		'javascript',
		'vbscript',
		'expression',
		'applet',
		'meta',
		'xml',
		'blink',
		'link',
		'style',
		'script',
		'embed',
		'object',
		'iframe',
		'frame',
		'frameset',
		'ilayer',
		'layer',
		'bgsound',
		'title',
		'base'
	);
	$ra2 = array(
		'onabort',
		'onactivate',
		'onafterprint',
		'onafterupdate',
		'onbeforeactivate',
		'onbeforecopy',
		'onbeforecut',
		'onbeforedeactivate',
		'onbeforeeditfocus',
		'onbeforepaste',
		'onbeforeprint',
		'onbeforeunload',
		'onbeforeupdate',
		'onblur',
		'onbounce',
		'oncellchange',
		'onchange',
		'onclick',
		'oncontextmenu',
		'oncontrolselect',
		'oncopy',
		'oncut',
		'ondataavailable',
		'ondatasetchanged',
		'ondatasetcomplete',
		'ondblclick',
		'ondeactivate',
		'ondrag',
		'ondragend',
		'ondragenter',
		'ondragleave',
		'ondragover',
		'ondragstart',
		'ondrop',
		'onerror',
		'onerrorupdate',
		'onfilterchange',
		'onfinish',
		'onfocus',
		'onfocusin',
		'onfocusout',
		'onhelp',
		'onkeydown',
		'onkeypress',
		'onkeyup',
		'onlayoutcomplete',
		'onload',
		'onlosecapture',
		'onmousedown',
		'onmouseenter',
		'onmouseleave',
		'onmousemove',
		'onmouseout',
		'onmouseover',
		'onmouseup',
		'onmousewheel',
		'onmove',
		'onmoveend',
		'onmovestart',
		'onpaste',
		'onpropertychange',
		'onreadystatechange',
		'onreset',
		'onresize',
		'onresizeend',
		'onresizestart',
		'onrowenter',
		'onrowexit',
		'onrowsdelete',
		'onrowsinserted',
		'onscroll',
		'onselect',
		'onselectionchange',
		'onselectstart',
		'onstart',
		'onstop',
		'onsubmit',
		'onunload'
	);
	$ra = array_merge($ra1, $ra2);

	$found = true; // keep replacing as long as the previous round replaced something
	while($found == true)
	{
		$val_before = $val;
		for($i = 0; $i < sizeof($ra); $i++)
		{
			$pattern = '/';
			for($j = 0; $j < strlen($ra[$i]); $j++)
			{
				if($j > 0)
				{
					$pattern .= '(';
					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
					$pattern .= '|';
					$pattern .= '|(&#0{0,8}([9|10|13]);)';
					$pattern .= ')*';
				}
				$pattern .= $ra[$i][$j];
			}
			$pattern .= '/i';
			$replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
			$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
			if($val_before == $val)
			{
				// no replacements were made, so exit the loop
				$found = false;
			}
		}
	}

	return $val;
}

/**
 * 输出安全的html
 *
 * @param string $text
 * @param string $tags
 * @return string
 */
function html_replace($text, $tags = null)
{
	$text = trim($text);
	//完全过滤注释
	$text = preg_replace('/<!--?.*-->/', '', $text);
	//完全过滤动态代码
	$text = preg_replace('/<\?|\?' . '>/', '', $text);
	//完全过滤js
	$text = preg_replace('/<script?.*\/script>/', '', $text);

	$text = str_replace('[', '&#091;', $text);
	$text = str_replace(']', '&#093;', $text);
	$text = str_replace('|', '&#124;', $text);
	//过滤换行符
	$text = preg_replace('/\r?\n/', '', $text);
	//br
	$text = preg_replace('/<br(\s\/)?' . '>/i', '[br]', $text);
	$text = preg_replace('/<p(\s\/)?' . '>/i', '[br]', $text);
	$text = preg_replace('/(\[br\]\s*){10,}/i', '[br]', $text);
	//过滤危险的属性，如：过滤on事件lang js
	while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i', $text, $mat))
	{
		$text = str_replace($mat[0], $mat[1], $text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat))
	{
		$text = str_replace($mat[0], $mat[1] . $mat[3], $text);
	}
	if(empty($tags))
	{
		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
	}
	//允许的HTML标签
	$text = preg_replace('/<(' . $tags . ')( [^><\[\]]*)>/i', '[\1\2]', $text);
	$text = preg_replace('/<\/(' . $tags . ')>/Ui', '[/\1]', $text);
	//过滤多余html
	$text = preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i', '', $text);
	//过滤合法的html标签
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i', $text, $mat))
	{
		$text = str_replace($mat[0], str_replace('>', ']', str_replace('<', '[', $mat[0])), $text);
	}
	//转换引号
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i', $text, $mat))
	{
		$text = str_replace($mat[0], $mat[1] . '|' . $mat[3] . '|' . $mat[4], $text);
	}
	//过滤错误的单个引号
	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i', $text, $mat))
	{
		$text = str_replace($mat[0], str_replace($mat[1], '', $mat[0]), $text);
	}
	//转换其它所有不合法的 < >
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('"', '&quot;', $text);
	//反转换
	$text = str_replace('[', '<', $text);
	$text = str_replace(']', '>', $text);
	$text = str_replace('|', '"', $text);
	//过滤多余空格
	$text = str_replace('  ', ' ', $text);

	return $text;
}

/**
 * UBB代码转换
 *
 * @param string $Text
 * @return string
 */
function ubb($text)
{
	$text = trim($text);
	$text = preg_replace("/\\t/is", "  ", $text);
	$text = preg_replace("/\[h1\](.+?)\[\/h1\]/is", "<h1>\\1</h1>", $text);
	$text = preg_replace("/\[h2\](.+?)\[\/h2\]/is", "<h2>\\1</h2>", $text);
	$text = preg_replace("/\[h3\](.+?)\[\/h3\]/is", "<h3>\\1</h3>", $text);
	$text = preg_replace("/\[h4\](.+?)\[\/h4\]/is", "<h4>\\1</h4>", $text);
	$text = preg_replace("/\[h5\](.+?)\[\/h5\]/is", "<h5>\\1</h5>", $text);
	$text = preg_replace("/\[h6\](.+?)\[\/h6\]/is", "<h6>\\1</h6>", $text);
	$text = preg_replace("/\[separator\]/is", "", $text);
	$text = preg_replace("/\[center\](.+?)\[\/center\]/is", "<center>\\1</center>", $text);
	$text = preg_replace("/\[url=http:\/\/([^\[]*)\](.+?)\[\/url\]/is", "<a href=\"http://\\1\" target=_blank>\\2</a>", $text);
	$text = preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is", "<a href=\"http://\\1\" target=_blank>\\2</a>", $text);
	$text = preg_replace("/\[url\]http:\/\/([^\[]*)\[\/url\]/is", "<a href=\"http://\\1\" target=_blank>\\1</a>", $text);
	$text = preg_replace("/\[url\]([^\[]*)\[\/url\]/is", "<a href=\"\\1\" target=_blank>\\1</a>", $text);
	$text = preg_replace("/\[img\](.+?)\[\/img\]/is", "<img src=\\1>", $text);
	$text = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is", "<font color=\\1>\\2</font>", $text);
	$text = preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is", "<font size=\\1>\\2</font>", $text);
	$text = preg_replace("/\[sup\](.+?)\[\/sup\]/is", "<sup>\\1</sup>", $text);
	$text = preg_replace("/\[sub\](.+?)\[\/sub\]/is", "<sub>\\1</sub>", $text);
	$text = preg_replace("/\[pre\](.+?)\[\/pre\]/is", "<pre>\\1</pre>", $text);
	$text = preg_replace("/\[email\](.+?)\[\/email\]/is", "<a href='mailto:\\1'>\\1</a>", $text);
	$text = preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis", "color_txt('\\1')", $text);
	$text = preg_replace("/\[emot\](.+?)\[\/emot\]/eis", "emot('\\1')", $text);
	$text = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>\\1</i>", $text);
	$text = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>\\1</u>", $text);
	$text = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $text);
	$text = preg_replace("/\[quote\](.+?)\[\/quote\]/is", " <div class='quote'><h5>引用:</h5><blockquote>\\1</blockquote></div>", $text);
	$text = preg_replace("/\[code\](.+?)\[\/code\]/eis", "highlight_code('\\1')", $text);
	$text = preg_replace("/\[php\](.+?)\[\/php\]/eis", "highlight_code('\\1')", $text);
	$text = preg_replace("/\[sig\](.+?)\[\/sig\]/is", "<div class='sign'>\\1</div>", $text);
	$text = preg_replace("/\\n/is", "<br/>", $text);

	return $text;
}

/**
 * 发送HTTP状态
 *
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code)
{
	static $_status = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',
		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Moved Temporarily ',
		// 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		// 306 is deprecated but reserved
		307 => 'Temporary Redirect',
		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded'
	);

	if(isset($_status[$code]))
	{
		header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
		// 确保FastCGI模式下正常
		header('Status:' . $code . ' ' . $_status[$code]);
	}
}

/**
 * 功能等同htmlspecialchars
 *
 * @param string $string
 * @param string $charset
 * @return string
 */
function dhtmlspecialchars($string, $charset = '')
{
	if(empty($charset))
	{
		$charset = RcCheck::isUtf8($string) === true ? 'UTF-8' : 'GB2312';
	}

	return version_compare(PHP_VERSION, '5.4', '>=') ? htmlspecialchars($string, ENT_QUOTES, $charset) : htmlspecialchars($string, ENT_QUOTES);
}

/**
 * Custom ip2long.
 *
 * @param int $ip
 * @return bool|number
 */
function dip2long($ip)
{
	if(RcCheck::isIpv4($ip) === true)
	{
		return bindec(decbin(ip2long($ip)));
	}

	return false;
}

/**
 * Rand string code.
 *
 * @param int    $len
 * @param string $type
 * @param string $addChars
 * @return string
 */
function rand_string($len = 6, $type = '', $addChars = '')
{
	$str = '';
	switch($type)
	{
		case 0:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1:
			$chars = str_repeat('0123456789', 3);
			break;
		case 2:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3:
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 4:
			$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
			break;
		default:
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if($len > 10)
	{
		//位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
	}
	if($type != 4)
	{
		$chars = str_shuffle($chars);
		$str = substr($chars, 0, $len);
	}
	else
	{
		// 中文随机字
		for($i = 0; $i < $len; $i++)
		{
			$str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
		}
	}

	return $str;
}

/**
 * Rand color code.
 *
 * @return void
 */
function rand_color()
{
	return '#' . sprintf("%02X", mt_rand(0, 255)) . sprintf("%02X", mt_rand(0, 255)) . sprintf("%02X", mt_rand(0, 255));
}
