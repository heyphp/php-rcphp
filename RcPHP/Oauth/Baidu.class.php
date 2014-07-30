<?php
/**
 * Baidu class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Oauth
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Baidu extends Oauth
{

	/**
	 * 百度帐号体系 Api地址
	 *
	 * @var string
	 */
	private $apiBase = 'https://openapi.baidu.com/rest/2.0/';

	/**
	 * 构造方法 百度帐号配置
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$conf = RcPHP::getConfig("baidu");

		if(!empty($conf) && !empty($conf['appKey']) && !empty($conf['appSecret']) && !empty($conf['callback']))
		{
			$this->appKey = $conf['appKey'];
			$this->appSecret = $conf['appSecret'];
			$this->callback = $conf['callback'];
		}
		else
		{
			Controller::halt('Baidu configuration error.');
		}
	}

	/**
	 * 授权
	 *
	 * @param string $scope
	 * @return string
	 */
	public function login_url($callback = '', $scope = '')
	{
		if(!empty($callback))
		{
			$this->callback = $callback;
		}

		$params = array(
			'client_id' => $this->appKey,
			'response_type' => $this->responseType,
			'redirect_uri' => $this->callback,
			'scope' => $scope,
			'state' => md5(time()),
			'display' => 'page'
		);

		return 'http://openapi.baidu.com/oauth/2.0/authorize?' . http_build_query($params);
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
			'client_secret' => $this->responseType,
			'redirect_uri' => $this->callback
		);
		$url = 'https://openapi.baidu.com/oauth/2.0/token';

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
	 * 使用Refresh Token刷新以获得新的Access Token
	 *
	 * @param $refresh_token
	 * @return array
	 */
	public function access_token_refresh($refresh_token)
	{
		if(empty($refresh_token))
		{
			Controller::halt("The refresh_token param is null.");
		}

		$params = array(
			'grant_type' => 'refresh_token',
			'refresh_token' => $refresh_token,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret
		);
		$url = 'https://openapi.baidu.com/oauth/2.0/token';

		$result = $this->http($url, http_build_query($params), 'POST');

		if(!empty($result))
		{
			return json_decode($result, true);
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
		return $this->api('passport/users/getLoggedInUser', array());
	}

	/**
	 * 获取登录用户ID
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
			$data = json_decode($data, true);

			return empty($data['uid']) ? null : $data['uid'];
		}
		else
		{
			return null;
		}
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
		if(!empty($this->token['token']))
		{
			$params['access_token'] = $this->token['token'];
		}
		else
		{
			throw new Exception("The access token is null.");
		}

		if($method == 'GET')
		{
			$result = $this->http($url . '?' . http_build_query($params));
		}
		else
		{
			$result = $this->http($url, http_build_query($params), 'POST');
		}

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