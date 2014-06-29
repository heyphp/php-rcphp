<?php
/**
 * RcCacheMemcache class file.
 *
 * @author         RcPHP Dev Team
 * @version        $Id: RcCacheMemcache.php 1.0 2013-08-27 17:45 zhangwj $
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        cache
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

/**
 * 使用说明
 * @example
 * 参数范例
 * $mem_options = array(
 * 'servers'=> array(
 * array('host'=>'127.0.0.1', 'port'=>11211, 'persistent'=>true, 'weight'=>1, 'timeout'=>60),
 * array('host'=>'192.168.0.101', 'port'=>11211, 'persistent'=>true, 'weight'=>2, 'timeout'=>60),
 * ),
 * 'compressed'=>true,
 * 'lifeTime' => 3600,
 * 'persistent' => true,
 * );
 * 或在config项目目录中配置文件:memcache.ini.php,内容如下:
 * <?php
 * if(!defined('IN_RCPHP')) exit('Access denied');
 * return array(
 * 'servers'=> array(
 * array('host'=>'127.0.0.1', 'port'=>11211, 'persistent'=>true, 'timeout'=>60),
 * array('host'=>'192.168.0.101', 'port'=>11211, 'persistent'=>true, 'timeout'=>60),
 * ),
 * 'compressed'=>true,
 * 'lifeTime' => 3600,
 * 'persistent' => true,
 * );
 * 实例化
 * 法一:
 * $memcache = new RcCacheMemcache($mem_options);
 */
class RcCacheMemcache extends RcBase
{

	/**
	 * 单例模式实现化对象
	 * @var object
	 */
	protected static $_instance = null;

	/**
	 * 连接实例
	 * @var objeact
	 */
	private $_conn = null;

	/**
	 * 默认的缓存服务器
	 * @var array
	 */
	protected $_defaultServer = array(
		'host' => '127.0.0.1',
		'port' => '11211'
	);

	/**
	 * 默认的缓存策略
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
	 * @return void
	 */
	private function __construct(array $options = null)
	{
		if(!extension_loaded('memcache'))
		{
			RcController::halt('The memcache extension must be loaded before use!');
		}

		//当参数为空时,程序则自动加载config目录中的memcache.ini.php的配置文件
		if(is_null($options))
		{
			if(is_file(CONFIG_DIR . 'memcache.ini.php'))
			{
				$options = RcController::getConfig('memcache');
			}
		}

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
				RcController::halt(sprintf('Connect memcached server [%s:%s] failed!', $server['host'], $server['port']));
			}
		}
	}

	/**
	 * 单例模式 防止被外部使用
	 * @return boolean
	 */
	private function __clone()
	{
		return true;
	}

	/**
	 * 写入缓存
	 * @param string $key
	 * @param mixed  $data
	 * @param int    $expire
	 * @return void
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
	 * @param string $key
	 * @return boolean
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
	 * @return boolean
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
	 * @return object
	 */
	public function memcache()
	{
		return $this->_conn;
	}

	/**
	 * 析构函数
	 * @return void
	 */
	public function __destruct()
	{
		if($this->_conn)
		{
			$this->_conn->close();
		}
	}

	/**
	 * 单例模式
	 * @return object
	 */
	public static function getInstance($options = null)
	{

		if(self::$_instance === null)
		{
			self::$_instance = new RcCacheMemcache($options);
		}

		return self::$_instance;
	}
}