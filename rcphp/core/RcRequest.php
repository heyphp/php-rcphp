<?php
/**
 * RcRequest class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcRequest extends RcBase
{

	/**
	 * ��ȡGET������Ϣ
	 *
	 * @param string $index
	 * @param string $xss
	 * @return boolean Ambigous string, mixed>|multitype:Ambigous <string, mixed>
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

		// GET����Ϊ����ʱ
		$getArray = array();
		foreach($_GET[$index] as $key => $val)
		{
			$getArray[$key] = self::_fetch_from_array($_GET[$index], $key, $xss);
		}

		return $getArray;
	}

	/**
	 * ��ȡPOST������Ϣ
	 *
	 * @param string $index
	 * @param string $xss
	 * @return boolean Ambigous string, mixed>|multitype:Ambigous <string, mixed>
	 */
	public static function post($index = null, $xss = false)
	{

		// ��������
		if(is_null($index) || is_array($index) || !isset($_POST[$index]))
		{
			return false;
		}

		if(!is_array($_POST[$index]))
		{
			return self::_fetch_from_array($_POST, $index, $xss);
		}

		// POST����Ϊ����ʱ
		$postArray = array();
		foreach($_POST[$index] as $key => $val)
		{
			$postArray[$key] = self::_fetch_from_array($_POST[$index], $key, $xss);
		}

		return $postArray;
	}

	/**
	 * ��ȡPOST|GET������Ϣ
	 *
	 * @param string $index
	 * @param string $xss
	 * @return Ambigous <boolean, multitype:string , unknown, string, mixed>
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
	 * ��ȡCOOKIE������Ϣ
	 *
	 * @param string $index
	 * @param string $xss
	 * @return boolean Ambigous string, mixed>|multitype:Ambigous <string, mixed>
	 */
	public static function cookie($index = null, $xss = false)
	{

		// ��������
		if(is_null($index) || is_array($index) || !isset($_COOKIE[$index]))
		{
			return false;
		}

		if(!is_array($_COOKIE[$index]))
		{
			return self::_fetch_from_array($_COOKIE, $index, $xss);
		}

		// COOKIE����Ϊ����ʱ
		$cookieArray = array();
		foreach($_COOKIE[$index] as $key => $val)
		{
			$cookieArray[$key] = self::_fetch_from_array($_COOKIE[$index], $key, $xss);
		}

		return $cookieArray;
	}

	/**
	 * ��ȡSERVER������Ϣ
	 *
	 * @param string $index
	 * @param string $xss
	 * @return boolean Ambigous string, mixed>|multitype:Ambigous <string, mixed>
	 */
	public static function server($index = null, $xss = false)
	{

		// ��������
		if(is_null($index) || is_array($index) || !isset($_SERVER[$index]))
		{
			return false;
		}

		if(!is_array($_SERVER[$index]))
		{
			return self::_fetch_from_array($_SERVER, $index, $xss);
		}

		// SERVER����Ϊ����ʱ
		$serverArray = array();
		foreach($_SERVER[$index] as $key => $val)
		{
			$serverArray[$key] = self::_fetch_from_array($_SERVER[$index], $key, $xss);
		}

		return $serverArray;
	}

	/**
	 * ��ȡCLIģʽ�²�����Ϣ
	 *
	 * @param string $index
	 * @param string $xss
	 * @return boolean Ambigous string, mixed>|multitype:Ambigous <string, mixed>
	 */
	public static function client($index = null, $xss = false)
	{

		// ��������
		if(is_null($index) || is_array($index) || !isset($_SERVER['argv'][$index]))
		{
			return false;
		}

		if(!is_array($_SERVER['argv'][$index]))
		{
			return $argvInfo = self::_fetch_from_array($_SERVER['argv'], $index, $xss);
		}

		// POST����Ϊ����ʱ
		$argvArray = array();
		foreach($_SERVER['argv'][$index] as $key => $val)
		{
			$argvArray[$key] = self::_fetch_from_array($_SERVER['argv'][$index], $key, $xss);
		}

		return $argvArray;
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
		elseif(($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) // Does the index contain array notation
		{
			$value = $array;
			for($i = 0; $i < $count; $i++)
			{
				$key = trim($matches[0][$i], '[]');
				if($key === '') // Empty notation will return the value as array
				{
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
