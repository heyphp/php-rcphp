<?php
/**
 * QQ class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Oauth
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class QQ
{

	/**
	 * QQ帐号体系 Api地址
	 *
	 * @var string
	 */
	private $apiBase = 'https://graph.qq.com/user/';

	/**
	 * 构造方法 QQ配置
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$conf = RcPHP::getConfig("qq");

		if(!empty($conf) && !empty($conf['appKey']) && !empty($conf['appSecret']) && !empty($conf['callback']))
		{
			$this->appKey = $conf['appKey'];
			$this->appSecret = $conf['appSecret'];
			$this->callback = $conf['callback'];
		}
		else
		{
			Controller::halt('QQ configuration error.');
		}
	}

	/**
	 * 生成授权网址
	 *
	 * @param string $callback_url
	 * @param string $scope
	 * @return string
	 */
	public function login_url($callback_url = '', $scope = '', $state = "test")
	{
		if(!empty($callback))
		{
			$this->callback = $callback;
		}

		$params = array(
			'response_type' => $this->responseType,
			'client_id' => $this->appKey,
			'redirect_uri' => $this->callback,
			'state' => md5($state),
			'scope' => $scope
		);

		return 'https://graph.qq.com/oauth2.0/authorize?' . http_build_query($params);
	}

	/**
	 * 获取access_token
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
			'client_secret' => $this->appSecret,
			'redirect_uri' => $this->callback
		);
		$url = 'https://graph.qq.com/oauth2.0/token';

		$result = $this->http($url, http_build_query($params));

		if(!empty($result))
		{

			$result = explode('&', $result);

			$data = array();

			foreach($result as $v)
			{
				$tmp = explode('=', $v);
				$data[$tmp[0]] = $data[$tmp[1]];
			}

			$this->token = $data;

			return $this->token;
		}
		else
		{
			return array();
		}
	}

	/**
	 * 获取登录用户信息
	 *
	 * @return array|mixed
	 */
	public function me()
	{
		return $this->api('user', array());
	}

	/**
	 * 其他Api调用
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
		if(!empty($this->token['access_token']))
		{
			$params['access_token'] = $this->token['access_token'];
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