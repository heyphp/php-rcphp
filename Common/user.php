<?php
/**
 * User function.
 *
 * @author        zhangwj<phperweb@vip.qq.com>
 * @copyright     Copyright (c) 2014,RcPHP Dev Team
 */
defined('IN_RCPHP') or exit('Access denied');

/**
 * ����¼
 *
 * @author zhangwj<phperweb@vip.qq.com>
 * @return bool
 */
function checkLogin()
{
	if(Cookie::is_set("I") !== false)
	{
		list($uid, $nickname, $version) = explode(",", Des::decrypt(Cookie::get("I"), APP_SESS_KEY));

		return getUserInfo($uid);
	}

	return false;
}

/**
 * ��ȡ�û���Ϣ
 *
 * @author zhangwj<phperweb@vip.qq.com>
 * @param int $uid
 * @return array|bool
 */
function getUserInfo($uid)
{
	if(empty($uid))
	{
		return false;
	}

	$userSql = "SELECT uid,email,nickname,password,gender,birthday,address,mobile,company,job,homepage,description,reg_time,reg_ip,login_time,login_ip FROM rz_account WHERE uid = " . intval($uid) . " FOR UPDATE";

	$model = new Model();

	return $model->execute($userSql, false);
}