<?php
/**
 * index.
 *
 * @author        zhangwj<phperweb@vip.qq.com>
 * @copyright     Copyright (c) 2014,RcPHP Dev Team
 */
defined('IN_RCPHP') or exit('Access denied');

class indexController extends Controller
{

	private $_data = array();

	/**
	 * �̳л��๹�췽��
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_data['title'] = '��֪ - ����רҵ�ļ����ʴ�����';
	}

	public function index()
	{
		$this->assign($this->_data)
			 ->display();
	}
}