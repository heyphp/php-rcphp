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
namespace RCPHP\Util;

defined('IN_RCPHP') or exit('Access denied');

class Cookie
{

	/**
	 * Cookie path
	 *
	 * @var string
	 */
	public static $path = "/";

	/**
	 * Cookie domain
	 *
	 * @var string
	 */
	public static $domain = null;

	/**
	 * Cookie secure
	 *
	 * @var bool
	 */
	public static $secure = false;

	/**
	 * Http olny
	 *
	 * @var bool
	 */
	public static $httponly = false;

	/**
	 * cookie prefix
	 *
	 * @var string
	 */
	public static $prefix = "";

	/**
	 * Set cookie
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $expire
	 * @return void
	 */
	public static function set($key, $value, $expire = 0)
	{
		if($expire != 0)
		{
			$expire = time() + $expire;
		}

		setcookie(self::$prefix . $key, $value, $expire, Cookie::$path, Cookie::$domain, Cookie::$secure, Cookie::$httponly);

		$_COOKIE[self::$prefix . $key] = $value;
	}

	/**
	 * Get cookie
	 *
	 * @param string $key
	 * @return string
	 */
	public static function get($key, $default = null)
	{
		if(self::is_set($key))
		{
			return $_COOKIE[self::$prefix . $key];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Delete cookie
	 *
	 * @param string $key
	 * @return void
	 */
	public static function delete($keys)
	{
		if(is_array($keys))
		{
			foreach($keys as $key)
			{
				unset($_COOKIE[self::$prefix . $key]);
				self::set($key, null);
			}
		}
		else
		{
			unset($_COOKIE[self::$prefix . $keys]);
			self::set($keys, null);
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
	 * @param string $key
	 * @return bool
	 */
	public static function is_set($key)
	{
		return isset($_COOKIE[self::$prefix . $key]);
	}
}