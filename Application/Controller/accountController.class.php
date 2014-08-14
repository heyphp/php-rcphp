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
			elseif(empty($password))
			{
				// �������
				$this->_data['error_message'] = "���벻��Ϊ��";
			}
			elseif(empty($nickname))
			{
				//����ǳ�
				$this->_data['error_message'] = "�ǳƲ���Ϊ��";
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