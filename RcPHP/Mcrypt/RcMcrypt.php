<?php
/**
 * RcMcrypt class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcMcrypt extends RcBase
{

	/**
	 * ¼ÓÃÜ×Ö·û´®
	 *
	 * @param string $txt
	 * @return string
	 */
	public static function encrypt($txt, $secret_key = '!@#$%^&*()')
	{
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++)
		{
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
		}

		return base64_encode(self::key($tmp, $secret_key));
	}

	/**
	 * ½âÃÜ×Ö·û´®
	 *
	 * @param string $txt
	 * @return string
	 */
	public static function decrypt($txt, $secret_key = '!@#$%^&*()')
	{
		$txt = self::key(base64_decode($txt), $secret_key);
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++)
		{
			$md5 = $txt[$i];
			$tmp .= $txt[++$i] ^ $md5;
		}

		return $tmp;
	}

	/**
	 * ÔËËãkey
	 *
	 * @param string $txt
	 * @param string $encrypt_key
	 * @return string
	 */
	private static function key($txt, $encrypt_key = '!@#$%^&*()')
	{
		$encrypt_key = md5($encrypt_key);
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++)
		{
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}

		return $tmp;
	}
}