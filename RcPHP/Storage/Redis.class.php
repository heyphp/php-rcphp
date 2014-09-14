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
namespace RCPHP\Storage;

defined('IN_RCPHP') or exit('Access denied');

class Redis
{

	/**
	 * Redis连接ID
	 *
	 * @var null|array
	 */
	protected $linkID = array();

	/**
	 * Redis 配置
	 *
	 * @var array
	 */
	private $_config = array();

	/**
	 * 构造方法
	 *
	 * @return void
	 */
	public function __construct(array $config = null)
	{
		if(!extension_loaded('redis'))
		{
			\RCPHP\Controller::halt('Does not support the Redis extension');
		}

		//加载Redis配置
		if(is_null($config))
		{
			$config = \RCPHP\RcPHP::getConfig("redis");

			if(empty($config))
			{
				\RCPHP\Controller::halt('The Redis configuration failed to load');
			}
		}

		$this->_config['master'] = array(
			'host' => $config['host'],
			'port' => $config['port']
		);

		if(!empty($config['password']))
		{
			$this->_config['master']['password'] = $config['password'];
		}

		//分析从库连接配置
		if(isset($config['slave']) && !empty($config['slave']) && is_array($config['slave']))
		{
			if(is_array($config['slave'][0]))
			{
				foreach($config['slave'] as $slave)
				{
					if(empty($slave['password']) && !empty($this->_config['master']['password']))
					{
						$slave['password'] = $this->_config['master']['password'];
					}
					$this->_config['slave'][] = $slave;
				}
			}
			else
			{
				if(empty($config['slave']['password']) && !empty($this->_config['master']['password']))
				{
					$slave['password'] = $this->_config['master']['password'];
				}
				$this->_config['slave'][] = $config['slave'];
			}

			$this->_config['slave'][] = $this->_config['master'];
		}
		else
		{
			$configs['slave'][] = $this->_config['master'];
		}
	}

	/**
	 * Connect redis.
	 *
	 * @param bool $master
	 * @return object
	 */
	public function connect($master = true)
	{
		$config = array();

		if($master === true)
		{
			$linkNum = 0;
			$config = $this->_config['master'];
		}
		else
		{
			if(isset($this->linkID[0]))
			{
				return $this->linkID[0];
			}

			$length = count($this->_config['slave']);

			$linkNum = $length == 1 ? 0 : array_rand($this->_config['slave']);
			$config = $this->_config['slave'][$linkNum];
			$linkNum++;
		}

		if(!isset($this->linkID[$linkNum]))
		{
			if(empty($config))
			{
				\RCPHP\Controller::halt('The Redis configuration failed to load');
			}

			//连接redis数据库
			$this->linkID[$linkNum] = new \Redis();
			$this->linkID[$linkNum]->connect($config['host'], $config['port'], 3);

			if(!$this->linkID[$linkNum])
			{
				\RCPHP\Controller::halt('The Redis connection failed');
			}

			\RCPHP\Debug::addMessage('Redis has been connected');

			//需要密码操作
			if(!empty($config['password']))
			{
				$this->linkID[$linkNum]->auth($config['password']);
			}
		}

		unset($config);

		return $this->linkID[$linkNum];
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
		$result = $this->connect()
					   ->set($key, $value);

		if($timeOut > 0)
		{
			$this->connect()
				 ->setTimeout($key, $timeOut);
		}

		\RCPHP\Debug::addMessage("Set redis cache, cache name is : " . $key);

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
		return $this->connect(false)
					->get($key);
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
			return $this->connect()
						->delete($key);
		}

		$num = func_num_args(); // 获取函数参数个数

		if($num > 1)
		{
			return $this->connect()
						->delete(func_get_args());
		}
		else
		{
			return $this->connect()
						->delete($key);
		}
	}

	/**
	 * 清空所有数据（慎用）
	 *
	 * @return bool
	 */
	public function flushAll()
	{
		return $this->connect()
					->flushAll();
	}

	/**
	 * 判断key是否存在
	 *
	 * @param string $key
	 * @return bool
	 */
	public function exists($key)
	{
		return $this->connect()
					->exists($key);
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
		return $flag === false ? $this->connect()
									  ->expire($key, $time) : $this->connect()
																   ->expireAt($key, $time);
	}

	/**
	 * 返回Redisd对象
	 *
	 * @param bool $master
	 * @return object
	 */
	public function redis($master = true)
	{
		return $this->connect($master);
	}

	/**
	 * 关闭数据库连接
	 *
	 * @return bool
	 */
	public function close()
	{
		$this->connect()
			 ->close();
		$this->connect(false)
			 ->close();

		\RCPHP\Debug::addMessage('Redis is close.');

		return true;
	}

	/**
	 * 析构函数
	 *
	 * @return object
	 */
	public function __destruct()
	{
		$this->close();
	}
}