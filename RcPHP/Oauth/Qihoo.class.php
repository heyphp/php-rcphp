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
namespace RCPHP\Oauth;

defined('IN_RCPHP') or exit('Access denied');

class Qihoo extends \RCPHP\Oauth\Oauth
{

	/**
	 * 360帐号体系 Api地址
	 *
	 * @var string
	 */
	protected $apiBase = 'https://openapi.360.cn/';

	/**
	 * 360帐号体系 登录地址
	 *
	 * @var string
	 */
	protected $loginUrl = 'https://openapi.360.cn/oauth2/authorize';

	/**
	 * 360帐号体系 Token地址
	 *
	 * @var string
	 */
	protected $accessTokenUrl = 'https://openapi.360.cn/oauth2/access_token';

	/**
	 * 解析access_token
	 *
	 * @param array $data
	 * @return array
	 * @throws \Exception
	 */
	protected function parseToken(array $data)
	{
		if(empty($data))
		{
			throw new \Exception("Param data is null.");
		}

		$data = json_decode($data, true);

		if($data['access_token'] && $data['expires_in'])
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

		$data = $this->api('user/me.json');

		if(!empty($data) && !empty($data['id']))
		{
			return $data['id'];
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