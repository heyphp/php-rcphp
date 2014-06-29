<?php
/**
 * RcDbMysql class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcDbMysql extends RcBase
{

	/**
	 * ����ģʽʵ��������
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
	 * @var boolean
	 */
	public $Transactions = false;

	/**
	 * ���캯��
	 * @return void
	 */
	public function __construct($config = array())
	{
		if(empty($config['host']) || empty($config['user']) || empty($config['password']))
		{
			RcController::halt('Database configuration error');
		}

		if(isset($config['port']) && empty($config['port']))
		{
			$config['host'] .= ":" . $config['port'];
		}

		$this->dbLink = mysql_connect($config['host'], $config['user'], $config['password']);

		if(!$this->dbLink)
		{
			RcController::halt('Mysql connection failed Error number��' . mysql_errno() . ' Error message��' . mysql_error());
		}
		else
		{
			if(mysql_select_db($config['database'], $this->dbLink))
			{
				mysql_query('SET NAMES ' . $config['charset']);
			}
			else
			{
				RcController::halt('Mysql database connection failed Error number��' . mysql_errno() . ' Error message��' . mysql_error());
			}
		}

		RcDebug::addMessage('���ݿ������ӣ�����Ϊ' . $config['driver']);

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
		$result = mysql_query($sql, $this->dbLink);
		$use = sprintf('%.5f', microtime(true) - $stime);
		if(!$result)
		{
			RcController::halt($sql . '����The SQL query failed Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		//Record the slow query log
		if($use >= 0.5)
		{
			RcLog::write($use, $sql, 'Slow', 'slow_' . date('Ymd'));
		}

		//DEBUG������Ϣ
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
		$result = mysql_query($sql);
		$use = sprintf('%.5f', microtime(true) - $stime);
		if($result === false)
		{
			RcController::halt('The SQL query failed Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		//Record the slow query log
		if($use >= 0.5)
		{
			RcLog::write($use, $sql, 'Slow', 'slow_' . date('Ymd'));
		}

		//DEBUG������Ϣ
		RcDebug::addMessage($use > 0.5 ? "<font color='red'>" . $sql . " [" . $use . "��]</font>" : $sql . " [" . $use . "��]", 2);

		return mysql_affected_rows();
	}

	/**
	 * ���ش�����Ϣ
	 * @return string
	 */
	public function error()
	{

		return ($this->dbLink) ? mysql_error($this->dbLink) : mysql_error();
	}

	/**
	 * ���ش������
	 * @return int
	 */
	public function errno()
	{
		return ($this->dbLink) ? mysql_errno($this->dbLink) : mysql_errno();
	}

	/**
	 * �������²������ݵ�ID
	 *
	 * @return int
	 */
	public function insertId()
	{
		return ($id = mysql_insert_id($this->dbLink)) >= 0 ? $id : mysql_result($this->query("SELECT last_insert_id()"));
	}

	/**
	 * ��ȡȫ��������Ϣ���ֶ��ͣ�
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

		if(!$result)
		{
			RcController::halt($sql . '����The SQL query failed Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		$rows = array();

		while($row = mysql_fetch_assoc($result))
		{
			$rows[] = $row;
		}

		mysql_free_result($result);

		return $rows;
	}

	/**
	 * ��ȡ����������Ϣ���ֶ��ͣ�
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

		if(!$result)
		{
			RcController::halt($sql . '����The SQL query failed Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		$row = mysql_fetch_assoc($result);

		mysql_free_result($result);

		return $row;
	}

	/**
	 * ��ȡ����������Ϣ���ֶ��ͣ�
	 * @param string $sql
	 * @return array
	 */
	public function fetchColumn($sql)
	{
		//�����ж�
		if(empty($sql))
		{
			return false;
		}

		$result = $this->query($sql);

		if(!$result)
		{
			RcController::halt($sql . '����The SQL query failed Error number��' . $this->errno() . ' Error message��' . $this->error());
		}

		$row = mysql_fetch_row($result);

		mysql_free_result($result);

		return $row[0];
	}

	/**
	 * ����������
	 * @return boolen
	 */
	public function trans()
	{
		if($this->Transactions == false)
		{
			mysql_query("BEGIN");
			$this->Transactions = true;
			RcDebug::addMessage("����������");
		}

		return true;
	}

	/**
	 * �����ύ
	 * @return true
	 */
	public function commit()
	{
		if($this->Transactions == true)
		{
			if($this->query('COMMIT'))
			{
				RcDebug::addMessage("���������ύ");
				$this->Transactions = false;
				RcDebug::addMessage("�������ѽ���");
			}
			else
			{
				RcController::halt('Transaction commit exception Error number��' . $this->errno() . ' Error message��' . $this->error());
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
	 * @return true
	 */
	public function rollback()
	{
		if($this->Transactions == true)
		{
			if($this->query('ROLLBACK'))
			{
				RcDebug::addMessage("�������ѻع�");
				$this->Transactions = false;
				RcDebug::addMessage("�������ѽ���");
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
	 * @return string|boolen
	 */
	public function escapeString($string = null)
	{
		if(is_null($string))
		{
			return false;
		}

		return mysql_real_escape_string($string);
	}

	/**
	 * �������ݿ�����
	 * @return boolen
	 */
	public function close()
	{
		if($this->dbLink)
		{
			mysql_close($this->dbLink);
		}

		return true;
	}

	/**
	 * �������� �������ݿ�����
	 * @return boolen
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * ����ģʽ
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
