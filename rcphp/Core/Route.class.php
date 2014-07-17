<?php
/**
 * Route class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Route
{

	/**
	 * Route config
	 *
	 * @var array
	 */
	private static $config = array();

	/**
	 * Analyze route rule.
	 *
	 * @return array
	 */
	public static function parseUrl()
	{
		return self::queryString();
	}

	/**
	 * Normal mode.
	 *
	 * @return array
	 */
	public static function queryString()
	{
		if(!empty($_SERVER['PATH_INFO']))
		{
			return self::pathinfo();
		}
		else
		{
			$params = array();

			$queryString = $_SERVER['PATH_INFO'];

			if($queryString != false)
			{
				$queryArray = explode("&", $queryString);

				$tmp = array();

				$params = array();

				if(count($queryArray) > 0)
				{
					foreach($queryArray as $item)
					{
						$tmp = explode('=', $item);
						$params[$tmp[0]] = $tmp[1];
					}

					if(empty($params['c']))
					{
						$params['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
					}

					if(empty($params['a']))
					{
						$params['a'] = $_GET['a'] = DEFAULT_ACTION;
					}
				}
				else
				{
					$params['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
					$params['a'] = $_GET['a'] = DEFAULT_ACTION;
				}

				unset($tmp);
			}
			else
			{
				$params['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
				$params['a'] = $_GET['a'] = DEFAULT_ACTION;
			}

			return $params;
		}
	}

	/**
	 * pathinfo mode.
	 *
	 * @return array
	 */
	public static function pathinfo()
	{
		self::$config = RcPHP::getConfig("route");

		if(empty(self::$config))
		{
			Controller::halt('The routing configuration errors');
		}

		$script_name = $_SERVER["SCRIPT_NAME"]; //获取当前文件的路径
		$url = $_SERVER["REQUEST_URI"]; //获取完整的路径，包含"?"之后的字符串

		if($url && strpos($url, $script_name, 0) !== false)
		{
			$url = substr($url, strlen($script_name));
		}
		else
		{
			$script_name = str_replace(basename($_SERVER["SCRIPT_NAME"]), '', $_SERVER["SCRIPT_NAME"]);
			if($url && strpos($url, $script_name, 0) !== false)
			{
				$url = substr($url, strlen($script_name));
			}
		}

		//第一个字符是'/'，则去掉
		if($url[0] == '/')
		{
			$url = substr($url, 1);
		}

		//去除问号后面的查询字符串
		if($url && false !== ($pos = @strrpos($url, '?')))
		{
			$url = substr($url, 0, $pos);
		}

		//去除后缀
		if($url && ($pos = strrpos($url, self::$config['URL_HTML_SUFFIX'])) > 0)
		{
			$url = substr($url, 0, $pos);
		}

		$params = array();

		//获取模块名称
		if($url && ($pos = @strpos($url, self::$config['URL_CONTROLLER_DEPR'], 1)) > 0)
		{
			$params['c'] = $_GET['c'] = substr($url, 0, $pos);

			$url = substr($url, $pos + 1);
		}
		else
		{
			$params['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
		}

		//获取操作方法名称
		if($url && ($pos = @strpos($url, self::$config['URL_ACTION_DEPR'], 1)) > 0)
		{
			$params['a'] = $_GET['a'] = substr($url, 0, $pos); //模块
			$url = substr($url, $pos + 1);
		}
		else
		{
			$params['a'] = $_GET['a'] = DEFAULT_ACTION;
		}

		$param = explode(self::$config['URL_PARAM_DEPR'], $url);

		$total = count($param);

		for($i = 0; $i < $total; $i = $i + 2)
		{
			if(!empty($param[$i]))
			{
				$params[$param[$i]] = $_GET[$param[$i]] = empty($param[$i + 1]) ? '' : $param[$i + 1];
			}
		}

		unset($param);
		unset($total);

		return $params;
	}
}