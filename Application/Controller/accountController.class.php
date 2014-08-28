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
	 * ģ�����
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * �̳л��๹�췽��
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
	 * ��¼
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function login()
	{
		if(Request::post('login-submit') !== false)
		{
			// ��¼����
			$email = trim(P("email"));
			$password = trim(P("password"));

			if(empty($email) || Check::isEmail($email) === false)
			{
				// �������
				$this->_data['error_message'] = "�����ʽ����";
			}
			elseif(empty($password))
			{
				// �������
				$this->_data['error_message'] = "���벻��Ϊ��";
			}
			else
			{
				$info = M()->checkAccount($email);

				if(empty($info))
				{
					$this->_data['error_message'] = "���䲻����";
				}
				elseif($info['password'] != sha1($password))
				{
					$this->_data['error_message'] = "������������";
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

		$this->_data['title'] = '��֪ - ��¼ - ��ӵ����Ŀ����߼����ʴ�����';
		$this->assign($this->_data)
			 ->display();
	}

	/**
	 * �û�ע��
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
				// �����֤��
				$this->_data['error_message'] = "��֤������ʧЧ";
			}
			elseif(empty($email) || Check::isEmail($email) === false)
			{
				// �������
				$this->_data['error_message'] = "�����ʽ����";
			}
			elseif(M()->checkEmail($email) !== true)
			{
				// ����Ψһ��
				$this->_data['error_message'] = "�����ѱ�ʹ��";
			}
			elseif(empty($password))
			{
				// �������
				$this->_data['error_message'] = "���벻��Ϊ��";
			}
			elseif(empty($nickname))
			{
				// ����ǳ�
				$this->_data['error_message'] = "�ǳƲ���Ϊ��";
			}
			elseif(M()->checkNickname($nickname) !== true)
			{
				// ����ǳ�Ψһ��
				$this->_data['error_message'] = "�ǳ��ѱ�ʹ��";
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
					// ע��
					$this->_data['error_message'] = "ע��ʧ��";
				}
			}
		}

		$this->_data['title'] = '��֪ - ע�� - ����רҵ�ļ����ʴ�����';
		$this->assign($this->_data)
			 ->display();
	}

	/**
	 * ��ȡ��֤��
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
	 * �˳�
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