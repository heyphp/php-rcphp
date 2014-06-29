<?php
/**
 * RcDbMysqli class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcDbMysqli extends RcBase
{

	/**
	 * 单例模式实例化对象
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
	 * @var boolean
	 */
	public $Transactions = false;

	/**
	 * 构造函数
	 * @return void
	 */
	public function __construct($config = array())
	{
		if(empty($config['host']) || empty($config['user']) || empty($config['password']))
		{
			RcController::halt('Database configuration error');
		}

		$this->dbLink = mysqli_init();
		$this->dbLink->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
		if(!empty($config['port']))
		{
			$this->dbLink->real_connect($config['host'], $config['user'], $config['password'], $config['database'], $config['port']);
		}
		else
		{
			$this->dbLink->real_connect($config['host'], $config['user'], $config['password'], $config['database']);
		}

		if(mysqli_connect_errno())
		{
			RcController::halt('Mysql connection failed Error number：' . mysqli_connect_errno() . ' Error message：' . mysqli_connect_error());
		}
		else
		{
			$this->dbLink->query('SET NAMES ' . $config['charset']);
		}

		RcDebug::addMessage('数据库已连接，驱动为' . $config['driver']);

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
		if(!$result)
		{
			RcController::halt($sql . '　　The SQL query failed Error number：' . $this->errno() . ' Error message：' . $this->error());
		}

		//Record the slow query log
		if($use >= 0.5)
		{
			RcLog::write($use, $sql, 'Slow', 'slow_' . date('Ymd'));
		}

		//DEBUG调试信息
		RcDebug::addMessage($use > 0.5 ? "<font color='red'>" . $sql . " [" . $use . "秒]</font>" : $sql . " [" . $use . "秒]", 2);

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
		$result = $this->dbLink->query($sql);
		$use = sprintf('%.5f', microtime(true) - $stime);
		if($result === false)
		{
			RcController::halt('The SQL query failed Error number：' . $this->errno() . ' Error message：' . $this->error());
		}

		//Record the slow query log
		if($use >= 0.5)
		{
			RcLog::write($use, $sql, 'Slow', 'slow_' . date('Ymd'));
		}

		//DEBUG调试信息
		RcDebug::addMessage($use > 0.5 ? "<font color='red'>" . $sql . " [" . $use . "秒]</font>" : $sql . " [" . $use . "秒]", 2);

		return $this->dbLink->affected_rows;
	}

	/**
	 * 返回错误信息
	 * @return string
	 */
	public function error()
	{

		return $this->dbLink->error;
	}

	/**
	 * 返回错误代码
	 * @return int
	 */
	public function errno()
	{
		return $this->dbLink->errno;
	}

	/**
	 * 返回最新插入数据的ID
	 *
	 * @return int
	 */
	public function insertId()
	{
		return ($id = $this->dbLink->insert_id) >= 0 ? $id : $this->query("SELECT last_insert_id()")
																  ->fetch_row();
	}

	/**
	 * 获取全部数据信息（字段型）
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

		if(!$result)
		{
			RcController::halt($sql . '　　The SQL query failed Error number：' . $this->errno() . ' Error message：' . $this->error());
		}

		$rows = array();
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}

		$result->free();

		return $rows;
	}

	/**
	 * 获取单行数据信息（字段型）
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

		if(!$result)
		{
			RcController::halt($sql . '　　The SQL query failed Error number：' . $this->errno() . ' Error message：' . $this->error());
		}

		$row = $result->fetch_assoc();

		$result->free();

		return $row;
	}

	/**
	 * 获取单列数据信息（字段型）
	 * @param string $sql
	 * @return array
	 */
	public function fetchColumn($sql)
	{
		//参数判断
		if(empty($sql))
		{
			return false;
		}

		$result = $this->query($sql);

		if(!$result)
		{
			RcController::halt($sql . '　　The SQL query failed Error number：' . $this->errno() . ' Error message：' . $this->error());
		}

		$row = $result->fetch_row();

		$result->free();

		return $row[0];
	}

	/**
	 * 开启事务处理
	 * @return boolen
	 */
	public function trans()
	{
		if($this->Transactions == false)
		{
			$this->dbLink->autocommit(false);
			$this->Transactions = true;
			RcDebug::addMessage("开启事务处理");
		}

		return true;
	}

	/**
	 * 事务提交
	 * @return true
	 */
	public function commit()
	{
		if($this->Transactions == true)
		{
			$result = $this->dbLink->commit();

			if($result)
			{
				$this->dbLink->autocommit(true);
				RcDebug::addMessage("事务处理已提交");
				$this->Transactions = false;
				RcDebug::addMessage("事务处理已结束");
			}
			else
			{
				RcController::halt('Transaction commit exception Error number：' . $this->errno() . ' Error message：' . $this->error());
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
	 * @return true
	 */
	public function rollback()
	{
		if($this->Transactions == true)
		{
			$result = $this->dbLink->rollback();

			if($result)
			{
				$this->dbLink->autocommit(true);
				RcDebug::addMessage("事务处理已回滚");
				$this->Transactions = false;
				RcDebug::addMessage("事务处理已结束");
			}
			else
			{
				RcController::halt('Transaction rollback exception Error number：' . $this->errno() . ' Error message：' . $this->error());
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
	 * @return string|boolen
	 */
	public function escapeString($string = null)
	{
		if(is_null($string))
		{
			return false;
		}

		return $this->dbLink->real_escape_string($string);
	}

	/**
	 * 销毁数据库连接
	 * @return boolen
	 */
	public function close()
	{
		if($this->dbLink)
		{
			$this->dbLink->close();
		}

		return true;
	}

	/**
	 * 析构函数 销毁数据库连接
	 * @return boolen
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * 单例模式
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
