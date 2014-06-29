<?php
/**
 * RcView class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcView extends RcBase
{

	/**
	 * ����ģʽ����
	 *
	 * @var object
	 */
	protected static $_instance;

	/**
	 * ��ͼ��������
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * ��ͼ��������
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * ��ͼ�ļ���չ��
	 *
	 * @var string
	 */
	protected $_ext = '.php';

	/**
	 * ���캯��
	 *
	 * @return void
	 */
	protected function __construct()
	{
		// Print debug info.
		RcDebug::addMessage('RcView Class Initialized');
	}

	/**
	 * ħ������ ����__clone()��������ֹ��¡
	 *
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * ������ͼ�ļ�
	 *
	 * @param string $fileName
	 * @return void
	 */
	public function parseView($fileName = '')
	{
		if(empty($fileName))
		{
			//��ǰApp�µ�viewsĿ¼��
			$fileName = APP_PATH . 'views' . DS . ucfirst(RcPHP::getController()) . DS . RcPHP::getAction();

			$viewFile = $fileName . $this->_ext;
		}
		else
		{
			//��ǰApp�µ�viewsĿ¼��
			$fileName = APP_PATH . 'views' . DS . $fileName;

			$viewFile = $fileName . $this->_ext;
		}

		//������ͼ�ļ��Ƿ����
		if(!file_exists($viewFile))
		{
			RcController::halt('The view file:' . $viewFile . ' is not exists!');
		}

		return $viewFile;
	}

	/**
	 * ���ز����ļ�
	 *
	 * @param string $fileName
	 */
	public function layout($fileName)
	{
		if(empty($fileName) || !is_string($fileName))
		{
			RcController::halt('The layout file is not empty!');
		}

		extract($this->_data, EXTR_PREFIX_SAME, 'data');

		$this->layout = $fileName;

		$layoutName = APP_PATH . DS . 'views' . DS . 'layout' . DS . $this->layout . '.php';

		include $layoutName;
	}

	/**
	 * ��ͼ������ֵ����
	 *
	 * @param mixted $keys
	 * @param string $value
	 * @return bool
	 */
	public function assign($keys, $value = null)
	{

		if(empty($keys))
		{
			return false;
		}

		//��$keysΪ����ʱ
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
	 * ��ʾ��ͼ����
	 *
	 * @param string $fileName
	 * @return void
	 */
	public function display($fileName, $_data = null)
	{
		$viewFile = $this->parseView($fileName);

		//����������� �����assign����
		if(!is_null($_data))
		{
			$this->assign($_data);
		}

		//ģ���������
		if(!empty($this->_data))
		{
			extract($this->_data, EXTR_PREFIX_SAME, 'data');
		}

		//����������
		ob_start();
		include $viewFile;
		$content = ob_get_clean();

		//��ʾ�������е���ͼ����
		echo $content;
	}

	/**
	 * ������ͼ����
	 *
	 * @param string $fileName
	 * @param array  $_data
	 * @return string
	 */
	public function fetch($fileName, $_data = array())
	{
		if(empty($fileName))
		{
			RcController::halt('The view file is not empty!');
		}

		$viewFile = $this->parseView($fileName);

		//����������� �����assign����
		if(!is_null($_data))
		{
			$this->assign($_data);
		}

		//ģ���������
		if(!empty($this->_data))
		{
			extract($this->_data, EXTR_PREFIX_SAME, 'data');
		}

		//����������
		ob_start();
		include $viewFile;
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * ��������
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
	 * ����ģʽ ʵ��������
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