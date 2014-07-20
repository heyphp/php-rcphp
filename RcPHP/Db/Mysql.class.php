<?php
/**
 * Mysql class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Db
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Mysql
{

	/**
	 * 单例模式实例化对象
	 *
	 * @var object
	 */
	public static $_instance;

	/**
	 * 数据库连接ID
	 *
	 * @var object
	 */
	public $dbLink;

	/**
	 * 事务处理开启状态
	 *
	 * @var boolean
	 */
	public $Transactions = false;

	/**
	 * 构造方法 初始化连接数据库
	 *
	 * @param array $config
	 * @return bool
	 */
	public function __construct(array $config = array())
	{
		if(empty($config['dsn']))
		{
			Controller::halt('Database connection configuration error');
		}

		try
		{
			$this->dbLink = new PDO($config['dsn'], $config['user'], $config['password'], array(
				PDO::ATTR_TIMEOUT => 3
			));
		}
		catch(PDOException $e)
		{
			Controller::halt('Mysql connection failed. Error number：' . $e->getCode() . ' Error message：' . $e->getMessage());
		}

		if($config['driver'] == 'pdo_mysql')
		{
			$this->dbLink->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
			$this->dbLink->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
			$this->dbLink->exec('SET NAMES ' . $config['charset']);
		}

		Debug::addMessage('Database has been connected，Drive for ' . $config['driver']);

		return true;
	}

	/**
	 * 处理SQL语句
	 *
	 * @param string $sql
	 * @return object
	 */
	public function query($sql)
	{
		if(empty($sql))
		{
			return false;
		}

		$stime = microtime(true);
		$result = $this->dbLink->query($sql);
		$use = sprintf('%.5f', microtime(true) - $stime);
		if($result === false)
		{
			Controller::halt('The SQL query failed. Error number：' . $this->errno() . ' Error message：' . $this->error());
		}

		//Record the slow query log
		if($use >= 0.5 && (defined("RC_SLOW_LOG") && RC_SLOW_LOG === true))
		{
			Log::write($use, $sql, 'SLOW', 'slow_' . date('Ymd'));
		}

		//Debug调试信息
		Debug::addMessage($use > 0.5 ? "<font color='red'>" . $sql . " [" . $use . "秒]</font>" : $sql . " [" . $use . "秒]", 2);

		return $result;
	}

	/**
	 * 处理寫的SQL语句（主要針對寫數據的請求 返回影響行數）
	 *
	 * @param string $sql
	 * @return int
	 */
	public function execute($sql)
	{
		if(empty($sql))
		{
			return false;
		}

		$stime = microtime(true);
		$result = $this->dbLink->exec($sql);
		$use = sprintf('%.5f', microtime(true) - $stime);
		if($result === false)
		{
			Controller::halt('The SQL query failed. Error number：' . $this->errno() . ' Error message：' . $this->error());
		}

		//Record the slow query log
		if($use >= 0.5 && (defined("RC_SLOW_LOG") && RC_SLOW_LOG === true))
		{
			Log::write($use, $sql, 'SLOW', 'slow_' . date('Ymd'));
		}

		//Debug调试信息
		Debug::addMessage($use > 0.5 ? "<font color='red'>" . $sql . " [" . $use . "秒]</font>" : $sql . " [" . $use . "秒]", 2);

		return $result;
	}

	/**
	 * 返回错误信息
	 *
	 * @return string
	 */
	public function error()
	{
		$errorInfo = $this->dbLink->errorInfo();

		return $errorInfo[2];
	}

	/**
	 * 返回错误代码
	 *
	 * @return int
	 */
	public function errno()
	{
		return $this->dbLink->errorCode();
	}

	/**
	 * 返回最新插入数据的ID
	 *
	 * @return int
	 */
	public function insertId()
	{
		return $this->dbLink->lastInsertId();
	}

	/**
	 * 获取全部数据信息（字段型）
	 *
	 * @param string $sql
	 * @return array
	 */
	public function fetchAll($sql)
	{
		//参数判断
		if(empty($sql))
		{
			return false;
		}

		$result = $this->query($sql);

		if($result === false)
		{
			Controller::halt('The SQL query failed. Error number:' . $this->errno() . ' Error message:' . $this->error());
		}

		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

		unset($result);

		return $rows;
	}

	/**
	 * 获取单行数据信息（字段型）
	 *
	 * @param string $sql
	 * @return array
	 */
	public function fetchRow($sql)
	{
		//参数判断
		if(empty($sql))
		{
			return false;
		}

		$result = $this->query($sql);

		if($result === false)
		{
			Controller::halt('The SQL query failed. Error number:' . $this->errno() . ' Error message:' . $this->error());
		}

		$row = $result->fetch(PDO::FETCH_ASSOC);

		unset($result);

		return $row;
	}

	/**
	 * 获取单列数据信息（字段型）
	 *
	 * @param string $sql
	 * @return mixed
	 */
	public function fetchColumn($sql)
	{
		//参数判断
		if(empty($sql))
		{
			return false;
		}

		$result = $this->query($sql);

		if($result === false)
		{
			Controller::halt('The SQL query failed. Error number:' . $this->errno() . ' Error message:' . $this->error());
		}

		$column = $result->fetchColumn();

		unset($result);

		return $column;
	}

	/**
	 * 开启事务处理
	 *
	 * @return bool
	 */
	public function trans()
	{
		if($this->Transactions == false)
		{
			$this->dbLink->beginTransaction();
			$this->Transactions = true;
			Debug::addMessage("Open transaction.");
		}

		return true;
	}

	/**
	 * 事务提交
	 *
	 * @return true
	 */
	public function commit()
	{
		if($this->Transactions == true)
		{
			if($this->dbLink->commit())
			{
				Debug::addMessage("The transaction has been submitted.");
				$this->Transactions = false;
				Debug::addMessage("The transaction is over.");
			}
			else
			{
				Controller::halt('Transaction commit exception. Error number:' . $this->errno() . ' Error message:' . $this->error());
			}
		}
		else
		{
			$this->trans();
			$this->commit();
		}

		return true;
	}

	/**
	 * 事务回滚
	 *
	 * @return true
	 */
	public function rollback()
	{
		if($this->Transactions == true)
		{
			if($this->dbLink->rollBack())
			{
				Debug::addMessage("The transaction has been rolled back.");
				$this->Transactions = false;
				Debug::addMessage("The transaction is over.");
			}
			else
			{
				Controller::halt('Transaction rollback exception Error number:' . $this->errno() . ' Error message:' . $this->error());
			}
		}
		else
		{
			$this->trans();
			$this->rollback();
		}

		return true;
	}

	/**
	 * 字符串转义 防SQL注入
	 *
	 * @param string $string
	 * @return string|bool
	 */
	public function escapeString($string)
	{
		return $this->dbLink->quote($string);
	}

	/**
	 * 销毁数据库连接
	 *
	 * @return bool
	 */
	public function close()
	{
		if($this->dbLink)
		{
			$this->dbLink = null;

			Debug::addMessage('Database has been disconnected');
		}

		return true;
	}

	/**
	 * 析构函数 销毁数据库连接
	 *
	 * @return bool
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * 单例模式
	 *
	 * @param array $params
	 * @return object
	 */
	public static function getInstance($params)
	{
		if(!self::$_instance)
		{
			self::$_instance = new self($params);
		}

		return self::$_instance;
	}
}
