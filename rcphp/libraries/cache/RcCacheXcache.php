<?php
/**
 * RcCacheXcache class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        cache
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcCacheXcache extends RcBase
{

	/**
	 * 构造方法 判断扩展是否存在
	 * @return void
	 */
	public function __construct()
	{
		if(!extension_loaded('xcache'))
		{
			RcController::halt('The xcache extension must be loaded before use!');
		}
	}

	/**
	 * 设置缓存数据
	 * @param type $key
	 * @param type $value
	 * @param type $expire
	 */
	public function set($key, $value, $expire = 3600)
	{
		if(empty($key))
		{
			return false;
		}

		return xcache_set($key, $value, $expire);
	}

	/**
	 * 获取缓存数据
	 * @param type $key
	 * @return string
	 */
	public function get($key)
	{
		//如果key为空 直接返回false
		if(empty($key))
		{
			return false;
		}

		return xcache_get($key);
	}

	/**
	 * 删除缓存数据
	 * @param type $key
	 * @return boolen
	 */
	public function delete($key)
	{
		if(empty($key))
		{
			return false;
		}

		return xcache_unset($key);
	}

	/**
	 * 清除所有缓存数据 慎用
	 * @return boolen
	 */
	public function clear()
	{
		$count = xcache_count(XC_TYPE_VAR);
		for($i = 0; $i < $count; $i++)
		{
			xcache_clear_cache(XC_TYPE_VAR, $i);
		}

		return true;
	}

	/**
	 * 判断缓存数据是否存在
	 * @param string $key
	 * @return boolen
	 */
	public function has($key)
	{
		if(empty($key))
		{
			return false;
		}

		return xcache_isset($key);
	}
}
