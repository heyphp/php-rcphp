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
	 * 申请应用时分配的app_key
	 *
	 * @var string
	 */
	protected $appKey = '';

	/**
	 * 申请应用时分配的 app_secret
	 *
	 * @var string
	 */
	protected $appSecret = '';

	/**
	 * 授权类型 response_type 目前只能为code
	 *
	 * @var string
	 */
	protected $responseType = 'code';

	/**
	 * grant_type 目前只能为 authorization_code
	 *
	 * @var string
	 */
	protected $grantType = 'authorization_code';

	/**
	 * 授权后获取到的TOKEN信息
	 *
	 * @var array
	 */
	protected $token = null;

	/**
	 * 回调页面URL  可以通过配置文件配置
	 *
	 * @var string
	 */
	protected $callback = '';

	/**
	 * 构造方法
	 *
	 * @return bool
	 */
	public function __construct()
	{
		return true;
	}

	/**
	 * 获取Access token.
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

		if($data['access_token'] && $data['expires_in'])
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
	 * 提交请求
	 *
	 * @param string $url
	 * @param string $postfields
	 * @param string $method
	 * @param array  $headers
	 * @return mixed
	 */
	protected function http($url, array $params = array(), $method = 'GET', $headers = array())
	{
		$ua = "User-Agent: RcPHP.PHP(piscdong.com)";

		if($method == "post")
		{
			return RcPHP::import("Net/Curl")
						->setHeader($headers)
						->setUserAgent($ua)
						->post($url, $params);
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
	 * 获取当前授权用户信息
	 *
	 * @return mixed
	 */
	abstract public function me();

	/**
	 * 获取登录用户ID
	 *
	 * @return mixed
	 */
	abstract public function getOpenId();

	/**
	 * 其他API的调用
	 *
	 * @param string $url
	 * @param array  $params
	 * @param string $method
	 * @return mixed
	 */
	abstract public function api($url, array $params = array(), $method = "GET");
}