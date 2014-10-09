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
namespace RCPHP\Oauth;

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
	 * 额外参数
	 *
	 * @var array
	 */
	protected $param = array();

	/**
	 * Oauth driver
	 *
	 * @var string
	 */
	protected $driver = '';

	/**
	 * 构造方法
	 *
	 * @return bool
	 */
	public function __construct($token = null)
	{
		$this->driver = ucfirst(strtolower(get_class($this)));

		$conf = \RCPHP\RcPHP::getConfig($this->driver);

		if(!empty($conf) && !empty($conf['appKey']) && !empty($conf['appSecret']) && !empty($conf['callback']))
		{
			$this->appKey = $conf['appKey'];
			$this->appSecret = $conf['appSecret'];
			$this->callback = $conf['callback'];
			$this->token = $token;
		}
		else
		{
			\RCPHP\Controller::halt($this->driver . ' configuration error.');
		}
	}

	/**
	 * 实例化OAUTH驱动
	 *
	 * @param string $driver
	 * @param null   $token
	 * @return void
	 */
	public static function getInstance($driver, $token = null)
	{
		$className = ucfirst(strtolower($driver));

		$fileName = RCPHP_PATH . 'Oauth' . DS . $className . '.class.php';

		if(file_exists($fileName))
		{
			require_once $fileName;

			$class = "\\RCPHP\\Oauth\\" . $className;

			if(class_exists($class))
			{
				return new $class($token);
			}
			else
			{
				\RCPHP\Controller::halt("Instantiating " . $className . " class does not exist.");
			}
		}
		else
		{
			\RCPHP\Controller::halt("Instantiating " . $className . " class does not exist.");
		}
	}

	/**
	 * 获取配置
	 *
	 * @throws \Exception
	 */
	private function getConfig()
	{
		$conf = \RCPHP\RcPHP::getConfig($this->driver);

		if(!empty($conf['param']))
		{
			$this->param = $conf['param'];
		}

		if(!empty($conf['callback']))
		{
			$this->callback = $conf['callback'];
		}
		else
		{
			throw new \Exception("Callback url is null.");
		}
	}

	/**
	 * 获取登录跳转地址
	 *
	 * @param string $callback
	 * @return string
	 */
	public function login_url($callback = '')
	{
		$this->getConfig();

		if(!empty($callback))
		{
			$this->callback = $callback;
		}

		$params = array(
			'client_id' => $this->appKey,
			'response_type' => $this->responseType,
			'redirect_uri' => $this->callback
		);

		if(empty($this->param) && is_array($this->param))
		{
			$params = array_merge($params, $this->param);
		}

		return $this->loginUrl . '?' . http_build_query($params);
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
			\RCPHP\Controller::halt("The code param is null.");
		}

		$this->getConfig();

		$params = array(
			'grant_type' => $this->grantType,
			'code' => $code,
			'client_id' => $this->appKey,
			'client_secret' => $this->responseType,
			'redirect_uri' => $this->callback
		);

		$result = $this->http($this->accessTokenUrl, http_build_query($params), 'POST');

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
	 * 仅部分支持
	 *
	 * @param string $refresh_token
	 * @return array
	 */
	public function access_token_refresh($refresh_token)
	{
		if(empty($refresh_token))
		{
			\RCPHP\Controller::halt("The refresh_token param is null.");
		}

		$params = array(
			'grant_type' => 'refresh_token',
			'refresh_token' => $refresh_token,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret
		);

		$result = $this->http($this->accessTokenUrl, http_build_query($params), 'POST');

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
	 * 提交请求
	 *
	 * @param string $url
	 * @param string $params
	 * @param string $method
	 * @param array  $headers
	 * @return mixed
	 */
	protected function http($url, array $params = array(), $method = 'GET', $headers = array())
	{
		$ua = "User-Agent: RcPHP.PHP(piscdong.com)";

		if($method == "post")
		{
			return \RCPHP\RcPHP::instance('\RCPHP\Net\Curl')
							   ->setHeader($headers)
							   ->setUserAgent($ua)
							   ->post($url, $params);
		}
		else
		{
			return \RCPHP\RcPHP::instance('\RCPHP\Net\Curl')
							   ->setHeader($headers)
							   ->setUserAgent($ua)
							   ->get($url);
		}
	}

	/**
	 * @param array $data
	 * @return mixed
	 */
	abstract protected function parseToken(array $data);

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