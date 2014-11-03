<?php
/**
 * Request class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
namespace RCPHP;

defined('IN_RCPHP') or exit('Access denied');

class Request
{

	/**
	 * 获取GET参数信息
	 *
	 * @param string $index
	 * @param string $xss
	 * @return bool|string|array
	 */
	public static function get($index = null, $xss = false)
	{
		if(is_null($index) || is_array($index) || !isset($_GET[$index]))
		{
			return false;
		}

		if(!is_array($_GET[$index]))
		{
			return self::_fetch_from_array($_GET, $index, $xss);
		}

		// GET数据为数组时
		$getArray = array();
		foreach($_GET[$index] as $key => $val)
		{
			$getArray[$key] = self::_fetch_from_array($_GET[$index], $key, $xss);
		}

		return $getArray;
	}

	/**
	 * 获取POST参数信息
	 *
	 * @param string $index
	 * @param string $xss
	 * @return bool|string|array
	 */
	public static function post($index = null, $xss = false)
	{
		if(is_null($index) || is_array($index) || !isset($_POST[$index]))
		{
			return false;
		}

		if(!is_array($_POST[$index]))
		{
			return self::_fetch_from_array($_POST, $index, $xss);
		}

		// POST数据为数组时
		$postArray = array();
		foreach($_POST[$index] as $key => $val)
		{
			$postArray[$key] = self::_fetch_from_array($_POST[$index], $key, $xss);
		}

		return $postArray;
	}

	/**
	 * 获取POST|GET参数信息
	 *
	 * @param string $index
	 * @param string $xss
	 * @return bool|string|array
	 */
	public static function get_or_post($index = null, $xss = false)
	{
		if(isset($_POST[$index]))
		{
			return self::post($index, $xss);
		}
		else
		{
			return self::get($index, $xss);
		}
	}

	/**
	 * Conversion process data
	 *
	 * @param array  $array
	 * @param string $index
	 * @param string $xss_clean
	 * @return mixed
	 */
	protected static function _fetch_from_array(&$array, $index = '', $xss_clean = false)
	{
		if(isset($array[$index]))
		{
			$value = $array[$index];
		}
		elseif(($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1)
		{
			// Does the index contain array notation
			$value = $array;
			for($i = 0; $i < $count; $i++)
			{
				$key = trim($matches[0][$i], '[]');
				if($key === '')
				{
					// Empty notation will return the value as array
					break;
				}

				if(isset($value[$key]))
				{
					$value = $value[$key];
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}

		$value = ($xss_clean === true) ? remove_xss($value) : $value;

		return dhtmlspecialchars($value);
	}
}
