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

	/**
	 * 获取客户端操作系统信息
	 * @return string
	 */
	public static function getUserOs()
	{
		if(RcRequest::server('HTTP_USER_AGENT') === false)
		{
			return false;
		}

		$userAgentInfo = RcRequest::server('HTTP_USER_AGENT', true);

		if(strpos($userAgentInfo, 'Windows NT 6.2'))
		{
			return 'Windows 8';
		}
		else if(strpos($userAgentInfo, 'Windows NT 6.1'))
		{
			return 'Windows 7';
		}
		else if(strpos($userAgentInfo, 'Windows NT 6.0'))
		{
			return 'Windows Vista';
		}
		else if(strpos($userAgentInfo, 'Windows NT 5.2'))
		{
			return 'Windows 2003';
		}
		else if(strpos($userAgentInfo, 'Windows NT 5.1'))
		{
			return 'Windows XP';
		}
		else if(strpos($userAgentInfo, 'Windows NT 5.0'))
		{
			return 'Windows 2000';
		}
		else if(strpos($userAgentInfo, 'Windows ME'))
		{
			return 'Windows ME';
		}
		else if(strpos($userAgentInfo, 'PPC Mac OS X'))
		{
			return 'OS X PPC';
		}
		else if(strpos($userAgentInfo, 'Intel Mac OS X'))
		{
			return 'OS X Intel';
		}
		else if(strpos($userAgentInfo, 'Win98'))
		{
			return 'Windows 98';
		}
		else if(strpos($userAgentInfo, 'Win95'))
		{
			return 'Windows 95';
		}
		else if(strpos($userAgentInfo, 'WinNT4.0'))
		{
			return 'Windows NT4.0';
		}
		else if(strpos($userAgentInfo, 'Mac OS X Mach-O'))
		{
			return 'OS X Mach';
		}
		else if(strpos($userAgentInfo, 'Ubuntu'))
		{
			return 'Ubuntu';
		}
		else if(strpos($userAgentInfo, 'Debian'))
		{
			return 'Debian';
		}
		else if(strpos($userAgentInfo, 'AppleWebKit'))
		{
			return 'WebKit';
		}
		else if(strpos($userAgentInfo, 'Mint/8'))
		{
			return 'Mint 8';
		}
		else if(strpos($userAgentInfo, 'Minefield'))
		{
			return 'Minefield Alpha';
		}
		else if(strpos($userAgentInfo, 'gentoo'))
		{
			return 'Gentoo';
		}
		else if(strpos($userAgentInfo, 'Kubuntu'))
		{
			return 'Kubuntu';
		}
		else if(strpos($userAgentInfo, 'Slackware/13.0'))
		{
			return 'Slackware 13';
		}
		else if(strpos($userAgentInfo, 'Fedora'))
		{
			return 'Fedora';
		}
		else if(strpos($userAgentInfo, 'FreeBSD'))
		{
			return 'FreeBSD';
		}
		else if(strpos($userAgentInfo, 'SunOS'))
		{
			return 'SunOS';
		}
		else if(strpos($userAgentInfo, 'OpenBSD'))
		{
			return 'OpenBSD';
		}
		else if(strpos($userAgentInfo, 'NetBSD'))
		{
			return 'NetBSD';
		}
		else if(strpos($userAgentInfo, 'DragonFly'))
		{
			return 'DragonFly';
		}
		else if(strpos($userAgentInfo, 'IRIX'))
		{
			return 'IRIX';
		}
		else if(strpos($userAgentInfo, 'Windows CE'))
		{
			return 'Windows CE';
		}
		else if(strpos($userAgentInfo, 'PalmOS'))
		{
			return 'PalmOS';
		}
		else if(strpos($userAgentInfo, 'Linux'))
		{
			return 'Linux';
		}
		else if(strpos($userAgentInfo, 'DragonFly'))
		{
			return 'DragonFly';
		}
		else if(strpos($userAgentInfo, 'Android'))
		{
			return 'Android';
		}
		else if(strpos($userAgentInfo, 'Mac OS X'))
		{
			return 'Mac OS X';
		}
		else if(strpos($userAgentInfo, 'iPhone'))
		{
			return 'iPhone OS';
		}
		else if(strpos($userAgentInfo, 'Symbian OS'))
		{
			return 'Symbian';
		}
		else if(strpos($userAgentInfo, 'Symbian OS'))
		{
			return 'Symbian';
		}
		else if(strpos($userAgentInfo, 'SymbianOS'))
		{
			return 'SymbianOS';
		}
		else if(strpos($userAgentInfo, 'webOS'))
		{
			return 'webOS';
		}
		else if(strpos($userAgentInfo, 'PalmSource'))
		{
			return 'PalmSource';
		}
		else
		{
			return 'Others';
		}
	}
}