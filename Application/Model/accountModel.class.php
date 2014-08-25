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
	 * 继承基类构造方法
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 检查邮箱唯一性
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 * @param string $email
	 * @return bool
	 */
	public function checkEmail($email)
	{
		if(empty($email))
		{
			return false;
		}

		$total = $this->from("account", "COUNT(*)")
					  ->where("email = '%s'", $email)
					  ->fetchColumn();

		if($total > 0)
		{
			return false;
		}

		return true;
	}

	/**
	 * 检查昵称唯一性
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 * @param string $nickanme
	 * @return bool
	 */
	public function checkNickname($nickname)
	{
		if(empty($nickname))
		{
			return false;
		}

		$total = $this->from("account", "COUNT(*)")
					  ->where("nickname = '%s'", $nickname)
					  ->fetchColumn();

		if($total > 0)
		{
			return false;
		}

		return true;
	}

	/**
	 * 新建用户
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
		 * 写入用户数据
		 */
		$lastId = $this->insert("account", $data);

		/**
		 * 写入登录日志数据
		 */
		$log = $this->insert("account_login_log", array(
			"uid" => $lastId,
			"dateline" => $data['reg_time']
		));

		if($lastId !== false && $log !== false)
		{
			$this->commit();

			return $lastId;
		}
		else
		{
			$this->rollback();

			return false;
		}
	}

	/**
	 * 检查用户信息
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 * @param string $email
	 * @return array|bool
	 */
	public function checkAccount($email)
	{
		if(empty($email) || Check::isEmail($email) === false)
		{
			return false;
		}

		return $this->from("account", "uid,email,nickname,password")
					->where("email = '%s'", $email)
					->limit(1)
					->fetchRow();
	}
}