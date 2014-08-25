<?php
/**
 * User function.
 *
 * @author        zhangwj<phperweb@vip.qq.com>
 * @copyright     Copyright (c) 2014,RcPHP Dev Team
 */
defined('IN_RCPHP') or exit('Access denied');

/**
 * 检查登录
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
 * 获取用户信息
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

	$userSql = "SELECT uid,email,nickname,password,exp,reg_time,reg_ip FROM rz_account WHERE uid = " . intval($uid) . " FOR UPDATE";

	$model = new Model();

	return $model->execute($userSql, false);
}