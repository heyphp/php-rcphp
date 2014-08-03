<?php
/**
 * Weibo class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Oauth
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Weibo extends Oauth
{

	/**
	 * 微博 Api地址
	 *
	 * @var string
	 */
	private $apiBase = 'https://api.weibo.com/2/';

	/**
	 * 构造方法 Weibo配置
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$conf = RcPHP::getConfig("weibo");

		if(!empty($conf) && !empty($conf['appKey']) && !empty($conf['appSecret']) && !empty($conf['callback']))
		{
			$this->appKey = $conf['appKey'];
			$this->appSecret = $conf['appSecret'];
			$this->callback = $conf['callback'];
		}
		else
		{
			Controller::halt('Weibo configuration error.');
		}
	}

	/**
	 * 生成授权网址
	 *
	 * @param string $callback_url
	 * @param string $scope
	 * @return string
	 */
	public function login_url($callback_url = '', $scope = '', $display = 'default')
	{
		if(!empty($callback))
		{
			$this->callback = $callback;
		}

		$params = array(
			'client_id' => $this->appKey,
			'redirect_uri' => $this->callback,
			'scope' => $scope,
			'state' => md5(time()),
			'display' => $display
		);

		return 'https://api.weibo.com/oauth2/authorize?' . http_build_query($params);
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
		$url = 'https://api.weibo.com/oauth2/access_token';

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
	 * 获取授权用户信息
	 *
	 * @return string
	 */
	public function me()
	{
		return $this->http("https://api.weibo.com/oauth2/get_token_info", array(), "POST");
	}

	/**
	 * 获取授权用户ID
	 *
	 * @return mixed|null
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
