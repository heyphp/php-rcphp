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
namespace RCPHP;

defined('IN_RCPHP') or exit('Access denied');

class View
{

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
	public function __construct()
	{
		// Print debug info.
		\RCPHP\Debug::addMessage('View Class Initialized');
	}

	/**
	 * ������ͼ�ļ�
	 *
	 * @param string $fileName
	 * @return string
	 */
	private function parseView($fileName = '')
	{
		if(empty($fileName))
		{
			//��ǰApp�µ�viewsĿ¼��
			$fileName = APP_PATH . 'View' . DS . ucfirst(RcPHP::getController()) . DS . RcPHP::getAction();

			$viewFile = $fileName . $this->_ext;
		}
		else
		{
			//��ǰApp�µ�viewsĿ¼��
			$fileName = APP_PATH . 'View' . DS . $fileName;

			$viewFile = $fileName . $this->_ext;
		}

		//������ͼ�ļ��Ƿ����
		if(!file_exists($viewFile))
		{
			Controller::halt('The view file:' . $viewFile . ' is not exists!');
		}

		return $viewFile;
	}

	/**
	 * ���ز����ļ�
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
	 * ��ͼ������ֵ����
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
			Controller::halt('The view file is not empty!');
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

		return ob_get_clean();
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
}