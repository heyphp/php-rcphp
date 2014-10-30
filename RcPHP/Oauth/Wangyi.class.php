<?php
/**
 * Wangyi class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Oauth
 * @since          1.0
 */
namespace RCPHP\Oauth;

defined('IN_RCPHP') or exit('Access denied');

class Wangyi extends \RCPHP\Oauth\Oauth
{

	/**
	 * �����ʺ���ϵ Api��ַ
	 *
	 * @var string
	 */
	private $apiBase = 'https://api.t.163.com/';

	/**
	 * ���� ��¼��ַ
	 *
	 * @var string
	 */
	protected $loginUrl = 'https://api.t.163.com/oauth2/authorize';

	/**
	 * ���� Token��ַ
	 *
	 * @var string
	 */
	protected $accessTokenUrl = 'https://api.t.163.com/oauth2/access_token';

	/**
	 * ����access_token
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

		if($data['oauth_token'] && $data['oauth_token_secreate'])
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

		$data = $this->api('users/show.json');

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