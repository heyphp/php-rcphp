<?php
/**
 * View class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class View extends Base
{

	/**
	 * 单例模式对象
	 *
	 * @var object
	 */
	protected static $_instance;

	/**
	 * 视图布局名称
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * 视图变量数组
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * 视图文件扩展名
	 *
	 * @var string
	 */
	protected $_ext = '.php';

	/**
	 * 构造函数
	 *
	 * @return void
	 */
	protected function __construct()
	{
		// Print debug info.
		Debug::addMessage('View Class Initialized');
	}

	/**
	 * 魔术方法 覆盖__clone()方法，禁止克隆
	 *
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * 分析视图文件
	 *
	 * @param string $fileName
	 * @return string
	 */
	private function parseView($fileName = '')
	{
		if(empty($fileName))
		{
			//当前App下的views目录中
			$fileName = APP_PATH . 'View' . DS . ucfirst(RcPHP::getController()) . DS . RcPHP::getAction();

			$viewFile = $fileName . $this->_ext;
		}
		else
		{
			//当前App下的views目录中
			$fileName = APP_PATH . 'View' . DS . $fileName;

			$viewFile = $fileName . $this->_ext;
		}

		//分析视图文件是否存在
		if(!file_exists($viewFile))
		{
			Controller::halt('The view file:' . $viewFile . ' is not exists!');
		}

		return $viewFile;
	}

	/**
	 * 加载布局文件
	 *
	 * @param string $fileName
	 * @return void
	 */
	public function layout($fileName)
	{
		if(empty($fileName) || !is_string($fileName))
		{
			Controller::halt('The layout file is not empty!');
		}

		extract($this->_data, EXTR_PREFIX_SAME, 'data');

		$this->layout = $fileName;

		$layoutName = APP_PATH . DS . 'View' . DS . 'Layout' . DS . $this->layout . '.php';

		include $layoutName;
	}

	/**
	 * 视图变量赋值操作
	 *
	 * @param string $keys
	 * @param string $value
	 * @return bool
	 */
	public function assign($keys, $value = null)
	{
		if(empty($keys))
		{
			return false;
		}

		//当$keys为数组时
		if(!is_array($keys))
		{
			$this->_data[$keys] = $value;
		}
		else
		{
			foreach($keys as $key => $value)
			{
				$this->_data[$key] = $value;
			}
		}

		return true;
	}

	/**
	 * 显示视图内容
	 *
	 * @param string $fileName
	 * @return void
	 */
	public function display($fileName, $_data = null)
	{
		$viewFile = $this->parseView($fileName);

		//如果变量存在 则调用assign方法
		if(!is_null($_data))
		{
			$this->assign($_data);
		}

		//模板变量解析
		if(!empty($this->_data))
		{
			extract($this->_data, EXTR_PREFIX_SAME, 'data');
		}

		//开启缓冲区
		ob_start();
		include $viewFile;
		$content = ob_get_clean();

		//显示缓冲区中的视图内容
		echo $content;
	}

	/**
	 * 返回视图内容
	 *
	 * @param string $fileName
	 * @param array  $_data
	 * @return string
	 */
	public function fetch($fileName, $_data = array())
	{
		if(empty($fileName))
		{
			Controller::halt('The view file is not empty!');
		}

		$viewFile = $this->parseView($fileName);

		//如果变量存在 则调用assign方法
		if(!is_null($_data))
		{
			$this->assign($_data);
		}

		//模板变量解析
		if(!empty($this->_data))
		{
			extract($this->_data, EXTR_PREFIX_SAME, 'data');
		}

		//开启缓冲区
		ob_start();
		include $viewFile;
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * 析构函数
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if(!empty($this->_data))
		{
			$this->_data = array();
		}
	}

	/**
	 * 单例模式 实例化对象
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		if(!(self::$_instance instanceof self))
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}