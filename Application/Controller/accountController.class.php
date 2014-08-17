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

	/**
	 * 模板变量
	 *
	 * @var array
	 */
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

	/**
	 * 用户注册
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function register()
	{
		if(Request::post("register-submit") !== false)
		{
			$email = trim(P("email"));
			$password = trim(P("password"));
			$nickname = trim(P("nickname"));
			$code = trim(P("code"));

			$captcha = RcPHP::import("Util/Captcha");

			if($captcha->check($code) === false && false)
			{
				// 检查验证码
				$this->_data['error_message'] = "验证码错误或失效";
			}
			elseif(empty($email) || Check::isEmail($email) === false)
			{
				// 检查邮箱
				$this->_data['error_message'] = "邮箱格式错误";
			}
			elseif(M()->checkEmail($email) !== true)
			{
				// 邮箱唯一性
				$this->_data['error_message'] = "邮箱已被使用";
			}
			elseif(empty($password))
			{
				// 检查密码
				$this->_data['error_message'] = "密码不能为空";
			}
			elseif(empty($nickname))
			{
				// 检查昵称
				$this->_data['error_message'] = "昵称不能为空";
			}
			elseif(M()->checkNickname($nickname) !== true)
			{
				// 检查昵称唯一性
				$this->_data['error_message'] = "昵称已被使用";
			}
			else
			{
				$password = sha1($password);
				$reg_ip = Http::get_ip(true);
				$reg_time = time();

				$result = M()->newAccount(array(
					"email" => $email,
					"nickname" => $nickname,
					"password" => $password,
					"reg_ip" => $reg_ip,
					"reg_time" => $reg_time
				));
			}
		}

		$this->_data['title'] = '认知 - 注册 - 做最专业的技术问答社区';
		$this->assign($this->_data)
			 ->display();
	}

	/**
	 * 获取验证码
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function captcha()
	{
		$captcha = RcPHP::import("Util/Captcha");

		$captcha->setLength(6)
				->setSize(200, 80)
				->doimg();
	}
}