<?php
/**
 * RcCacheApc class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        cache
 * @since          1.0
 * @filesource
 */
class RcCacheApc extends RcBase
{

	/**
	 * 构造方法 判断APC模块是否加载
	 *
	 * @return void
	 */
	public function __construct()
	{
		if(!extension_loaded('apc'))
		{
			RcController::halt('The apc extension must be loaded.');
		}
	}

	/**
	 * 设置缓存数据
	 * @param string|array $key
	 * @param string       $value
	 * @param int          $expire
	 * @return bool
	 */
	public function set($key, $value = '', $expire = 60)
	{
		if(empty($key))
		{
			return false;
		}

		if(is_array($key))
		{
			return apc_store($key, $expire);
		}
		else
		{
			return apc_store($key, $value, $expire);
		}
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

		return apc_fetch($key);
	}

	/**
	 * 删除缓存数据
	 * @param string $key
	 * @return bool
	 */
	public function delete($key)
	{
		if(empty($key))
		{
			return false;
		}

		return apc_delete($key);
	}

	/**
	 * 清除所有缓存数据 慎用
	 * @return bool
	 */
	public function clear()
	{
		return apc_clear_cache();
	}

	/**
	 * 检测指定缓存是否存在
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		if(empty($key))
		{
			return false;
		}

		return apc_exists($key);
	}
}