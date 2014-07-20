<?php
/**
 * Eaccelerator class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Cache
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Eaccelerator
{

	/**
	 * 构造方法 判断APC模块是否加载
	 *
	 * @return void
	 */
	public function __construct()
	{
		if(!extension_loaded('eaccelerator_put'))
		{
			RcController::halt('The eAccelerator extension must be loaded.');
		}
	}

	/**
	 * 设置缓存数据
	 *
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

		return eaccelerator_put($key, $value, $expire);
	}

	/**
	 * 获取缓存数据
	 *
	 * @param string $key
	 * @return string
	 */
	public function get($key)
	{
		if(empty($key))
		{
			return false;
		}

		return eaccelerator_get($key);
	}

	/**
	 * 删除缓存数据
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete($key)
	{
		if(empty($key))
		{
			return false;
		}

		return eaccelerator_rm($key);
	}

	/**
	 * 清除所有缓存数据 慎用
	 *
	 * @return bool
	 */
	public function clear()
	{
		eaccelerator_clean();

		return true;
	}

	/**
	 * 检测指定缓存是否存在
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		if(empty($key))
		{
			return false;
		}

		return eaccelerator_get($key) === null ? false : true;
	}
}