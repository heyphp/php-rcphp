<?php
/**
 * Yac class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Cache
 * @since          1.0
 */
namespace RCPHP\Cache;

defined('IN_RCPHP') or exit('Access denied');

class Yac
{

	/**
	 * 连接实例
	 *
	 * @var null|object
	 */
	private $_conn = null;

	/**
	 * 构造方法 判断Yac模块是否加载
	 *
	 * @return void
	 */
	public function __construct()
	{
		if(!extension_loaded('yac'))
		{
			\RCPHP\Controller::halt('The Yac extension must be loaded.');
		}

		$this->_conn = new Yac();
	}

	/**
	 * 写入缓存
	 *
	 * @param string $key
	 * @param string $data
	 * @param int    $expire
	 * @return bool
	 */
	public function set($key, $value, $expire = null)
	{
		if(empty($key))
		{
			return false;
		}

		return is_null($expire) ? $this->_conn->set($key, $value) : $this->_conn->set($key, $value, $expire);
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

		return $this->_conn->get($key);
	}

	/**
	 * 删除缓存数据
	 *
	 * @param string|array $key
	 * @return bool
	 */
	public function delete($key)
	{
		if(empty($key))
		{
			return false;
		}

		return $this->_conn->delete($key);
	}

	/**
	 * 清除所有的缓存数据
	 *
	 * @return bool
	 */
	public function clear()
	{
		return $this->_conn->flush();
	}

	/**
	 * 获取服务信息
	 *
	 * @return array
	 */
	public function info()
	{
		return $this->_conn->info();
	}

	/**
	 * 析构函数
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if($this->_conn)
		{
			$this->_conn = null;
		}
	}
}