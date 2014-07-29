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

class Baidu
{

	/**
	 * 百度帐号体系 Api地址
	 *
	 * @var string
	 */
	private $api_url = 'https://openapi.baidu.com/rest/2.0/';

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
	 * 构造方法 百度帐号配置
	 *
	 * @return void
	 */
	public function __construct()
	{
		$conf = RcPHP::getConfig("baidu");

		if(!empty($conf) && !empty($conf['client_id']) && !empty($conf['client_secret']))
		{
			$this->client_id = $conf['client_id'];
			$this->client_secret = $conf['client_secret'];
			empty($conf['access_token']) or $this->access_token = $conf['access_token'];
		}
		else
		{
			Controller::halt('Baidu configuration error.');
		}
	}
}