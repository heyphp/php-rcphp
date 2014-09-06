<?php
/**
 * Log class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
namespace RCPHP;

defined('IN_RCPHP') or exit('Access denied');

class Log
{

	/**
	 * Write log file.
	 *
	 * @param string $message
	 * @param string $level
	 * @param string $logFileName
	 * @return bool
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

		if(!is_writable($logDir))
		{
			chmod($logDir, 0777);
		}

		error_log(date('[Y-m-d H:i:s]', time()) . " [" . $level . "] [client " . (function_exists('getIp') ? getIp() : '127.0.0.1') . "] " . $message . ": " . $sourceFile . "\r\n", 3, $fileName);

		return true;
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