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
defined('IN_RCPHP') or exit('Access denied');

class Check
{

	/**
	 * ��֤Email
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
	 * ��֤URL
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
	 * ��֤�ַ������Ƿ��зǷ��ַ�
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
	 * ��������ʽ��֤��֤����
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
	 * ������ʽ��֤���֤����
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
	 * ������ʽ��֤IPV4��ַ
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

		// ��ʵ��ip��ַÿ�����ֲ��ܴ���255��0-255��
		return ($ipArray[0] <= 255 && $ipArray[1] <= 255 && $ipArray[2] <= 255 && $ipArray[3] <= 255) ? true : false;
	}

	/**
	 * ������ʽ��֤IPV6��ַ
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
	 * ��������ʽ��֤�������ISBN��
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
	 * ��������ʽ��֤�ֻ�����(�й���½��)
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

		return preg_match('#^(13|15|18)[0-9]{9}$#', $num) ? true : false;
	}

	/**
	 * ��������ʽ��֤�绰����(�й���½��) ƥ����ʽ�� 0511-4405222 �� 021-87888822
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
	 * ��֤QQ���Ƿ�Ϸ�
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

		return preg_match('#^[1-9][0-9]{4,10}$#', $num) ? true : false;
	}

	/**
	 * ��֤��ɫ�����Ƿ�Ϸ�
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
	 * ��֤�ַ����Ƿ���UTF8����
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
	 * ��֤�Ƿ���ajax�ύ
	 *
	 * @return bool
	 */
	public static function isAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') || (isset($_SERVER['X-Requested-With']) && $_SERVER['X-Requested-With'] == 'XMLHttpRequest') ? true : false;
	}
}