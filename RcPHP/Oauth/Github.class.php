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

class Github
{

	/**
	 * Github Api地址
	 *
	 * @var string
	 */
	private $api_url = 'https://api.github.com/';

	/**
	 * Client ID.
	 *
	 * @var null|string
	 */
	private $client_id = null;

	/**
	 * Client Secret.
	 *
	 * @var null|string
	 */
	private $client_secret = null;

	/**
	 * Access token.
	 *
	 * @var null|string
	 */
	private $access_token = null;

	/**
	 * 构造方法 Github配置
	 *
	 * @return void
	 */
	public function __construct()
	{
		$conf = RcPHP::getConfig("github");

		if(!empty($conf) && !empty($conf['client_id']) && !empty($conf['client_secret']))
		{
			$this->client_id = $conf['client_id'];
			$this->client_secret = $conf['client_secret'];
			empty($conf['access_token']) or $this->access_token = $conf['access_token'];
		}
		else
		{
			Controller::halt('Github configuration error.');
		}
	}

	/**
	 * 授权
	 *
	 * @param string $callback_url
	 * @param string $scope
	 * @return string
	 */
	public function login_url($callback_url, $scope = '')
	{
		if(empty($callback_url))
		{
			Controller::halt("The callback param is null.");
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
	 * @param string $callback_url
	 * @param string $code
	 * @return array
	 */
	public function access_token($callback_url, $code)
	{
		if(empty($callback_url))
		{
			Controller::halt("The callback param is null.");
		}

		if(empty($code))
		{
			Controller::halt("The code param is null.");
		}

		$params = array(
			'code' => $code,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'redirect_uri' => $callback_url
		);
		$url = 'https://github.com/login/oauth/access_token';

		$result = $this->http($url, http_build_query($params), 'POST');

		$json_result = array();
		if(!empty($result))
		{
			parse_str($result, $json_result);
		}

		return $json_result;
	}

	/**
	 * 获取登录用户信息
	 *
	 * @return array|mixed
	 */
	public function me()
	{
		$params = array();

		return $this->api('user', $params);
	}

	/**
	 * 获取登录用户代码仓库
	 *
	 * @return array|mixed
	 */
	public function repos()
	{
		$params = array();

		return $this->api('user/repos', $params);
	}

	/**
	 * 其他Api调用
	 *
	 * @param string $url
	 * @param array  $params
	 * @param string $method
	 * @return array|mixed
	 */
	public function api($url, $params = array(), $method = 'GET')
	{
		if(empty($url))
		{
			Controller::halt("The url param is null.");
		}

		$url = $this->api_url . $url;
		$params['access_token'] = $this->access_token;

		if($method == 'GET')
		{
			$result = $this->http($url . '?' . http_build_query($params));
		}
		else
		{
			$result = $this->http($url, http_build_query($params), 'POST');
		}

		$return = array();

		if(!empty($result))
		{
			$return = json_decode($result, true);
		}

		return $return;
	}

	/**
	 * 提交请求
	 *
	 * @param string $url
	 * @param string $postfields
	 * @param string $method
	 * @param array  $headers
	 * @return mixed
	 */
	private function http($url, $postfields = '', $method = 'GET', $headers = array())
	{
		$ua = "User-Agent: GitHub.PHP(piscdong.com)";

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
}