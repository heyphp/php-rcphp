<?php
/**
 * Http class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Library.Util
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Http
{

	/**
	 * Get client IP.
	 *
	 * @param bool $int
	 * @return string
	 */
	public static function get_ip($int = false)
	{
		if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		{
			$ip = getenv("HTTP_CLIENT_IP");
		}
		else
		{
			if(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
			{
				$ip = getenv("HTTP_X_FORWARDED_FOR");
			}
			else
			{
				if(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
				{
					$ip = getenv("REMOTE_ADDR");
				}
				else
				{
					if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
					{
						$ip = $_SERVER['REMOTE_ADDR'];
					}
					else
					{
						$ip = "unknown";
					}
				}
			}
		}

		if($int === true)
		{
			return dip2long($ip);
		}

		return $ip;
	}

	/**
	 * Check the remote file is exists.
	 *
	 * @param string $url
	 * @return bool
	 */
	public static function remote_file_exists($url)
	{
		if(empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false)
		{
			return false;
		}

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_NOBODY, true);

		$result = curl_exec($curl);

		$found = false;

		if($result !== false)
		{
			//检查http响应码是否为200
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if($statusCode == 200)
			{
				$found = true;
			}
		}
		curl_close($curl);

		return $found;
	}

	/**
	 * Send http status code.
	 *
	 * @param int $code
	 * @return void
	 */
	public static function send_http_status($code)
	{
		static $_status = array(
			// Informational 1xx
			100 => 'Continue',
			101 => 'Switching Protocols',
			// Success 2xx
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			// Redirection 3xx
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily ',
			// 1.1
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			// 306 is deprecated but reserved
			307 => 'Temporary Redirect',
			// Client Error 4xx
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			// Server Error 5xx
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			509 => 'Bandwidth Limit Exceeded'
		);

		if(isset($_status[$code]))
		{
			header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
			// 确保FastCGI模式下正常
			header('Status:' . $code . ' ' . $_status[$code]);
		}
	}
}