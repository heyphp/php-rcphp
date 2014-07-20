<?php
/**
 * Controller class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Controller extends Base
{

	/**
	 * View object.
	 *
	 * @var object
	 */
	protected static $_view;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		if(get_magic_quotes_gpc())
		{
			$_POST = $this->stripSlashes($_POST);
			$_GET = $this->stripSlashes($_GET);
			$_COOKIE = $this->stripSlashes($_COOKIE);
		}
	}

	/**
	 * Show error message.
	 *
	 * @param string $message
	 * @param string $level
	 * @return bool
	 */
	public static function halt($message, $level = 'Error')
	{
		if(empty($message))
		{
			return false;
		}

		if(defined('APP_DEBUG'))
		{
			//output message
			$trace = debug_backtrace();
			krsort($trace);

			$sourceFile = $trace[0]['file'] . ' line:' . $trace[0]['line'];

			$traceString = '';
			$i = 1;
			foreach($trace as $key => $t)
			{
				$args = $t['args'];
				if($args != '')
				{
					$tempFunc = function ($params)
					{
						return is_object($params) ? (array)$params : $params;
					};
					$tempArgs = array_map($tempFunc, $args);

					$args = implode('.', $tempArgs);
				}
				if(substr(php_sapi_name(), 0, 3) === 'cli')
				{
					$traceString .= '#' . $i . ' ' . $t['file'] . '(line:' . $t['line'] . ')' . $t['class'] . $t['type'] . $t['function'] . '(' . $args . ')<br/>';
				}
				else
				{
					$traceString .= "<tr class='bg1'>";
					$traceString .= "<td>" . $i . "</td>";
					$traceString .= "<td>" . str_replace(PRO_PATH, "", $t['file']) . "</td>";
					$traceString .= "<td>" . $t['line'] . "</td>";
					$traceString .= "<td>" . $t['class'] . $t['type'] . $t['function'] . "(" . $args . ")</td>";
					$traceString .= "</tr>";
				}

				$i++;
			}

			Log::write($message, $sourceFile, $level, date('Ymd', time()) . '_trace');

			if(APP_DEBUG)
			{
				if(substr(php_sapi_name(), 0, 3) !== 'cli')
				{
					header('HTTP/1.1 404 Not Found');
					header('Status:404 Not Found');

					include_once RCPHP_PATH . 'sources/html/exception.php';
				}
				else
				{
					echo '[Description]' . PHP_EOL . $message . PHP_EOL;
					echo '[Source File]' . PHP_EOL . $sourceFile . PHP_EOL;
					echo '[Stack Trace]' . PHP_EOL . str_replace('<br/>', "\r\n", $traceString) . PHP_EOL;
				}
				//exit
				exit();
			}
		}
		else
		{
			Http::send_http_status(404);
		}

		return true;
	}

	/**
	 * Show prompt message.
	 *
	 * @param string $message
	 * @param string $gotoUrl
	 * @param int    $limitTime
	 * @return void
	 */
	public static function showMessage($message = '跳转中……', $gotoUrl = '-1', $limitTime = 5)
	{
		if(!is_null($gotoUrl))
		{
			if($gotoUrl == -1)
			{
				$gotoUrl = 'javascript:history.go(-1);';
			}
			else
			{
				$gotoUrl = str_replace(array(
					"\n",
					"\r"
				), '', $gotoUrl);
			}
		}

		include_once RCPHP_PATH . 'sources/html/message.php';

		exit();
	}

	/**
	 * Get domain.
	 *
	 * @example
	 * $url = $this->getServerName();
	 * echo $url;
	 * http://www.2345.com
	 * @return string
	 */
	public static function getServerName()
	{
		//获取网址域名部分.
		$serverName = !empty($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : $_SERVER['SERVER_NAME'];
		$serverPort = ($_SERVER['SERVER_PORT'] == '80') ? '' : ':' . intval($_SERVER['SERVER_PORT']);

		//获取网络协议.
		$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;

		return ($secure ? 'https://' : 'http://') . $serverName . $serverPort;
	}

	/**
	 * Access to the root directory of the current project's URL.
	 *
	 * @return string
	 */
	public static function getBaseUrl()
	{
		$url = str_replace(array(
			'\\',
			'//'
		), '/', dirname($_SERVER['SCRIPT_NAME']));

		return (substr($url, -1) == '/') ? $url : $url . '/';
	}

	/**
	 * URL redirect.
	 *
	 * @param string $url
	 * @return void|bool
	 */
	public function redirect($url = null)
	{
		if(is_null($url))
		{
			return false;
		}

		if(!headers_sent())
		{
			header('Location:' . $url);
		}
		else
		{
			echo '<script type="text/javascript">location.href="' . $url . '";</script>';
		}

		exit();
	}

	/**
	 * The singleton pattern.
	 *
	 * @param string $className
	 * @return object
	 */
	public static function instance($className)
	{
		if(empty($className))
		{
			return false;
		}

		return Structure::singleton($className);
	}

	/**
	 * Stripslashes.
	 *
	 * @param string|array $string
	 * @return string|array
	 */
	public function stripSlashes($string = null)
	{
		if(is_null($string))
		{
			return false;
		}

		if(!is_array($string))
		{
			return stripslashes($string);
		}

		foreach($string as $key => $value)
		{
			if(is_array($value))
			{
				$string[$key] = $this->stripSlashes($value);
			}
			else
			{
				$string[$key] = stripslashes($value);
			}
		}

		return $string;
	}

	/**
	 * Set the template variables.
	 *
	 * @param string|array $key
	 * @param string       $value
	 * @return $this
	 */
	public function assign($key, $value = null)
	{
		//load view class.
		self::$_view = View::getInstance();

		self::$_view->assign($key, $value);

		return $this;
	}

	/**
	 * Display content template
	 *
	 * @param string $fileName
	 * @param array  $data
	 * @return void
	 */
	public function display($fileName = null, $data = null)
	{
		//load view class.
		self::$_view = View::getInstance();

		self::$_view->display($fileName, $data);
	}

	/**
	 * Get the template content
	 *
	 * @param string $fileName
	 * @param array  $data
	 * @return string
	 */
	public function fetch($fileName = null, $data = array())
	{
		//load view class.
		self::$_view = View::getInstance();

		return self::$_view->fetch($fileName, $data);
	}
}