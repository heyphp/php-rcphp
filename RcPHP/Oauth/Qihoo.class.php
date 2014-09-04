<?php
/**
 * Qihoo class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Oauth
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Qihoo
{

	/**
	 * 360 Api��ַ
	 *
	 * @var string
	 */
	private $apiBase = 'https://openapi.360.cn/';

	/**
	 * ���췽�� 360����
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$conf = RcPHP::getConfig("360");

		if(!empty($conf) && !empty($conf['appKey']) && !empty($conf['appSecret']) && !empty($conf['callback']))
		{
			$this->appKey = $conf['appKey'];
			$this->appSecret = $conf['appSecret'];
			$this->callback = $conf['callback'];
		}
		else
		{
			Controller::halt('360 configuration error.');
		}
	}

	/**
	 * ������Ȩ��ַ
	 *
	 * @param string $callback_url
	 * @param string $scope
	 * @return string
	 */
	public function login_url($callback_url = '', $scope = '')
	{
		if(!empty($callback))
		{
			$this->callback = $callback;
		}

		$params = array(
			'response_type' => $this->responseType,
			'client_id' => $this->appKey,
			'redirect_uri' => $this->callback,
			'scope' => $scope
		);

		return 'https://openapi.360.cn/oauth2/authorize?' . http_build_query($params);
	}

	/**
	 * ��ȡaccess_token
	 *
	 * @param string $code
	 * @return array
	 */
	public function access_token($code)
	{
		if(empty($code))
		{
			Controller::halt("The code param is null.");
		}

		$params = array(
			'grant_type' => $this->grantType,
			'code' => $code,
			'client_id' => $this->appKey,
			'client_secret' => $this->responseType,
			'redirect_uri' => $this->callback
		);
		$url = 'https://openapi.360.cn/oauth2/access_token';

		$result = $this->http($url, http_build_query($params), 'POST');

		if(!empty($result))
		{
			$this->token = $this->parseToken($result);

			return $this->token;
		}
		else
		{
			return array();
		}
	}

	/**
	 * ��ȡ��¼�û���Ϣ
	 *
	 * @return array|mixed
	 */
	public function me()
	{
		return $this->api('user/me', array());
	}

	/**
	 * ��ȡ��¼�û�ID
	 *
	 * @return null|string
	 */
	public function getOpenId()
	{
		if(!empty($this->token['openid']))
		{
			return $this->token['openid'];
		}

		$data = $this->me();

		if(!empty($data))
		{
			return empty($data['id']) ? null : $data['id'];
		}
		else
		{
			return null;
		}
	}

	/**
	 * ����Api����
	 *
	 * @param string $url
	 * @param array  $params
	 * @param string $method
	 * @return array|mixed
	 */
	public function api($url, array $params = array(), $method = 'GET')
	{
		if(empty($url))
		{
			Controller::halt("The url param is null.");
		}

		$url = $this->$apiBase . $url;
		if(!empty($this->token['token']))
		{
			$params['access_token'] = $this->token['token'];
		}
		else
		{
			throw new Exception("The access token is null.");
		}

		$result = $this->http($url, $params, $method);

		if(!empty($result))
		{
			return json_decode($result, true);
		}
		else
		{
			return array();
		}
	}
}