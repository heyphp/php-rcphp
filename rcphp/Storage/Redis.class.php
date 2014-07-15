<?php
/**
 * Redis class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Storage
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class Redis
{

	/**
	 * 单例模式实例化对象
	 *
	 * @var object
	 */
	protected static $_instance = null;

	/**
	 * Redis连接ID
	 *
	 * @var null|object
	 */
	protected $dbLink = null;

	/**
	 * 构造方法
	 *
	 * @return void
	 */
	public function __construct(array $config = null)
	{
		if(!extension_loaded('redis'))
		{
			Controller::halt('Does not support the Redis extension');
		}

		//加载Redis配置
		if(is_null($config))
		{
			$config = RcPHP::getConfig("redis");

			if(empty($config))
			{
				Controller::halt('The Redis configuration failed to load');
			}
		}

		//连接redis数据库
		$this->dbLink = new Redis();
		$this->dbLink->connect($config['host'], $config['port'], 3);

		if(!$this->dbLink)
		{
			Controller::halt('The Redis connection failed');
		}

		Debug::addMessage('Redis has been connected');

		//需要密码操作
		if(!empty($config['password']))
		{
			$this->dbLink->auth($config['password']);
		}
	}

	/**
	 * 设置key
	 *
	 * @param string $key
	 * @param string $value
	 * @param int    $timeOut
	 * @return bool
	 */
	public function set($key, $value, $timeOut = 0)
	{
		$result = $this->dbLink->set($key, $value);

		if($timeOut > 0)
		{
			$this->dbLink->setTimeout($key, $timeOut);
		}

		Debug::addMessage("Set redis cache, cache name is ：" . $key);

		return $result;
	}

	/**
	 * 获取key
	 *
	 * @param string $key
	 * @return string
	 */
	public function get($key)
	{
		return $this->dbLink->get($key);
	}

	/**
	 * 删除key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete($key)
	{
		if(is_array($key))
		{
			return $this->dbLink->delete($key);
		}

		$num = func_num_args(); // 获取函数参数个数

		if($num > 1)
		{
			return $this->dbLink->delete(func_get_args());
		}
		else
		{
			return $this->dbLink->delete($key);
		}
	}

	/**
	 * 清空所有数据（慎用）
	 *
	 * @return bool
	 */
	public function flushAll()
	{
		return $this->dbLink->flushAll();
	}

	/**
	 * 判断key是否存在
	 *
	 * @param string $key
	 * @return bool
	 */
	public function exists($key)
	{
		return $this->dbLink->exists($key);
	}

	/**
	 * 设置过期时间
	 *
	 * @param string $key
	 * @param int    $time
	 * @param bool   $flag
	 * @return bool
	 */
	public function expire($key, $time, $flag = false)
	{
		return $flag === false ? $this->dbLink->expire($key, $time) : $this->dbLink->expireAt($key, $time);
	}

	/**
	 * 返回Redisd对象
	 *
	 * @return object
	 */
	public function redis()
	{
		return $this->dbLink;
	}

	/**
	 * 关闭数据库连接
	 *
	 * @return object
	 */
	public function close()
	{
		return $this->dbLink->close();
	}

	/**
	 * 析构函数
	 *
	 * @return object
	 */
	public function __destruct()
	{
		Debug::addMessage('Redis is close.');

		$this->close();
	}

	/**
	 * 单例模式
	 *
	 * @param mixed $params
	 * @return object
	 */
	public static function getInstance($params = null)
	{
		if(!self::$_instance)
		{
			self::$_instance = new self($params);
		}

		return self::$_instance;
	}
}