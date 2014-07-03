<?php
/**
 * RcClient class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcClient extends RcBase
{

	/**
	 * 获取客户端系统语言
	 *
	 * @return string
	 */
	public static function getUserLang()
	{
		if(RcRequest::server('HTTP_ACCEPT_LANGUAGE') === false)
		{
			return false;
		}

		return RcRequest::server('HTTP_ACCEPT_LANGUAGE', true);
	}

	/**
	 * 获取当前页面的url来源
	 *
	 * @return string
	 */
	public static function getUrlSource()
	{
		if(RcRequest::server('HTTP_REFERER') === false)
		{
			return false;
		}

		return RcRequest::server('HTTP_REFERER', true);
	}

	/**
	 * 获取客户端浏览器信息.
	 *
	 * @return string
	 */
	public static function getUserAgent()
	{
		if(RcRequest::server('HTTP_USER_AGENT') === false)
		{
			return false;
		}

		return RcRequest::server('HTTP_USER_AGENT', true);
	}

	/**
	 * 获取客户端浏览器和浏览器版本号.
	 *
	 * @return string
	 */
	public static function getUserBrowser()
	{
		if(RcRequest::server('HTTP_USER_AGENT') === false)
		{
			return false;
		}

		$userAgentInfo = RcRequest::server('HTTP_USER_AGENT', true);

		// 全部浏览器代理	 // CriOS == iPhone 的chrome
		$browser = array(
			'Chrome',
			'Firefox',
			'Opera',
			'MSIE',
			'CriOS',
			'Safari'
		);
		$r = array(
			'Unknown',
			0
		);

		// 搜索 浏览器
		if(!preg_match_all('/([0-9a-z]{4,})[\/ ]([0-9\.]+)/i', $userAgentInfo, $arr))
		{
			return implode(' ', $r);
		}

		foreach($browser as $value)
		{
			if(($key = array_search($value, $arr[1])) !== false)
			{
				$r = array(
					$arr[1][$key],
					$arr[2][$key]
				);
				break;
			}
		}

		return implode(' ', $r);
	}
}