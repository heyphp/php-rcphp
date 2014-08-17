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

	private $_sessid = "00a6c9fb8f5c6d708dde2225b35bec84";

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
	 * ��¼
	 *
	 * @author zhangwj<phperweb@vip.qq.com>
	 */
	public function login()
	{
		$this->_data['title'] = '��֪ - ��¼ - ����רҵ�ļ����ʴ�����';
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
					setcookie("I", "i=" . $result . "&u=" . urlencode($email) . "&n=" . urlencode($nickname) . "&t=" . $reg_time . "&v=1.0", time() + 3600 * 24 * 30, '/', $_SERVER['HTTP_HOST'], false, true);
					setcookie("U", md5("i=" . $result . "&u=" . urlencode($email) . "&n=" . urlencode($nickname) . "&t=" . $reg_time . "&v=1.0" . $this->_sessid), time() + 3600 * 24 * 30, '/', $_SERVER['HTTP_HOST'], false, true);

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
}