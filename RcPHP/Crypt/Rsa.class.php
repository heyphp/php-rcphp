<?php
/**
 * Rsa class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Crypt
 * @since          1.0
 */
namespace RCPHP\Crypt;

defined('IN_RCPHP') or exit('Access denied');

class Rsa
{

	/**
	 * ¼ÓÃÜ×Ö·û´®
	 *
	 * @param string $str
	 * @param string $key
	 * @param int    $expire
	 * @return string
	 */
	public static function encrypt($str, $key, $expire = 60)
	{
		$expire = sprintf('%010d', !empty($expire) ? $expire + time() : 0);
		$str = $expire . $str;

		if(file_exists($key))
		{
			$key = file_get_contents($key);
		}

		$resource = openssl_pkey_get_private($key);

		if($resource === false)
		{
			\RCPHP\Controller::halt("The private key is not available.");
		}
		$encryptString = '';

		$resource = openssl_private_encrypt($str, $encryptString, $key);

		if($resource === false)
		{
			\RCPHP\Controller::halt("Cryptographic failure.");
		}

		return base64_encode($encryptString);
	}

	/**
	 * ½âÃÜ×Ö·û´®
	 *
	 * @param string $str
	 * @param string $key
	 * @return string
	 */
	public static function decrypt($str, $key)
	{
		if(file_exists($key))
		{
			$key = file_get_contents($key);
		}

		$resource = openssl_pkey_get_public($key);

		if($resource === false)
		{
			\RCPHP\Controller::halt("The public key is not available.");
		}

		$decryptString = '';

		$resource = openssl_public_decrypt(base64_decode($str), $decryptString, $key);

		if($resource === false)
		{
			\RCPHP\Controller::halt("Decryption failure.");
		}

		$expire = substr($decryptString, 0, 10);

		if($expire > 0 && $expire < time())
		{
			return '';
		}

		$decryptString = substr($decryptString, 10);

		return $decryptString;
	}
}