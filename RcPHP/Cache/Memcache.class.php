<?php
/**
 * Memcache class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Cache
 * @since          1.0
 */
namespace RCPHP\Cache;

defined('IN_RCPHP') or exit('Access denied');

class Memcache
{

	/**
	 * 连接实例
	 *
	 * @var null|object
	 */
	private $_conn = null;

	/**
	 * 默认的缓存服务器
	 *
	 * @var array
	 */
	protected $_defaultServer = array(
		'host' => '127.0.0.1',
		'port' => '11211'
	);

	/**
	 * 默认的缓存策略
	 *
	 * @var array
	 */
	protected $_defaultOptions = array(
		'servers' => array(),
		'compressed' => false,
		'lifeTime' => 900,
		'persistent' => true
	);

	/**
	 * 构造函数 判断模块的加载
	 *
	 * @return void
	 */
	public function __construct()
	{
		if(!extension_loaded('memcache'))
		{
			\RCPHP\Controller::halt('The memcache extension must be loaded before use!');
		}

		$options = \RCPHP\RcPHP::getConfig('memcache');

		if(is_array($options))
		{
			$this->_defaultOptions = array_merge($this->_defaultOptions, $options);
		}

		if(empty($this->_defaultOptions['servers']))
		{
			$this->_defaultOptions['servers'][] = $this->_defaultServer;
		}

		$this->_conn = new Memcache();

		foreach($this->_defaultOptions['servers'] as $server)
		{
			$result = $this->_conn->addServer($server['host'], $server['port'], $this->_defaultOptions['persistent']);

			if(!$result)
			{
				\RCPHP\Controller::halt(sprintf('Connect memcached server [%s:%s] failed!', $server['host'], $server['port']));
			}
		}
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

		if(is_null($expire))
		{
			$expire = $this->_defaultOptions['lifeTime'];
		}

		return $this->_conn->set($key, $value, empty($this->_defaultOptions['compressed']) ? 0 : MEMCACHE_COMPRESSED, $expire);
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
	 * @param string $key
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
		$this->_conn->flush();
	}

	/**
	 * 获取memcache server状态
	 *
	 * @return string
	 */
	public function stats()
	{
		return $this->_conn->getStats();
	}

	/**
	 * 返回连接 调用更多方法
	 *
	 * @return object
	 */
	public function memcache()
	{
		return $this->_conn;
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
			$this->_conn->close();
		}
	}
}