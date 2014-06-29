<?php
/**
 * RcDbPdoMysql class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcDbPdoMysql extends RcBase
{

	/**
	 * ����ģʽʵ��������
	 *
	 * @var object
	 */
	public static $_instance;

	/**
	 * ���ݿ�����ID
	 *
	 * @var object
	 */
	public $dbLink;

	/**
	 * ��������״̬
	 *
	 * @var boolean
	 */
	public $Transactions = false;

	/**
	 * ���췽�� ��ʼ���������ݿ�
	 *
	 * @param array $config
	 * @return bool
	 */
	public function __construct($config = array())
	{
		if(empty($config['dsn']))
		{
			RcController::halt('Database connection configuration error');
		}

		try
		{
			$this->dbLink = new PDO($config['dsn'], $config['user'], $config['password'], array(
				PDO::ATTR_TIMEOUT => 3
			));
		}
		catch(PDOException $e)
		{
			RcController::halt('Mysql connection failed. Error number��' . $e->getCode() . ' Error message��' . $e->getMessage());
		}

		if($config['driver'] == 'pdo_mysql')
		{
			$this->dbLink->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
			$this->dbLink->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
			$this->dbLink->exec('SET NAMES ' . $config['charset']);
		}

		RcDebug::addMessage('Database has been connected��Drive for ' . $config['driver']);

		return true;
	}

	/**
	 * ����SQL���
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
			RcController::halt('The SQL query failed. Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		//Record the slow query log
		if($use >= 0.5)
		{
			RcLog::write($use, $sql, 'Slow', 'slow_' . date('Ymd'));
		}

		//RcDebug������Ϣ
		RcDebug::addMessage($use > 0.5 ? "<font color='red'>" . $sql . " [" . $use . "��]</font>" : $sql . " [" . $use . "��]", 2);

		return $result;
	}

	/**
	 * ���팑��SQL��䣨��Ҫᘌ���������Ո�� ����Ӱ��Д���
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
			RcController::halt('The SQL query failed. Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		//Record the slow query log
		if($use >= 0.5)
		{
			RcLog::write($use, $sql, 'Slow', 'slow_' . date('Ymd'));
		}

		//RcDebug������Ϣ
		RcDebug::addMessage($use > 0.5 ? "<font color='red'>" . $sql . " [" . $use . "��]</font>" : $sql . " [" . $use . "��]", 2);

		return $result;
	}

	/**
	 * ���ش�����Ϣ
	 *
	 * @return string
	 */
	public function error()
	{
		$errorInfo = $this->dbLink->errorInfo();

		return $errorInfo[2];
	}

	/**
	 * ���ش������
	 *
	 * @return int
	 */
	public function errno()
	{
		return $this->dbLink->errorCode();
	}

	/**
	 * �������²������ݵ�ID
	 *
	 * @return int
	 */
	public function insertId()
	{
		return $this->dbLink->lastInsertId();
	}

	/**
	 * ��ȡȫ��������Ϣ���ֶ��ͣ�
	 *
	 * @param string $sql
	 * @return array
	 */
	public function fetchAll($sql)
	{
		//�����ж�
		if(empty($sql))
		{
			return false;
		}

		$result = $this->query($sql);

		if($result === false)
		{
			RcController::halt($sql . 'The SQL query failed. Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

		unset($result);

		return $rows;
	}

	/**
	 * ��ȡ����������Ϣ���ֶ��ͣ�
	 *
	 * @param string $sql
	 * @return array
	 */
	public function fetchRow($sql)
	{
		//�����ж�
		if(empty($sql))
		{
			return false;
		}

		$result = $this->query($sql);

		if($result === false)
		{
			RcController::halt($sql . 'The SQL query failed. Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		$row = $result->fetch(PDO::FETCH_ASSOC);

		unset($result);

		return $row;
	}

	/**
	 * ��ȡ����������Ϣ���ֶ��ͣ�
	 *
	 * @param string $sql
	 * @return mixed
	 */
	public function fetchColumn($sql)
	{
		//�����ж�
		if(empty($sql))
		{
			return false;
		}

		$result = $this->query($sql);

		if($result === false)
		{
			RcController::halt($sql . 'The SQL query failed. Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		$column = $result->fetchColumn();

		unset($result);

		return $column;
	}

	/**
	 * ����������
	 *
	 * @return bool
	 */
	public function trans()
	{
		if($this->Transactions == false)
		{
			$this->dbLink->beginTransaction();
			$this->Transactions = true;
			RcDebug::addMessage("Open transaction.");
		}

		return true;
	}

	/**
	 * �����ύ
	 *
	 * @return true
	 */
	public function commit()
	{
		if($this->Transactions == true)
		{
			if($this->dbLink->commit())
			{
				RcDebug::addMessage("The transaction has been submitted.");
				$this->Transactions = false;
				RcDebug::addMessage("The transaction is over.");
			}
			else
			{
				RcController::halt('Transaction commit exception. Error number��' . $this->errno() . ' Error message��' . $this->error());
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
	 * ����ع�
	 *
	 * @return true
	 */
	public function rollback()
	{
		if($this->Transactions == true)
		{
			if($this->dbLink->rollBack())
			{
				RcDebug::addMessage("The transaction has been rolled back.");
				$this->Transactions = false;
				RcDebug::addMessage("The transaction is over.");
			}
			else
			{
				RcController::halt('Transaction rollback exception Error number��' . $this->errno() . ' Error message��' . $this->error());
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
	 * �ַ���ת�� ��SQLע��
	 *
	 * @param string $string
	 * @return string|bool
	 */
	public function escapeString($string = null)
	{
		if(is_null($string))
		{
			return false;
		}

		return trim($this->dbLink->quote($string), '\'');
	}

	/**
	 * �������ݿ�����
	 *
	 * @return bool
	 */
	public function close()
	{
		if($this->dbLink)
		{
			$this->dbLink = null;
		}

		return true;
	}

	/**
	 * �������� �������ݿ�����
	 *
	 * @return bool
	 */
	public function __destruct()
	{
		$this->close();

		RcDebug::addMessage('Database has been disconnected');
	}

	/**
	 * ����ģʽ
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
