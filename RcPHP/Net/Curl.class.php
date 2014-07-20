<?php
/**
 * Curl class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Library.Util
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Curl
{

	/**
	 * HTTP头信息
	 *
	 * @var array
	 */
	private $headers = array();

	/**
	 * UA信息
	 *
	 * @var string
	 */
	private $userAgent = '';

	/**
	 * 压缩信息 默认Gzip
	 *
	 * @var string
	 */
	private $compression = 'gzip';

	/**
	 * cookie信息
	 *
	 * @var bool
	 */
	private $cookies = false;

	/**
	 * cookie文件
	 *
	 * @var string
	 */
	private $cookie_file = 'cookies.txt';

	/**
	 * 开启代理模式 默认关闭
	 *
	 * @var bool
	 */
	private $isProxy = false;

	/**
	 * 代理IP
	 *
	 * @var string
	 */
	private $proxy = '199.201.124.49:8080';

	/**
	 * 构造方法  设置基本参数信息
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
		$this->headers[] = 'Connection: Keep-Alive';
		$this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
		$this->userAgent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.' . rand(1, 10) . '.1180.' . rand(10, 99) . ' Safari/537.1';
	}

	/**
	 * Set the HTTP request header
	 *
	 * @param array $header
	 * @return $this
	 */
	public function setHeader($header = array())
	{
		if(!empty($header))
		{
			if(is_array($header))
			{
				$this->headers = $header;
			}
			else
			{
				$this->headers[] = $header;
			}
		}

		return $this;
	}

	/**
	 * Set the browser UA
	 *
	 * @param string $ua
	 * @return $this
	 */
	public function setUserAgent($ua)
	{
		if(!empty($ua))
		{
			$this->userAgent = $ua;

			RcDebug::addMessage("Set curl ua");
		}

		return $this;
	}

	/**
	 * Set the proxy
	 *
	 * @param string $proxy
	 * @return $this
	 */
	public function setProxy($proxy)
	{
		if(!empty($proxy))
		{
			$this->isProxy = true;
			$this->proxy = $proxy;
		}

		return $this;
	}

	/**
	 * 设置cookie
	 *
	 * @param string $cookie_file
	 * @return void
	 */
	public function setCookie($cookie_file)
	{
		if(file_exists($cookie_file))
		{
			$this->cookie_file = $cookie_file;
		}
		else
		{
			fopen($cookie_file, 'w') or RcController::halt('The cookie file could not be opened. Make sure this directory has the correct permissions');
			$this->cookie_file = $cookie_file;
			fclose($this->cookie_file);
		}
	}

	/**
	 * GET方式获取数据
	 *
	 * @param string $url
	 * @param string $ip
	 * @param int    $timeout
	 * @return string
	 */
	public function get($url, $ip = null, $timeout = 10)
	{
		$process = curl_init($url);

		curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($process, CURLOPT_USERAGENT, $this->userAgent);

		curl_setopt($process, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

		if(!is_null($ip))
		{
			curl_setopt($process, CURLOPT_FTPPORT, $ip);
		}

		if($this->cookies == true)
		{
			$this->setCookie($this->cookie_file);
			curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
		}

		if($this->cookies == true)
		{
			$this->setCookie($this->cookie_file);
			curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
		}

		curl_setopt($process, CURLOPT_ENCODING, $this->compression);
		curl_setopt($process, CURLOPT_TIMEOUT, $timeout);

		if($this->isProxy === true)
		{
			curl_setopt($process, CURLOPT_PROXY, $this->proxy);
		}

		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		$return = curl_exec($process);
		curl_close($process);

		return $return;
	}

	/**
	 * POST方式获取数据
	 *
	 * @param string $url
	 * @param array  $data
	 * @param int    $timeout
	 * @param string $ip
	 * @return string
	 */
	public function post($url, $data, $timeout = 10, $ip = null)
	{
		$process = curl_init($url);

		if(is_array($data))
		{
			$data = http_build_query($data);
		}

		// Avoid 411 Length Required
		$this->headers[] = "Content-length:" . strlen($data);

		curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($process, CURLOPT_USERAGENT, $this->userAgent);

		curl_setopt($process, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

		if(!is_null($ip))
		{
			curl_setopt($process, CURLOPT_FTPPORT, $ip);
		}

		if($this->cookies == true)
		{
			$this->setCookie($this->cookie_file);
			curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
		}

		if($this->cookies == true)
		{
			$this->setCookie($this->cookie_file);
			curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
		}

		curl_setopt($process, CURLOPT_ENCODING, $this->compression);
		curl_setopt($process, CURLOPT_TIMEOUT, $timeout);

		if($this->isProxy === true)
		{
			curl_setopt($process, CURLOPT_PROXY, $this->proxy);
		}

		curl_setopt($process, CURLOPT_POSTFIELDS, $data);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($process, CURLOPT_POST, 1);
		$return = curl_exec($process);
		curl_close($process);

		return $return;
	}
}