<?php
/**
 * RcRoute class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

abstract class RcRoute
{

	/**
	 * 路由配置
	 *
	 * @var array
	 */
	public static $config;

	/**
	 * 路由类型
	 *
	 * @var array
	 */
	public static $routeType = array(
		1 => '普通模式',
		2 => 'PATHINFO模式'
	);

	/**
	 * 路由解析核心方法
	 *
	 * @param array $config
	 * @return array
	 */
	public static function parseUrl($config = 1)
	{
		if(empty($config))
		{
			RcController::halt('The routing configuration errors');
		}

		self::$config = $config;

		return self::analyze();
	}

	/**
	 * 路由方式选择
	 *
	 * @return array
	 */
	public static function analyze()
	{
		$urlType = empty(self::$config['url_type']) ? 1 : self::$config['url_type'];

		if(!empty(self::$routeType[$urlType]))
		{
			return self::makeUrl();
		}

		return false;
	}

	/**
	 * 调用路由主方法
	 *
	 * @return void
	 */
	public static function makeUrl()
	{
		$urlType = empty(self::$config['url_type']) ? 1 : intval(self::$config['url_type']);
		switch($urlType)
		{
			case 1:
				return self::querystring();
				break;
			case 2:
				self::pathinfo();
				break;
			default:
				return self::querystring();
				break;
		}
	}

	/**
	 * 普通GET访问路由
	 *
	 * @return array
	 */
	public static function querystring()
	{
		if(RcRequest::server('PATH_INFO') !== false)
		{
			return self::pathinfo();
		}
		else
		{
			$urlParams = array();
			$queryString = RcRequest::server('QUERY_STRING', true);
			if($queryString != false)
			{
				$queryArray = explode("&amp;", $queryString);
				$tmp = $array = array();
				if(count($queryArray) > 0)
				{
					foreach($queryArray as $item)
					{
						$tmp = explode('=', $item);
						$array[$tmp[0]] = $tmp[1];
					}

					$urlParams = $array;

					if(!isset($array['c']))
					{
						$urlParams['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
					}
					if(!isset($array['a']))
					{
						$urlParams['a'] = $_GET['a'] = DEFAULT_ACTION;
					}
				}
				else
				{
					$urlParams['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
					$urlParams['a'] = $_GET['a'] = DEFAULT_ACTION;
				}
			}
			else
			{
				$urlParams['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
				$urlParams['a'] = $_GET['a'] = DEFAULT_ACTION;
			}

			return $urlParams;
		}
	}

	/**
	 * PATHINFO模式访问路由
	 *
	 * @return array
	 */
	public static function pathinfo()
	{
		$urlParams = array();
		if(!strpos($_SERVER['PATH_INFO'], '.'))
		{
			$str = $_SERVER['PATH_INFO'];
		}
		else
		{
			$str = substr($_SERVER['PATH_INFO'], 0, strpos($_SERVER['PATH_INFO'], '.'));
		}

		// 获取路径信息(pathinfo)
		$pathinfo = explode('/', trim($str, '/'));

		$num = count($pathinfo);
		if($num > 0)
		{
			for($i = 0; $i < $num; $i += 2)
			{
				$urlParams[addslashes($pathinfo[$i])] = $_GET[addslashes($pathinfo[$i])] = addslashes(empty($pathinfo[$i + 1]) ? '' : $pathinfo[$i + 1]);
			}

			if(empty($urlParams['c']))
			{
				$urlParams['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
			}
			if(empty($urlParams['a']))
			{
				$urlParams['a'] = $_GET['a'] = DEFAULT_ACTION;
			}
		}
		else
		{
			$urlParams['c'] = $_GET['c'] = DEFAULT_CONTROLLER;
			$urlParams['a'] = $_GET['a'] = DEFAULT_ACTION;
		}

		return $urlParams;
	}
}