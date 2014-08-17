<?php
/**
 * Model class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Model
{

	/**
	 * 数据库连接配置
	 *
	 * @var array
	 */
	protected $_config = array();

	/**
	 * SQL语句容器 存放SQL语句片段
	 *
	 * @var array
	 */
	protected $_params = array();

	/**
	 * 主数据库实例化对象
	 *
	 * @var object
	 */
	protected $_master = null;

	/**
	 * 从数据库实例化对象
	 *
	 * @var object
	 */
	protected $_slave = null;

	/**
	 * 数据库链接池
	 *
	 * @var array
	 */
	protected $_links = array();

	/**
	 * 数据库实例化是否为单例模式
	 *
	 * @var bool
	 */
	protected $_singleton = false;

	/**
	 * 数据库表前缀
	 *
	 * @var string
	 */
	protected $_prefix;

	/**
	 * 构造函数
	 *
	 * @return void
	 */
	public function __construct()
	{
		//获取数据库连接参数
		$this->_config = $this->config();
	}

	/**
	 * 获得数据表前缀
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->_prefix;
	}

	/**
	 * 获取数据表字段信息
	 *
	 * @param string $tableName
	 * @return array
	 */
	public function getTableFields($tableName)
	{
		//查询数据表字段信息
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
	 * 组装SQL语句中的from部分
	 *
	 * @param string $tableName
	 * @param array  $fields
	 * @return $this
	 */
	public function from($tableName, $fields = '*')
	{
		if(is_null($tableName))
		{
			Controller::halt('The table name is empty.');
		}

		$tableStr = (!empty($this->_prefix)) ? $this->_prefix . trim($tableName) : trim($tableName);

		if(empty($fields))
		{
			$fields = $this->getTableFields($tableName);
			$fields = implode(',', $fields);
		}

		$this->_params['from'] = "SELECT " . $fields . " FROM " . $tableStr;

		return $this;
	}

	/**
	 * 组装SQL语句的WHERE语句
	 *
	 * @param string $where
	 * @param string $cond
	 * @return $this
	 */
	public function where($where, $cond = '')
	{
		if(empty($where))
		{
			Controller::halt("The where param is empty.");
		}

		if(!empty($cond) && is_string($where))
		{
			if(!is_array($cond))
			{
				$parse = func_get_args();
				array_shift($parse);
			}
			$parse = $this->quote($parse);
			$where = vsprintf($where, $parse);
		}

		$this->_params['where'] = !empty($this->_params['where']) ? $this->_params['where'] . ' AND ' . $where : ' WHERE ' . $where;

		return $this;
	}

	/**
	 * 组装SQL语句order by部分
	 *
	 * @example
	 * 1.
	 * $this->order(' id DESC');
	 * 2.
	 * $this->order(array('id desc','price asc'));
	 * @param string|array $order
	 * @return $this
	 */
	public function order($order)
	{
		if(empty($order))
		{
			Controller::halt('The order param is empty.');
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

		$this->_params['order'] = !empty($this->_params['order']) ? $this->_params['order'] . ',' . $order : ' ORDER BY ' . $order;

		return $this;
	}

	/**
	 * 组装LIMIT语句
	 *
	 * @param int $offset
	 * @param int $count
	 * @return $this
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
	 * 分页使用的LIMIT语句
	 *
	 * @param int $page
	 * @param int $count
	 * @return $this
	 */
	public function page($page, $count)
	{
		$startId = intval($count) * (intval($page) - 1);

		$this->limit($startId, $count);

		return $this;
	}

	/**
	 * 组装JOIN语句
	 *
	 * @param string $tableName
	 * @param string $where
	 * @param string $join
	 * @return $this
	 */
	public function join($tableName, $where, $join = 'INNER')
	{
		if(empty($tableName) || empty($where))
		{
			Controller::halt('The name of the table or the condition is empty');
		}

		$tableName = (!empty($this->_prefix)) ? $this->_prefix . trim($tableName) : '`' . trim($tableName) . '`';

		//处理条件语句
		$where = trim($where);
		$this->_params['join'] = !empty($this->_params['join']) ? $this->_params['join'] . ' ' . $join . ' JOIN ' . $tableName . ' ON ' . $where : ' ' . $join . ' JOIN ' . $tableName . ' ON ' . $where;

		return $this;
	}

	/**
	 * 组装group语句
	 *
	 * @param string $field
	 * @return $this
	 */
	public function group($field)
	{
		if(empty($field))
		{
			Controller::halt('The SQL statement grouping field does not exist.');
		}

		$this->_params['group'] = !empty($this->_params['group']) ? $this->_params['group'] . ', ' . $field : ' GROUP BY ' . $field;

		return $this;
	}

	/**
	 * 组装SQL语句的having语句
	 *
	 * @param string $where
	 * @param string $cond
	 * @return $this
	 */
	public function having($where, $cond = '')
	{
		if(empty($where))
		{
			Controller::halt("The where param is empty.");
		}

		if(!empty($cond) && is_string($where))
		{
			if(!is_array($cond))
			{
				$parse = func_get_args();
				array_shift($parse);
			}
			$parse = $this->quote($parse);
			$where = vsprintf($where, $parse);
		}

		$this->_params['having'] = !empty($this->_params['having']) ? $this->_params['having'] . ' AND ' . $where : ' WHERE ' . $where;

		return $this;
	}

	/**
	 * 查询所有数据
	 *
	 * @return array
	 */
	public function fetchAll()
	{
		$sqlStr = '';
		//拼装SQL语句
		if(!empty($this->_params))
		{
			foreach($this->_params as $key => $value)
			{
				$sqlStr .= $value;
			}

			//销毁变量
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
	 * 查询单条数据
	 *
	 * @return array
	 */
	public function fetchRow()
	{
		$sqlStr = '';
		//拼装SQL语句
		if(!empty($this->_params))
		{
			foreach($this->_params as $key => $value)
			{
				$sqlStr .= $value;
			}

			//销毁变量
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
	 * 查询单列数据
	 *
	 * @return string
	 */
	public function fetchColumn()
	{
		$sqlStr = '';
		//拼装SQL语句
		if(!empty($this->_params))
		{
			foreach($this->_params as $key => $value)
			{
				$sqlStr .= $value;
			}

			//销毁变量
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
	 * 插入新数据
	 *
	 * @example
	 * $data = array('name'=>'zhangwj', 'age'=>23, 'address'=>'哈尔滨')  //key为数据表字段
	 * $this->insert('user',$data);
	 * @param string $tableName
	 * @param array  $data
	 * @param bool   $insertId
	 * @return mixed
	 */
	public function insert($tableName, array $data, $replace = false, $insertId = true)
	{
		if(empty($tableName))
		{
			Controller::halt('Table name is empty.');
		}

		if(!is_array($data) || empty($data))
		{
			Controller::halt('The data format is not correct.');
		}

		//组装SQL语句
		$fieldsArray = array();
		$valuesArray = array();

		foreach($data as $key => $value)
		{
			$fieldsArray[] = '`' . trim($key) . '`';
			$valuesArray[] = "'" . $this->quote(trim($value)) . "'";
		}

		$fieldString = implode(',', $fieldsArray);
		$valueString = implode(',', $valuesArray);

		$tableName = !empty($this->_prefix) ? $this->_prefix . trim($tableName) : trim($tableName);

		if($replace === true)
		{
			$sqlString = 'REPLACE INTO `' . $tableName . '`(' . $fieldString . ') VALUES (' . $valueString . ')';
		}
		else
		{
			$sqlString = 'INSERT INTO `' . $tableName . '`(' . $fieldString . ') VALUES (' . $valueString . ')';
		}

		//销毁不需要的变量
		unset($fieldString, $valueString);

		$result = $this->master()
					   ->execute($sqlString);

		if($result && $insertId === true)
		{
			return $this->getInsertId();
		}

		return $result;
	}

	/**
	 * 更新数据
	 *
	 * @param string $tableName
	 * @param array  $data
	 * @param string $where
	 * @return object
	 */
	public function update($tableName, $data, $where = '')
	{
		if(empty($tableName))
		{
			Controller::halt('Table name is empty.');
		}

		if(!is_array($data) || empty($data))
		{
			Controller::halt('The data format is not correct.');
		}

		$values = array();
		foreach($data as $key => $value)
		{
			$values[] = '`' . $key . '` = \'' . $this->quote(trim($value)) . '\'';
		}
		$valueStr = implode(',', $values);
		unset($values);

		$tableName = !empty($this->_prefix) ? $this->_prefix . trim($tableName) : trim($tableName);

		if(!empty($where))
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
	 * 删除数据
	 *
	 * @param string $tableName
	 * @param string $where
	 * @return object
	 */
	public function delete($tableName, $where = null)
	{
		if(empty($tableName))
		{
			Controller::halt('Table name is empty.');
		}

		$tableName = !empty($this->_prefix) ? $this->_prefix . trim($tableName) : trim($tableName);

		if(!empty($where))
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
	 * 执行其他类型SQL语句
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

		if($isSelect == true)
		{
			return $lines == true ? $this->slave()
										 ->fetchAll($sql) : $this->slave()
																 ->fetchRow($sql);
		}

		return substr(strtolower($sql), 0, 6) == 'select' ? ($lines == true ? $this->master()
																				   ->fetchAll($sql) : $this->master()
																										   ->fetchRow($sql)) : $this->master()
																																	->execute($sql);
	}

	/**
	 * 获取最新插入的最后ID
	 *
	 * @return int
	 */
	public function getInsertId()
	{
		return $this->master()
					->insertId();
	}

	/**
	 * 字符串的转义 防止SQL注入
	 *
	 * @return string
	 */
	public function quote($value)
	{
		if(empty($value))
		{
			return false;
		}

		//参数是否为数组
		if(is_array($value))
		{
			foreach($value as $key => $string)
			{
				$value[$key] = $this->quote($string);
			}
		}
		else
		{
			//当参数为字符串或字符时
			if(is_string($value))
			{
				$value = $this->master()
							  ->escapeString($value);
			}
		}

		return $value;
	}

	/**
	 * 开启事务
	 *
	 * @return void
	 */
	public function trans()
	{
		$this->master()
			 ->trans();
	}

	/**
	 * 提交事务
	 *
	 * @return void
	 */
	public function commit()
	{
		$this->master()
			 ->commit();
	}

	/**
	 * 回滚事务
	 *
	 * @return void
	 */
	public function rollback()
	{
		$this->master()
			 ->rollback();
	}

	/**
	 * 解析数据库配置
	 * 分析数据库主从信息
	 *
	 * @return array
	 */
	protected function config()
	{
		$params = $this->getConfig();

		var_dump($params);

		if(!is_array($params))
		{
			Controller::halt('Error loading the database configuration.');
		}

		//数据库表前缀
		$this->_prefix = (isset($params['prefix']) && !empty($params['prefix'])) ? trim($params['prefix']) : '';

		//数据库默认编码  默认编码GBK
		$params['charset'] = (isset($params['charset']) && !empty($params['charset'])) ? trim($params['charset']) : 'gbk';

		$configs = array();

		//分析主库连接配置
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

		//分析从库连接配置
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
			//当没有从库连接参数时,开启单例模式
			$this->_singleton = true;
			$configs['slave'][] = $configs['master'];
		}

		unset($params);

		return $configs;
	}

	/**
	 * 实例化主数据库
	 *
	 * @return object
	 */
	protected function master()
	{
		Debug::addMessage("Master connection");

		return $this->_master = $this->factory($this->_config['master'], 0);
	}

	/**
	 * 实例化从数据库
	 *
	 * @return object
	 */
	public function slave()
	{
		if($this->_singleton === true)
		{
			return $this->_master = $this->factory($this->_config['master'], 0);
		}
		Debug::addMessage("Slave connection");

		//获得从数据库配置的索引
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
	 * 获取数据库连接配置
	 *
	 * @return array
	 */
	protected function getConfig()
	{
		return RcPHP::getConfig('database');
	}

	/**
	 * 工厂模式实例化数据库驱动操作
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
					//组合dsn信息
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
					$this->_links[$linkNum] = new Mysql($config);
					break;
				default:
					$this->_links[$linkNum] = new Mysql($config);
			}
		}

		return $this->_links[$linkNum];
	}

	/**
	 * 关闭数据库链接
	 *
	 * @return void
	 */
	public function close()
	{
		$this->master()
			 ->close();
		$this->slave()
			 ->close();
	}

	/**
	 * 析构函数
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->close();
		unset($this->_params);
	}
}