<?php
/**
 * Check class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Util
 * @since          1.0
 */
namespace RCPHP\Util;

defined('IN_RCPHP') or exit('Access denied');

class Check
{

	/**
	 * 验证Email
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isEmail($str)
	{
		if(empty($str))
		{
			return false;
		}

		return filter_var($str, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * 验证URL
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isUrl($str)
	{
		if(empty($str))
		{
			return false;
		}

		return filter_var($str, FILTER_VALIDATE_URL);
	}

	/**
	 * 验证字符串中是否含有非法字符
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isInvalidStr($str)
	{
		if(empty($str))
		{
			return false;
		}

		return preg_match('#[!#$%^&*(){}~`"\';:?+=<>/\[\]]+#', $str) ? true : false;
	}

	/**
	 * 用正则表达式验证邮证编码
	 *
	 * @param int $num
	 * @return bool
	 */
	public static function isPostNum($num)
	{
		$num = intval($num);

		if(!$num)
		{
			return false;
		}

		return preg_match('#^[1-9][0-9]{5}$#', $num) ? true : false;
	}

	/**
	 * 正则表达式验证身份证号码
	 *
	 * @param integer $num
	 * @return bool
	 */
	public static function isPersonalCard($num)
	{
		if(!$num)
		{
			return false;
		}

		return preg_match('#^[\d]{15}$|^[\d]{18}$#', $num) ? true : false;
	}

	/**
	 * 正则表达式验证IPV4地址
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isIpv4($str)
	{
		if(!$str)
		{
			return false;
		}

		if(!preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $str))
		{
			return false;
		}

		$ipArray = explode('.', $str);

		// 真实的ip地址每个数字不能大于255（0-255）
		return ($ipArray[0] <= 255 && $ipArray[1] <= 255 && $ipArray[2] <= 255 && $ipArray[3] <= 255) ? true : false;
	}

	/**
	 * 正则表达式验证IPV6地址
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isIpv6($str)
	{
		if(empty($str))
		{
			return false;
		}

		return preg_match('#\A (?:
		(?: 
		(?:[a-f0-9]{1,4}:){6} 
		::(?:[a-f0-9]{1,4}:){5}
		(?:[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){4}
		(?:(?:[a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){3}
		(?:(?:[a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){2}
		(?:(?:[a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}: 
		(?:(?:[a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?:: )
		(?: 
		[a-f0-9]{1,4}:[a-f0-9]{1,4} 
		(?:(?:[0-9][1-9][0-9]1[0-9][0-9]2[0-4][0-9]25[0-5])\.){3} 
		(?:[0-9][1-9][0-9]1[0-9][0-9]2[0-4][0-9]25[0-5]) 
		) 
		(?: 
		(?:(?:[a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4} 
		(?:(?:[a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?:: 
		) 
		)\Z#ix', $str) ? true : false;
	}

	/**
	 * 用正则表达式验证出版物的ISBN号
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isBookIsbn($str)
	{
		if(empty($str))
		{
			return false;
		}

		return preg_match('#^978[\d]{10}$|^978-[\d]{10}$#', $str) ? true : false;
	}

	/**
	 * 用正则表达式验证手机号码(中国大陆区)
	 *
	 * @param int $num
	 * @return bool
	 */
	public static function isMobile($num)
	{
		$num = intval($num);

		if(!$num)
		{
			return false;
		}

		return preg_match('#^(13|14|15|18|17)[0-9]{9}$#', $num) ? true : false;
	}

	/**
	 * 用正则表达式验证电话号码(中国大陆区) 匹配形式如 0511-4405222 或 021-87888822
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isTel($str)
	{
		if(empty($str))
		{
			return false;
		}

		return preg_match('#^\d{3}-\d{7,8}|\d{4}-\d{7,8}$#', $str) ? true : false;
	}

	/**
	 * 验证QQ号是否合法
	 *
	 * @param int $num
	 * @return bool
	 */
	public static function isQQ($num)
	{
		$num = intval($num);

		if(!$num)
		{
			return false;
		}

		return preg_match('#^[1-9][0-9]{4,12}$#', $num) ? true : false;
	}

	/**
	 * 验证颜色代码是否合法
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isColorCode($str)
	{
		if(empty($str))
		{
			return false;
		}

		return preg_match('#^\#[0-9a-f]{6}$#i', $str) ? true : false;
	}

	/**
	 * 验证字符串是否是UTF8编码
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isUtf8($str)
	{
		if(empty($str))
		{
			return false;
		}

		return preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $str) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $str) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $str) ? true : false;
	}

	/**
	 * 验证是否为代理
	 *
	 * @return bool
	 */
	public static function isAgent()
	{
		if($_SERVER['HTTP_X_FORWARDED_FOR'] || $_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] || $_SERVER['HTTP_USER_AGENT_VIA'])
		{
			return true;
		}

		return false;
	}

	/**
	 * 验证是否为机器人
	 *
	 * @return bool
	 */
	public static function isRobot()
	{
		if(preg_match("/(Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla)/", $_SERVER['HTTP_USER_AGENT']))
		{
			return true;
		}

		return false;
	}

	/**
	 * 验证是否是ajax提交
	 *
	 * @return bool
	 */
	public static function isAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') || (isset($_SERVER['X-Requested-With']) && $_SERVER['X-Requested-With'] == 'XMLHttpRequest') ? true : false;
	}

	/**
	 * 判断是否是HTTPS
	 *
	 * @return bool
	 */
	public static function isHttps()
	{
		if(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
		{
			return true;
		}
		elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		{
			return true;
		}
		elseif(!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
		{
			return true;
		}

		return false;
	}

	/**
	 * 验证是否是命令行执行
	 *
	 * @return bool
	 */
	public static function isClient()
	{
		return (PHP_SAPI === 'cli' || defined('STDIN'));
	}
}