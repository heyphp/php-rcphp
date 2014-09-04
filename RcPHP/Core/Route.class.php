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
	 * Route dispatch
	 *
	 * @return void
	 */
	public static function dispatch()
	{
		switch(URL_MODEL)
		{
			case 1:
				self::queryString();
				break;
			case 2:
				self::rest();
				break;
			case 3:
				self::compat();
				break;
			default :
				self::queryString();
				break;
		}
	}

	/**
	 * Route for rest.
	 *
	 * @return array
	 */
	public static function rest()
	{
		$reqUri = $_SERVER['REQUEST_URI'];
		$reqStr = str_replace(BASE_URI, '', $reqUri);

		$tmp = explode("?", $reqStr);

		$reqStr = $tmp[0];
		$queryStr = $tmp[1];

		unset($tmp);

		if(substr($reqStr, 0, 1) == '/')
		{
			$reqStr = substr($reqStr, 1, strlen($reqStr));
		}

		if(substr($reqStr, strlen($reqStr) - 1) == '/')
		{
			$reqStr = substr($reqStr, 0, -1);
		}

		// convert uri into array
		$reqArr = explode('/', $reqStr);
		unset($reqStr);
		unset($reqStr);

		// first two segments is controller/action
		RcPHP::$_controller = empty($reqArr['0']) ? DEFAULT_CONTROLLER : $reqArr['0'];
		RcPHP::$_action = empty($reqArr['1']) ? DEFAULT_ACTION : $reqArr['1'];

		// uri parameters
		for($i = 2; $len = count($reqArr), $i < $len; $i++)
		{
			$f = $i % 2;
			if($f == 0) $_GET[$reqArr[$i]] = RcPHP::$_params[$reqArr[$i]] = empty($reqArr[$i + 1]) ? null : $reqArr[$i + 1];
		}

		// 处理问好后面的参数
		if(!empty($queryStr))
		{
			$queryArr = explode("=", $queryStr);
			for($i = 0; $len = count($queryArr), $i < $len; $i += 2)
			{
				$_GET[$queryArr[$i]] = empty($queryArr[$i + 1]) ? '' : $queryArr[$i + 1];
			}
		}
	}

	/**
	 * Route for querystring.
	 *
	 * @return void
	 */
	private static function queryString()
	{
		$params = array();

		$queryString = $_SERVER['QUERY_STRING'];

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
					RcPHP::$_controller = $_GET['c'] = DEFAULT_CONTROLLER;
					unset($params['c']);
				}

				if(empty($params['a']))
				{
					RcPHP::$_action = $_GET['a'] = DEFAULT_ACTION;
					unset($params['a']);
				}

				RcPHP::$_params = $params;
			}
			else
			{
				RcPHP::$_controller = $_GET['c'] = DEFAULT_CONTROLLER;
				RcPHP::$_action = $_GET['a'] = DEFAULT_ACTION;
			}

			unset($tmp);
			unset($params);
		}
		else
		{
			RcPHP::$_controller = $_GET['c'] = DEFAULT_CONTROLLER;
			RcPHP::$_action = $_GET['a'] = DEFAULT_ACTION;
		}
	}

	/**
	 * Route fot compat.
	 *
	 * @return void
	 */
	public static function compat()
	{
	}
}