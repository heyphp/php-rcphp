<?php
/**
 * account.
 *
 * @author        zhangwj<phperweb@vip.qq.com>
 * @copyright     Copyright (c) 2014,RcPHP Dev Team
 */
defined('IN_RCPHP') or exit('Access denied');

class accountModel extends Model
{

	/**
	 * �̳л��๹�췽��
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * �½��û�
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 * @param array $data
	 * @return bool
	 */
	public function newAccount(Array $data)
	{
		if(empty($data))
		{
			return false;
		}

		$this->trans();

		/**
		 * д���û�����
		 */
		$lastId = $this->insert("account", $data);

		/**
		 * д���¼��־����
		 */
		$log = $this->insert("account_login_log", array(
			"uid" => $lastId,
			"dateline" => $data['reg_time']
		));

		if($lastId !== false && $log !== false)
		{
			$this->commit();

			return true;
		}
		else
		{
			$this->rollback();

			return false;
		}
	}
}