<?php
/**
 * Github class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Oauth
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Github extends Oauth
{

	/**
	 * Github Api地址
	 *
	 * @var string
	 */
	private $apiBase = 'https://api.github.com/';

	/**
	 * 构造方法 Github配置
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$conf = RcPHP::getConfig("github");

		if(!empty($conf) && !empty($conf['appKey']) && !empty($conf['appSecret']) && !empty($conf['callback']))
		{
			$this->appKey = $conf['appKey'];
			$this->appSecret = $conf['appSecret'];
			$this->callback = $conf['callback'];
		}
		else
		{
			Controller::halt('Github configuration error.');
		}
	}

	/**
	 * 生成授权网址
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
			'client_id' => $this->client_id,
			'redirect_uri' => $callback_url,
			'scope' => $scope
		);

		return 'https://github.com/login/oauth/authorize?' . http_build_query($params);
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
			'code' => $code,
			'client_id' => $this->appKey,
			'client_secret' => $this->responseType,
			'redirect_uri' => $this->callback
		);
		$url = 'https://github.com/login/oauth/access_token';

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
	 * 获取登录用户信息
	 *
	 * @return array|mixed
	 */
	public function me()
	{
		return $this->api('user', array());
	}

	/**
	 * 获取登录用户代码仓库
	 *
	 * @return array|mixed
	 */
	public function repos()
	{
		return $this->api('user/repos', array());
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

			return empty($data['id']) ? null : $data['id'];
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