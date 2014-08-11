<?php
/**
 * account.
 *
 * @author        zhangwj<phperweb@vip.qq.com>
 * @copyright     Copyright (c) 2014,RcPHP Dev Team
 */
defined('IN_RCPHP') or exit('Access denied');

class accountController extends Controller
{

	private $_data = array();

	/**
	 * 继承基类构造方法
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 登录
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function login()
	{
		$this->_data['title'] = '认知 - 登录 - 做最专业的技术问答社区';
		$this->assign($this->_data)
			 ->display();
	}
}