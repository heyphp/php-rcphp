<?php
/**
 * RcCacheWinCache class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        cache
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcCacheWinCache extends RcBase
{

	/**
	 * 构造方法 判断wincache模块是否加载
	 *
	 * @return void
	 */
	public function __construct()
	{
		if(!function_exists('wincache_ucache_set'))
		{
			RcController::halt('The WinCache extension must be loaded.');
		}
	}

	/**
	 * 设置缓存数据
	 * @param string|array $key
	 * @param string       $value
	 * @param int          $expire
	 * @return boolen
	 */
	public function set($key, $value = '', $expire = 60)
	{
		if(empty($key))
		{
			return false;
		}

		return wincache_ucache_set($key, $value, $expire);
	}

	/**
	 * 获取缓存数据
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		if(empty($key))
		{
			return false;
		}

		return wincache_ucache_get($key);
	}

	/**
	 * 删除缓存数据
	 * @param string $key
	 * @return boolen
	 */
	public function delete($key)
	{
		if(empty($key))
		{
			return false;
		}

		return wincache_ucache_delete($key);
	}

	/**
	 * 清除所有缓存数据 慎用
	 * @return boolen
	 */
	public function clear()
	{
		return wincache_ucache_clear();
	}

	/**
	 * 检测指定缓存是否存在
	 * @param string $key
	 * @return boolen
	 */
	public function has($key)
	{
		if(empty($key))
		{
			return false;
		}

		return wincache_ucache_exists($key);
	}
}