<?php
/**
 * RcModel class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

abstract class RcModel extends RcBase
{

	/**
	 * ���ݿ���������
	 *
	 * @var array
	 */
	protected $_config = array();

	/**
	 * SQL������� ���SQL���Ƭ��
	 *
	 * @var array
	 */
	protected $_params = array();

	/**
	 * �����ݿ�ʵ��������
	 *
	 * @var object
	 */
	protected $_master = null;

	/**
	 * �����ݿ�ʵ��������
	 *
	 * @var object
	 */
	protected $_slave = null;

	/**
	 * ���ݿ����ӳ�
	 *
	 * @var array
	 */
	protected $_links = array();

	/**
	 * ���ݿ�ʵ�����Ƿ�Ϊ����ģʽ
	 *
	 * @var bool
	 */
	protected $_singleton = false;

	/**
	 * ���ݿ��ǰ׺
	 *
	 * @var string
	 */
	protected $_prefix;

	/**
	 * ���캯��
	 *
	 * @return void
	 */
	public function __construct()
	{
		//��ȡ���ݿ����Ӳ���
		$this->_config = $this->config();
	}

	/**
	 * ������ݱ�ǰ׺
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->_prefix;
	}

	/**
	 * ��ȡ���ݱ��ֶ���Ϣ
	 *
	 * @param string $tableName
	 * @return array
	 */
	public function getTableFields($tableName)
	{
		//��ѯ���ݱ��ֶ���Ϣ
		$tableName = !empty($this->_prefix) ? $this->_prefix . $tableName : $tableName;

		$sql = "SHOW FIELDS FROM " . $tableName;

		$result = $this->slave()
					   ->fetchAll($sql);

		$fields = array();

		foreach($result as $key => $value)
		{
			$fields[] = $value['Field'];
		}

		return $fields;
	}

	/**
	 * ��װSQL����е�from����
	 *
	 * @param string $tableName
	 * @param array  $fields
	 * @return object
	 */
	public function from($tableName = null, $fields = null)
	{
		if(is_null($tableName))
		{
			RcController::halt('Table name is empty');
		}

		$tableStr = (!empty($this->_prefix)) ? $this->_prefix . trim($tableName) : trim($tableName);

		if(is_array($fields))
		{
			$fields = $this->_parseFields($fields);
		}

		if(is_null($fields))
		{
			$fields = $this->getTableFields($tableName);
			$fields = implode(',', $fields);
		}

		$this->_params['from'] = "SELECT " . $fields . " FROM " . $tableStr;

		return $this;
	}

	/**
	 * �������ݱ��ֶ���Ϣ
	 *
	 * @param array $fields
	 * @return string
	 */
	protected function _parseFields($fields = null)
	{
		if(is_null($fields))
		{
			RcController::halt('Table fields is empty');
		}

		$fieldsStr = "";

		if(is_array($fields))
		{
			$fieldArray = array();
			foreach($fields as $key => $value)
			{
				$fieldArray[] = is_int($key) ? $value : "`" . $value . "` AS `" . $key . "`";
			}

			$fieldsStr = implode(',', $fieldArray);
			//���ٲ���Ҫ������ �����ڴ�ռ��
			unset($fieldArray);
		}
		else
		{
			$fieldsStr = "`" . $fields . "`";
		}

		return $fieldsStr;
	}

	/**
	 * ��װSQL����WHERE���
	 *
	 * @param string|array $where
	 * @return object
	 */
	public function where($where)
	{

		//ֱ�Ӵ��������ַ���
		if(!is_array($where))
		{
			$this->_params['where'] = isset($this->_params['where']) ? $this->_params['where'] . ' AND ' . $where : ' WHERE ' . $where;

			return $this;
		}
		$this->_parseWhere($where, true);

		return $this;
	}

	/**
	 * ��װSQL����ORWHERE���
	 *
	 * @param string|array $where
	 * @return object
	 */
	public function orwhere($where)
	{

		//ֱ�Ӵ��������ַ���
		if(!is_array($where))
		{
			$this->_params['orWhere'] = isset($this->_params['where']) ? $this->_params['where'] . ' AND ' . $where : ' WHERE ' . $where;

			return $this;
		}
		$this->_parseWhere($where, false);

		return $this;
	}

	/**
	 * ����where����
	 *
	 * @param string $where
	 * @param bool   $isWhere
	 * @return object
	 */
	protected function _parseWhere($where, $isWhere = true)
	{
		if(empty($where))
		{
			RcController::halt('SQL query condition is empty');
		}

		if(is_array($where))
		{
			$wheres = array();
			foreach($where as $string)
			{
				$wheres[] = trim($string);
			}

			$where = implode(' AND ', $wheres);

			unset($wheres);
		}

		if($isWhere == true)
		{
			$this->_params['where'] = isset($this->_params['where']) ? $this->_params['where'] . ' AND ' . $where : ' WHERE ' . $where;
		}
		else
		{
			$this->_params['orWhere'] = isset($this->_params['orWhere']) ? $this->_params['orWhere'] . ' AND ' . $where : ' OR ' . $where;
		}

		return $this;
	}

	/**
	 * ��װSQL���order by����
	 *
	 * @example
	 * 1.
	 * $this->order(' id DESC');
	 * 2.
	 * $this->order(array('id desc','price asc'));
	 * @param string|array $order
	 * @return object
	 */
	public function order($order)
	{
		if(empty($order))
		{
			RcController::halt('SQL query sort is empty');
		}

		if(is_array($order))
		{
			$orders = array();

			foreach($order as $o)
			{
				$orders[] = trim($o);
			}

			$order = implode(',', $orders);
		}

		$order = trim($order);

		$this->_params['order'] = isset($this->_params['order']) ? $this->_params['order'] . ',' . $order : ' ORDER BY ' . $order;

		return $this;
	}

	/**
	 * ��װLIMIT���
	 *
	 * @param int $offset
	 * @param int $count
	 * @return object
	 */
	public function limit($offset, $count = null)
	{

		$offset = intval($offset);
		$count = intval($count);

		$limitStr = ($count > 0) ? $offset . ', ' . $count : $offset;
		$this->_params['limit'] = ' LIMIT ' . $limitStr;

		return $this;
	}

	/**
	 * ��ҳʹ�õ�LIMIT���
	 *
	 * @param int $page
	 * @param int $count
	 * @return object
	 */
	public function page($page, $count)
	{
		$startId = intval($count) * (intval($page) - 1);

		$this->limit($startId, $count);

		return $this;
	}

	/**
	 * ��װJOIN���
	 *
	 * @param string $tableName
	 * @param string $where
	 * @param string $join
	 * @return object
	 */
	public function join($tableName, $where, $join = 'INNER')
	{
		//��������
		if(empty($tableName) || empty($where))
		{
			RcController::halt('The name of the table or the condition is empty');
		}

		$tableNameStr = (!empty($this->_prefix)) ? $this->_prefix . trim($tableName) : '`' . trim($tableName) . '`';

		//�����������
		$where = trim($where);
		$this->_params['join'] = isset($this->_params['join']) ? $this->_params['join'] . ' ' . $join . ' JOIN ' . $tableNameStr . ' ON ' . $where : ' ' . $join . ' JOIN ' . $tableNameStr . ' ON ' . $where;

		return $this;
	}

	/**
	 * ��װgroup���
	 *
	 * @param array|string $fieldName
	 * @return object
	 */
	public function group($fieldName)
	{
		if(empty($fieldName))
		{
			RcController::halt('The SQL statement grouping field does not exist');
		}

		if(is_array($fieldName))
		{
			$groupArray = array();
			foreach($fieldName as $field)
			{
				$groupArray[] = trim($field);
			}
			$fieldName = implode(',', $groupArray);
			unset($groupArray);
		}

		$this->_params['group'] = isset($this->_params['group']) ? $this->_params['group'] . ', ' . $fieldName : ' GROUP BY ' . $fieldName;

		return $this;
	}

	/**
	 * ��װSQL����having���
	 *
	 * @param string|array $where
	 * @return object
	 */
	public function having($where)
	{
		$this->_parseWhere($where, true);

		return $this;
	}

	/**
	 * ��װSQL����orhaving���
	 *
	 * @param string|array $where
	 * @return object
	 */
	public function orhaving($where)
	{
		$this->_parseWhere($where, false);

		return $this;
	}

	/**
	 * ����where����
	 *
	 * @param string $where
	 * @param bool   $isWhere
	 * @return object
	 */
	protected function _parseHaving($where, $isWhere = true)
	{
		if(empty($where))
		{
			RcController::halt('SQL query packets condition is empty');
		}

		if(is_array($where))
		{
			$wheres = array();
			foreach($where as $string)
			{
				$wheres[] = trim($string);
			}

			$where = implode(' AND ', $wheres);

			unset($wheres);
		}

		if($isWhere == true)
		{
			$this->_params['having'] = isset($this->_params['having']) ? $this->_params['having'] . ' AND ' . $where : ' WHERE ' . $where;
		}
		else
		{
			$this->_params['orHaving'] = isset($this->_params['orHaving']) ? $this->_params['orHaving'] . ' AND ' . $where : ' OR ' . $where;
		}

		return $this;
	}

	/**
	 * ��ѯ��������
	 *
	 * @return array
	 */
	public function fetchAll()
	{
		$sqlStr = '';
		//ƴװSQL���
		if(!empty($this->_params))
		{
			foreach($this->_params as $key => $value)
			{
				$sqlStr .= $value;
			}

			//���ٱ���
			$this->_params = array();

			return $this->slave()
						->fetchAll($sqlStr);
		}
		else
		{
			return false;
		}
	}

	/**
	 * ��ѯ��������
	 *
	 * @return array
	 */
	public function fetchRow()
	{
		$sqlStr = '';
		//ƴװSQL���
		if(!empty($this->_params))
		{
			foreach($this->_params as $key => $value)
			{
				$sqlStr .= $value;
			}

			//���ٱ���
			$this->_params = array();

			return $this->slave()
						->fetchRow($sqlStr);
		}
		else
		{
			return false;
		}
	}

	/**
	 * ��ѯ��������
	 *
	 * @return string
	 */
	public function fetchColumn()
	{
		$sqlStr = '';
		//ƴװSQL���
		if(!empty($this->_params))
		{
			foreach($this->_params as $key => $value)
			{
				$sqlStr .= $value;
			}

			//���ٱ���
			$this->_params = array();

			return $this->slave()
						->fetchColumn($sqlStr);
		}
		else
		{
			return false;
		}
	}

	/**
	 * ����������
	 *
	 * @example
	 * $data = array('name'=>'zhangwj', 'age'=>23, 'address'=>'������')  //keyΪ���ݱ��ֶ�
	 * $this->insert('user',$data);
	 * @param string $tableName
	 * @param array  $data
	 * @param bool   $insertId
	 * @return mixed
	 */
	public function insert($tableName, $data, $insertId = true)
	{
		//�Ժ����Ĳ��������ж�
		if(empty($tableName))
		{
			RcController::halt('Table name is empty');
		}

		if(!is_array($data))
		{
			RcController::halt('The data format is not correct');
		}

		if(empty($data))
		{
			return false;
		}

		//��װSQL���
		$fieldsArray = array();
		$valuesArray = array();

		foreach($data as $key => $value)
		{
			$fieldsArray[] = '`' . trim($key) . '`';
			$valuesArray[] = '\'' . $this->quote(trim($value)) . '\'';
		}

		$fieldString = implode(',', $fieldsArray);
		$valueString = implode(',', $valuesArray);

		$tableName = !empty($this->_prefix) ? $this->_prefix . trim($tableName) : trim($tableName);

		$sqlString = 'INSERT INTO `' . $tableName . '`(' . $fieldString . ') VALUES (' . $valueString . ')';

		//���ٲ���Ҫ�ı���
		unset($fieldString, $valueString);

		$result = $this->master()
					   ->execute($sqlString);

		if($result && $insertId)
		{
			return $this->getInsertId();
		}

		return $result;
	}

	/**
	 * ��������
	 *
	 * @param string       $tableName
	 * @param array        $data
	 * @param string|array $where
	 * @return object
	 */
	public function update($tableName, $data, $where = null)
	{
		if(empty($tableName))
		{
			RcController::halt('Table name is empty');
		}

		if(!is_array($data))
		{
			RcController::halt('The data format is not correct');
		}

		if(empty($data))
		{
			return false;
		}

		$values = array();
		foreach($data as $key => $value)
		{
			$values[] = '`' . $key . '` = \'' . $this->quote(trim($value)) . '\'';
		}
		$valueStr = implode(',', $values);
		unset($values);

		if(!is_null($where))
		{
			if(is_array($where))
			{
				$where = implode(" AND ", $where);
			}
		}

		$tableName = !empty($this->_prefix) ? $this->_prefix . trim($tableName) : trim($tableName);

		if(!is_null($where))
		{
			$sqlStr = 'UPDATE ' . $tableName . ' SET ' . $valueStr . ' WHERE ' . $where;
		}
		else
		{
			$sqlStr = 'UPDATE ' . $tableName . ' SET ' . $valueStr;
		}

		return $this->master()
					->execute($sqlStr);
	}

	/**
	 * ɾ������
	 *
	 * @param string       $tableName
	 * @param string|array $where
	 * @return object
	 */
	public function delete($tableName, $where = null)
	{
		if(empty($tableName))
		{
			RcController::halt('Table name is empty');
		}

		if(!is_null($where))
		{
			if(is_array($where))
			{
				$where = implode(" AND ", $where);
			}
		}

		$tableName = !empty($this->_prefix) ? $this->_prefix . trim($tableName) : trim($tableName);

		if(!is_null($where))
		{
			$sqlStr = 'DELETE FROM ' . $tableName . ' WHERE ' . $where;
		}
		else
		{
			$sqlStr = 'DELETE FROM ' . $tableName;
		}

		return $this->master()
					->execute($sqlStr);
	}

	/**
	 * ִ����������SQL���
	 *
	 * @param string $sql
	 * @param bool   $lines
	 * @param bool   $isSelect
	 * @return array | object
	 */
	public function execute($sql, $lines = true, $isSelect = false)
	{
		if(empty($sql))
		{
			return false;
		}

		$sql = strtolower($sql);

		if($isSelect == true)
		{
			return $lines == true ? $this->slave()
										 ->fetchAll($sql) : $this->slave()
																 ->fetchRow($sql);
		}

		return substr($sql, 0, 6) == 'select' ? ($lines == true ? $this->master()
																	   ->fetchAll($sql) : $this->master()
																							   ->fetchRow($sql)) : $this->master()
																														->execute($sql);
	}

	/**
	 * ��ȡ���²�������ID
	 *
	 * @return int
	 */
	public function getInsertId()
	{
		return $this->master()
					->insertId();
	}

	/**
	 * �ַ�����ת�� ��ֹSQLע��
	 *
	 * @return string
	 */
	public function quote($value = null)
	{

		//�����ж�
		if(is_null($value))
		{
			return false;
		}

		//�����Ƿ�Ϊ����
		if(is_array($value))
		{
			foreach($value as $key => $string)
			{
				$value[$key] = $this->quote($string);
			}
		}
		else
		{
			//������Ϊ�ַ������ַ�ʱ
			if(is_string($value))
			{
				$value = $this->master()
							  ->escapeString($value);
			}
		}

		return $value;
	}

	/**
	 * ��������
	 *
	 * @return void
	 */
	public function trans()
	{
		$this->master()
			 ->trans();
	}

	/**
	 * �ύ����
	 *
	 * @return void
	 */
	public function commit()
	{
		$this->master()
			 ->commit();
	}

	/**
	 * �ع�����
	 *
	 * @return void
	 */
	public function rollback()
	{
		$this->master()
			 ->rollback();
	}

	/**
	 * �������ݿ�����
	 * �������ݿ�������Ϣ
	 *
	 * @return array
	 */
	protected function config()
	{
		$params = $this->getConfig();

		if(!is_array($params))
		{
			RcController::halt('Error loading the database configuration');
		}

		//���ݿ��ǰ׺
		$this->_prefix = (isset($params['prefix']) && !empty($params['prefix'])) ? trim($params['prefix']) : '';

		//���ݿ�Ĭ�ϱ���  Ĭ�ϱ���GBK
		$params['charset'] = (isset($params['charset']) && !empty($params['charset'])) ? trim($params['charset']) : 'gbk';

		$configs = array();

		//����������������
		if(isset($params['master']) && !empty($params['master']))
		{
			$configs['master'] = $params['master'];
			$configs['master']['charset'] = $params['charset'];
			$configs['master']['driver'] = $params['driver'];
		}
		else
		{
			$configs['master'] = $params;
		}

		//�����ӿ���������
		if(isset($params['slave']) && !empty($params['slave']) && is_array($params['slave']))
		{
			foreach($params['slave'] as $slave)
			{
				$slave['charset'] = $params['charset'];
				$slave['driver'] = $params['driver'];

				$configs['slave'][] = $slave;
			}
		}
		else
		{
			//��û�дӿ����Ӳ���ʱ,��������ģʽ
			$this->_singleton = true;
			$configs['slave'][] = $configs['master'];
		}

		unset($params);

		return $configs;
	}

	/**
	 * ʵ���������ݿ�
	 *
	 * @return object
	 */
	protected function master()
	{
		RcDebug::addMessage("Master connection");

		return $this->_master = $this->factory($this->_config['master'], 0);
	}

	/**
	 * ʵ���������ݿ�
	 *
	 * @return object
	 */
	public function slave()
	{
		if($this->_singleton === true)
		{
			return $this->_master = $this->factory($this->_config['master'], 0);
		}

		//��ô����ݿ����õ�����
		$config_slave = $this->_config['slave'];

		if(defined('MASTER_READ') && MASTER_READ)
		{
			$config_slave[] = $this->_config['master'];
		}

		$length = count($config_slave);

		$index = $length == 1 ? 0 : array_rand($config_slave);

		return $this->_slave = $this->factory($config_slave[$index], $index + 1);
	}

	/**
	 * ��ȡ���ݿ���������
	 *
	 * @return array
	 */
	protected function getConfig()
	{
		return RcPHP::getConfig('config');
	}

	/**
	 * ����ģʽʵ�������ݿ���������
	 *
	 * @param array $config
	 * @return object
	 */
	public function factory($config, $linkNum = 0)
	{
		if(!isset($this->_links[$linkNum]))
		{
			$driver = $config['driver'];

			switch($driver)
			{
				case 'mysql':
				case 'mysqli':
					$this->_links[$linkNum] = new RcDbMysqli($config);
					break;
				case 'pdo_mysql':
					//���dsn��Ϣ
					if(!isset($config['dsn']))
					{
						$dsnArray = array();
						$dsnArray['host'] = $config['host'];
						$dsnArray['dbname'] = $config['database'];

						if(!empty($config['port']))
						{
							$dsnArray['port'] = $config['port'];
						}
						$config['dsn'] = sprintf('%s:%s', 'mysql', http_build_query($dsnArray, '', ';'));
					}
					$this->_links[$linkNum] = new RcDbPdoMysql($config);
					break;
				default:
					$this->_links[$linkNum] = new RcDbMysqli($config);
			}
		}

		return $this->_links[$linkNum];
	}

	/**
	 * ��������
	 *
	 * @return void
	 */
	public function __destruct()
	{
		unset($this->_params);
	}
}