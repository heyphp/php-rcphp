<?php
/**
 * Common function file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Function
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

/**
 * �ж�php�汾
 *
 * @param string $version
 * @return bool
 */
function is_php($version)
{
	static $_is_php;

	if(!isset($_is_php[$version]))
	{
		$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
	}

	return $_is_php[$version];
}

/**
 * ������Ѻõı������
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

	return true;
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
		\RCPHP\Controller::halt('The controller name is empty');
	}

	$controller = APP_PATH . "controllers/" . $class . "Controller.class.php";

	if(file_exists($controller))
	{
		\RCPHP\RcPHP::loadFile($controller);

		$class = $class . 'Controller';

		return \RCPHP\RcPHP::instance($class);
	}
	else
	{
		\RCPHP\Controller::halt('The controller file does not exist');
	}

	return false;
}

/**
 * Load model file
 *
 * @param string $class
 * @return object
 */
function M($class = '')
{
	$class = empty($class) ? \RCPHP\RcPHP::getController() : $class;
	$model = MODEL_PATH . $class . "Model.class.php";

	if(file_exists($model))
	{
		\RCPHP\RcPHP::loadFile($model);

		$class = $class . 'Model';

		return \RCPHP\RcPHP::instance($class);
	}
	else
	{
		\RCPHP\Controller::halt("The " . $model . " file does not exist");
	}

	return false;
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

	return \RCPHP\Request::get($key, true);
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

	return \RCPHP\Request::post($key, true);
}

/**
 * Load function file.
 *
 * @param string $func
 * @return bool
 */
function &load_function($func)
{
	if(empty($func))
	{
		return false;
	}

	foreach(array(
				RCPHP_PATH . 'Function',
				COMMON_PATH
			) as $path)
	{
		if(file_exists($path . DS . $func . '.php'))
		{
			\RCPHP\RcPHP::loadFile($path . DS . $func . '.php');

			return true;
		}
	}

	return false;
}

/**
 * �������
 *
 * @param string $class
 * @param string $directory
 * @return object|bool
 */
function &load_class($class, $directory = 'Util')
{
	if(empty($class))
	{
		return false;
	}

	$className = false;

	foreach(array(
				RCPHP_PATH,
				EXT_PATH
			) as $path)
	{
		if(file_exists($path . $directory . DS . $class . '.class.php'))
		{
			$className = $class;

			if(class_exists($class, false) === false)
			{
				\RCPHP\RcPHP::loadFile($path . $directory . DS . $class . '.class.php');
			}

			break;
		}
	}

	if($className === false)
	{
		\RCPHP\Net\Http::send_http_status(404);

		\RCPHP\Controller::halt('Unable to locate the specified class: ' . $class . '.php');
	}

	return \RCPHP\RcPHP::instance($className);
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
 * ��ά��������
 *
 * @param array  $multi_array
 * @param string $sort_key
 * @param string $sort
 * @return array
 */
function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
{
	if(is_array($multi_array) && !empty($multi_array))
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
 * �����ȫ��html
 *
 * @param string $text
 * @param string $tags
 * @return string
 */
function html_replace($text, $tags = null)
{
	$text = trim($text);
	//��ȫ����ע��
	$text = preg_replace('/<!--?.*-->/', '', $text);
	//��ȫ���˶�̬����
	$text = preg_replace('/<\?|\?' . '>/', '', $text);
	//��ȫ����js
	$text = preg_replace('/<script?.*\/script>/', '', $text);

	$text = str_replace('[', '&#091;', $text);
	$text = str_replace(']', '&#093;', $text);
	$text = str_replace('|', '&#124;', $text);
	//���˻��з�
	$text = preg_replace('/\r?\n/', '', $text);
	//br
	$text = preg_replace('/<br(\s\/)?' . '>/i', '[br]', $text);
	$text = preg_replace('/<p(\s\/)?' . '>/i', '[br]', $text);
	$text = preg_replace('/(\[br\]\s*){10,}/i', '[br]', $text);
	//����Σ�յ����ԣ��磺����on�¼�lang js
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
	//�����HTML��ǩ
	$text = preg_replace('/<(' . $tags . ')( [^><\[\]]*)>/i', '[\1\2]', $text);
	$text = preg_replace('/<\/(' . $tags . ')>/Ui', '[/\1]', $text);
	//���˶���html
	$text = preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i', '', $text);
	//���˺Ϸ���html��ǩ
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i', $text, $mat))
	{
		$text = str_replace($mat[0], str_replace('>', ']', str_replace('<', '[', $mat[0])), $text);
	}
	//ת������
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i', $text, $mat))
	{
		$text = str_replace($mat[0], $mat[1] . '|' . $mat[3] . '|' . $mat[4], $text);
	}
	//���˴���ĵ�������
	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i', $text, $mat))
	{
		$text = str_replace($mat[0], str_replace($mat[1], '', $mat[0]), $text);
	}
	//ת���������в��Ϸ��� < >
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('"', '&quot;', $text);
	//��ת��
	$text = str_replace('[', '<', $text);
	$text = str_replace(']', '>', $text);
	$text = str_replace('|', '"', $text);
	//���˶���ո�
	$text = str_replace('  ', ' ', $text);

	return $text;
}

/**
 * ���ܵ�ͬhtmlspecialchars
 *
 * @param string $string
 * @param string $charset
 * @return string
 */
function dhtmlspecialchars($string, $charset = '')
{
	if(empty($charset))
	{
		$charset = \RCPHP\Util\Check::isUtf8($string) === true ? 'UTF-8' : 'GB2312';
	}

	return is_php(5.4) ? htmlspecialchars($string, ENT_QUOTES, $charset) : htmlspecialchars($string, ENT_QUOTES);
}

/**
 * Custom ip2long.
 *
 * @param int $ip
 * @return bool|number
 */
function dip2long($ip)
{
	if(\RCPHP\Util\Check::isIpv4($ip) === true)
	{
		return bindec(decbin(ip2long($ip)));
	}

	return false;
}
