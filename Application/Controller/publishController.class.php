<?php
/**
 * publish.
 *
 * @author        zhangwj<phperweb@vip.qq.com>
 * @copyright     Copyright (c) 2014,RcPHP Dev Team
 */
defined('IN_RCPHP') or exit('Access denied');

class publishController extends Controller
{

	/**
	 * ģ�����
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * �̳л��๹�췽��
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function __construct()
	{
		parent::__construct();

		F("user", true);

		if(checkLogin() === false)
		{
			$this->redirect("/index.php/account/login");
		}
	}

	/**
	 * ������
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function ask()
	{
		$this->_data['title'] = '��֪ - ��������� - ��ӵ����Ŀ����߼����ʴ�����';

		$this->_data['js_module'] = "ask";

		$this->assign($this->_data)
			 ->display();
	}
}