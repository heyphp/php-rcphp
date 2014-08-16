<?php
/**
 * Yac class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Cache
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Yac
{

	/**
	 * ����ʵ��
	 *
	 * @var null|object
	 */
	private $_conn = null;

	/**
	 * ���췽�� �ж�Yacģ���Ƿ����
	 *
	 * @return void
	 */
	public function __construct()
	{
		if(!extension_loaded('yac'))
		{
			RcController::halt('The Yac extension must be loaded.');
		}

		$this->_conn = new Yac();
	}

	/**
	 * д�뻺��
	 *
	 * @param string $key
	 * @param string $data
	 * @param int    $expire
	 * @return bool
	 */
	public function set($key, $value, $expire = null)
	{
		if(empty($key))
		{
			return false;
		}

		return is_null($expire) ? $this->_conn->set($key, $value) : $this->_conn->set($key, $value, $expire);
	}

	/**
	 * ��ȡ��������
	 *
	 * @param string $key
	 * @return string
	 */
	public function get($key)
	{
		if(empty($key))
		{
			return false;
		}

		return $this->_conn->get($key);
	}

	/**
	 * ɾ����������
	 *
	 * @param string|array $key
	 * @return bool
	 */
	public function delete($key)
	{
		if(empty($key))
		{
			return false;
		}

		return $this->_conn->delete($key);
	}

	/**
	 * ������еĻ�������
	 *
	 * @return bool
	 */
	public function clear()
	{
		return $this->_conn->flush();
	}

	/**
	 * ��ȡ������Ϣ
	 *
	 * @return array
	 */
	public function info()
	{
		return $this->_conn->info();
	}

	/**
	 * ��������
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if($this->_conn)
		{
			$this->_conn = null;
		}
	}
}