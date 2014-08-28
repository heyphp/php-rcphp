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

		F("user", true);

		if(checkLogin() !== false)
		{
			$this->redirect("/");
			exit();
		}
	}

	/**
	 * 登录
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function login()
	{
		if(Request::post('login-submit') !== false)
		{
			// 登录操作
			$email = trim(P("email"));
			$password = trim(P("password"));

			if(empty($email) || Check::isEmail($email) === false)
			{
				// 检查邮箱
				$this->_data['error_message'] = "邮箱格式错误";
			}
			elseif(empty($password))
			{
				// 检查密码
				$this->_data['error_message'] = "密码不能为空";
			}
			else
			{
				$info = M()->checkAccount($email);

				if(empty($info))
				{
					$this->_data['error_message'] = "邮箱不存在";
				}
				elseif($info['password'] != sha1($password))
				{
					$this->_data['error_message'] = "邮箱或密码错误";
				}
				else
				{
					setcookie("I", Des::encrypt($info['uid'] . "," . $info['nickname'] . "," . APP_VERSION, APP_SESS_KEY), time() + 3600 * 24 * 30, '/', $_SERVER['HTTP_HOST'], false, true);

					if(!empty($_GET['forward']))
					{
						$this->redirect(trim($_GET['forward']));
					}
					else
					{
						$this->redirect("/");
					}
				}
			}
		}

		$this->_data['title'] = '认知 - 登录 - 最接地气的开发者技术问答社区';
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

				if($result !== false)
				{
					setcookie("I", Des::encrypt($result . "," . $nickname . "," . APP_VERSION, APP_SESS_KEY), time() + 3600 * 24 * 30, '/', $_SERVER['HTTP_HOST'], false, true);

					if(!empty($_GET['forward']))
					{
						$this->redirect(trim($_GET['forward']));
					}
					else
					{
						$this->redirect("/");
					}
				}
				else
				{
					// 注册
					$this->_data['error_message'] = "注册失败";
				}
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

	/**
	 * 退出
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function logout()
	{
		Cookie::delete("I");
		if(Request::get("forward") !== false)
		{
			$this->redirect(G("forward"));
		}
		else
		{
			$this->register("/");
		}
	}
}