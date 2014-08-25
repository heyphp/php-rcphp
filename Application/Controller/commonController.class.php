<?php
/**
 * common.
 *
 * @author        zhangwj<phperweb@vip.qq.com>
 * @copyright     Copyright (c) 2014,RcPHP Dev Team
 */
defined('IN_RCPHP') or exit('Access denied');

class commonController extends Controller
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
	 * 检查登录状态
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function checkLogin()
	{
		if(Check::isAjax())
		{
			F("user", true);

			$userInfo = String::auto_charset(checkLogin());

			$userInfo['avatar'] = "http://" . $_SERVER['HTTP_HOST'] . "/Public/Upload/avatar/" . (ceil($userInfo['uid'] / 3000)) . "/" . $userInfo['uid'] . ".jpg";

			$userInfo['error_avatar'] = "http://" . $_SERVER['HTTP_HOST'] . "/Public/Image/Application/avatar-mid-img.png";

			$data = array(
				"code" => 500,
				"data" => false
			);

			if($userInfo !== false)
			{
				$data['code'] = 200;
				$data['data'] = $userInfo;
			}

			unset($userInfo);

			echo json_encode($data);
		}
		exit();
	}
}