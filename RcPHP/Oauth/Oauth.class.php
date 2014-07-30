<?php
/**
 * Oauth class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Oauth
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

abstract class Oauth
{

	/**
	 * ����Ӧ��ʱ�����app_key
	 *
	 * @var string
	 */
	protected $appKey = '';

	/**
	 * ����Ӧ��ʱ����� app_secret
	 *
	 * @var string
	 */
	protected $appSecret = '';

	/**
	 * ��Ȩ���� response_type Ŀǰֻ��Ϊcode
	 *
	 * @var string
	 */
	protected $responseType = 'code';

	/**
	 * grant_type Ŀǰֻ��Ϊ authorization_code
	 *
	 * @var string
	 */
	protected $grantType = 'authorization_code';

	/**
	 * ��Ȩ���ȡ����TOKEN��Ϣ
	 *
	 * @var array
	 */
	protected $token = null;

	/**
	 * �ص�ҳ��URL  ����ͨ�������ļ�����
	 *
	 * @var string
	 */
	protected $callback = '';

	/**
	 * ���췽��
	 *
	 * @return bool
	 */
	public function __construct()
	{
		return true;
	}

	/**
	 * ��ȡAccess token.
	 *
	 * @param string $data
	 * @return array
	 * @throws Exception
	 */
	protected function parseToken($data)
	{
		if(empty($data))
		{
			Controller::halt("The data param is null.");
		}

		$data = json_decode($data, true);

		if($data['access_token'] && $data['expires_in'] && $data['refresh_token'])
		{
			$data['openid'] = $this->getOpenId();

			return $data;
		}
		else
		{
			throw new Exception("Get access token is error,error message is " . $data['error']);
		}
	}

	/**
	 * �ύ����
	 *
	 * @param string $url
	 * @param string $postfields
	 * @param string $method
	 * @param array  $headers
	 * @return mixed
	 */
	protected function http($url, $postfields = '', $method = 'GET', $headers = array())
	{
		$ua = "User-Agent: RcPHP.PHP(piscdong.com)";

		if($method == "post")
		{
			return RcPHP::import("Net/Curl")
						->setHeader($headers)
						->setUserAgent($ua)
						->post($url, $postfields);
		}
		else
		{
			return RcPHP::import("Net/Curl")
						->setHeader($headers)
						->setUserAgent($ua)
						->get($url);
		}
	}

	/**
	 * ��ȡ��ǰ��Ȩ�û���Ϣ
	 *
	 * @return mixed
	 */
	abstract public function me();

	/**
	 * ��ȡ��¼�û�ID
	 *
	 * @return mixed
	 */
	abstract public function getOpenId();

	/**
	 * ����API�ĵ���
	 *
	 * @param string $url
	 * @param array  $params
	 * @param string $method
	 * @return mixed
	 */
	abstract public function api($url, array $params = array(), $method = "GET");
}