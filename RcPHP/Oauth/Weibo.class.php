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
namespace RCPHP\Oauth;

defined('IN_RCPHP') or exit('Access denied');

class Weibo extends \RCPHP\Oauth\Oauth
{

	/**
	 * 微博 Api地址
	 *
	 * @var string
	 */
	protected $apiBase = 'https://api.weibo.com/2/';

	/**
	 * 微博 登录地址
	 *
	 * @var string
	 */
	protected $loginUrl = 'https://api.weibo.com/oauth2/authorize';

	/**
	 * 微博 Token地址
	 *
	 * @var string
	 */
	protected $accessTokenUrl = 'https://api.weibo.com/oauth2/access_token';

	/**
	 * 解析access_token
	 *
	 * @param array $data
	 * @return mixed|void
	 * @throws \Exception
	 */
	protected function parseToken(array $data)
	{
		if(empty($data))
		{
			throw new \Exception("Param data is null.");
		}

		$data = json_decode($data, true);

		if(!empty($data['access_token']) && !empty($data['expires_in']))
		{
			$this->token = $data;

			$data['openid'] = $this->getOpenId();

			return $data;
		}
		else
		{
			throw new \Exception("Get access token is error,error message is " . $data['error']);
		}
	}

	/**
	 * 获取授权用户ID
	 *
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function getOpenId()
	{
		if(!empty($this->token['openid']))
		{
			return $this->token['openid'];
		}

		$data = $this->api("account/get_uid");

		if(!empty($data) && !empty($data['uid']))
		{
			return (int)$data['uid'];
		}
		else
		{
			throw new \Exception("Get Open ID is error,error message is " . $data['error']);
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
			\RCPHP\Controller::halt("The url param is null.");
		}

		$url = $this->$apiBase . $url;

		$params['access_token'] = $this->token['access_token'];

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
