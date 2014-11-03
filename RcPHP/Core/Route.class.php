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
namespace RCPHP;

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
		if(\RCPHP\Util\Check::isClient())
		{
			// 客户端路由单独处理
			self::client();
		}
		else
		{
			switch(URL_MODEL)
			{
				case 1:
					self::rest();
					break;
				case 2:
					self::compat();
					break;
				default :
					self::rest();
					break;
			}
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

		if(!empty($tmp[1]))
		{
			$queryStr = $tmp[1];
		}

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
		\RCPHP\RcPHP::$_controller = empty($reqArr['0']) ? DEFAULT_CONTROLLER : $reqArr['0'];
		\RCPHP\RcPHP::$_action = empty($reqArr['1']) ? DEFAULT_ACTION : $reqArr['1'];

		// uri parameters
		for($i = 2; $len = count($reqArr), $i < $len; $i++)
		{
			$f = $i % 2;
			if($f == 0)
			{
				$_GET[$reqArr[$i]] = \RCPHP\RcPHP::$_params[$reqArr[$i]] = empty($reqArr[$i + 1]) ? null : $reqArr[$i + 1];
			}
		}

		// 处理问好后面的参数
		if(!empty($queryStr))
		{
			$queryArr = explode("=", $queryStr);
			for($i = 0; $len = count($queryArr), $i < $len; $i += 2)
			{
				$_GET[$queryArr[$i]] = \RCPHP\RcPHP::$_params[$queryArr[$i]] = empty($queryArr[$i + 1]) ? '' : $queryArr[$i + 1];
			}
		}
	}

	/**
	 * Route for compat.
	 *
	 * @return void
	 */
	public static function compat()
	{
	}

	/**
	 * Route for client.
	 *
	 * @return void
	 */
	public static function client()
	{
		$argv = $_SERVER['argv'];
		unset($argv[0]);

		if(empty($argv))
		{
			\RCPHP\RcPHP::$_controller = DEFAULT_CONTROLLER;
			\RCPHP\RcPHP::$_action = DEFAULT_ACTION;
		}
		else
		{
			\RCPHP\RcPHP::$_controller = (!empty($argv[1]) && substr($argv[1], 0, 1) !== '-') ? $argv[1] : DEFAULT_CONTROLLER;
			\RCPHP\RcPHP::$_action = (!empty($argv[2]) && substr($argv[2], 0, 1) !== '-') ? $argv[2] : DEFAULT_CONTROLLER;

			foreach($argv as $k => $v)
			{
				if(substr($v, 0, 1) === '-')
				{
					$arg = explode("=", substr($v, 1));
					$_GET[$arg[0]] = \RCPHP\RcPHP::$_params[$arg[0]] = empty($arg[1]) ? '' : $arg[1];
				}
			}
		}
	}
}