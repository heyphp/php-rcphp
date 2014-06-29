<?php
/**
 * RcLog class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcLog extends RcBase
{

	/**
	 * Write log file.
	 *
	 * @param string $message
	 * @param string $level
	 * @param string $logFileName
	 * @return boolean
	 */
	public static function write($message, $sourceFile, $level = 'Error', $fileName = null)
	{
		if(empty($message))
		{
			return false;
		}

		//Status
		if(!defined('RCPHP_LOG') || (defined('RCPHP_LOG') && RCPHP_LOG == 0))
		{
			return true;
		}

		$fileName = self::getLogFile($fileName);

		$logDir = dirname($fileName);

		if(!is_dir($logDir))
		{
			mkdir($logDir, 0777, true);
		}
		elseif(!is_writable($logDir))
		{
			chmod($logDir, 0777);
		}
		// [error] [client 127.0.0.1] File does not exist: F:/webroot/travel.2345.com/favicon.ico
		error_log(date('[Y-m-d H:i:s]', time()) . " [" . $level . "] [client " . (function_exists('getIp') ? getIp() : '127.0.0.1') . "] " . $message . ": " . $sourceFile . "\r\n", 3, $fileName);
	}

	/**
	 * Get log file name.
	 *
	 * @param string $fileName
	 * @return string
	 */
	protected static function getLogFile($fileName = null)
	{
		return LOG_PATH . date('Y-m') . '/' . (is_null($fileName) ? date('Y-m-d') : $fileName) . '.log';
	}
}