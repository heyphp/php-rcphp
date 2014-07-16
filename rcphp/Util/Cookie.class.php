<?php
/**
 * Cookie class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Util
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Cookie
{

	/**
	 * cookie expire time
	 *
	 * @var string
	 */
	private static $expire = 3600;

	/**
	 * cookie path
	 *
	 * @var string
	 */
	private static $path = "/";

	/**
	 * cookie domain
	 *
	 * @var string
	 */
	private static $domain = "";

	/**
	 * cookie prefix
	 *
	 * @var string
	 */
	private static $prefix = "";

	/**
	 * Setting cookies prefix
	 *
	 * @param $prefix
	 * @return void
	 */
	public static function prefix($prefix)
	{
		self::$prefix = $prefix;
	}

	/**
	 * Set cookie
	 *
	 * @param string $name
	 * @param string $val
	 * @param string $expire
	 * @param string $path
	 * @param string $domain
	 * @return void
	 */
	public static function set($name, $val, $expire = '', $path = '', $domain = '')
	{
		$expire = (empty($expire)) ? time() + (int)self::$expire : $expire; // cookie时间
		$path = (empty($path)) ? self::$path : $path; // cookie路径
		$domain = (empty($domain)) ? self::$domain : $domain; // 主机名称
		if(empty($domain))
		{
			setcookie(self::$prefix . $name, $val, $expire, $path);
		}
		else
		{
			setcookie(self::$prefix . $name, $val, $expire, $path, $domain);
		}

		$_COOKIE[self::$prefix . $name] = $val;
	}

	/**
	 * Get cookie
	 *
	 * @param string $name
	 * @return string
	 */
	public static function get($name)
	{
		return $_COOKIE[self::$prefix . $name];
	}

	/**
	 * Delete cookie
	 *
	 * @param string $name
	 * @param string $path
	 * @param string $domain
	 * @return void
	 */
	public static function delete($name, $path = '/', $domain = '')
	{
		if(is_array($name))
		{
			foreach($name as $key => $val)
			{
				self::set($val, '', time() - 3600, empty($path) ? self::$path : $path, $domain);
				$_COOKIE[self::$prefix . $val] = '';
				unset($_COOKIE[self::$prefix . $val]);
			}
		}
		else
		{
			self::set($name, '', time() - 3600, empty($path) ? self::$path : $path, $domain);
			$_COOKIE[self::$prefix . $name] = '';
			unset($_COOKIE[self::$prefix . $name]);
		}
	}

	/**
	 * Clear cookie
	 *
	 * @return void
	 */
	public static function clear()
	{
		unset($_COOKIE);
	}

	/**
	 * Cookie is exists
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function is_set($name)
	{
		return isset($_COOKIE[self::$prefix . $name]);
	}
}