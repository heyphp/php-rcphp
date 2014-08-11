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
	 * 继承基类构造方法
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_data['title'] = '认知 - 做最专业的技术问答社区';
	}

	public function index()
	{
		$this->assign($this->_data)
			 ->display();
	}
}