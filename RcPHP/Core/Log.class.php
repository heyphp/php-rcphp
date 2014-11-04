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
	 * 致命错误: 导致系统崩溃无法使用
	 */
	const FATAL = 'FATAL';
	/**
	 * 一般错误: 一般性错误
	 */
	const ERR = 'ERR';
	/**
	 * 警告性错误: 需要发出警告的错误
	 */
	const WARN = 'WARN';
	/**
	 * 不推荐用法：旧方法
	 */
	const DEPRECATED = 'DEPRECATED';
	/**
	 * 通知: 程序可以运行但是还不够完美的错误
	 */
	const NOTICE = 'NOTIC';
	/**
	 * 信息: 程序输出信息
	 */
	const INFO = 'INFO';
	/**
	 * 调试: 调试信息
	 */
	const DEBUG = 'DEBUG';
	/**
	 * SQL：SQL语句 注意只在调试模式开启时有效
	 */
	const SQL = 'SQL';

	/**
	 * Write log file.
	 *
	 * @param string $message
	 * @param string $level
	 * @param string $logFileName
	 * @return bool
	 */
	public static function write($message, $level = self::ERR, $fileName = null)
	{
		if(defined('RCPHP_LOG') || RCPHP_LOG == 1)
		{
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

			error_log(date('[Y-m-d H:i:s]') . " [" . $level . "] [client " . $_SERVER['REMOTE_ADDR'] . "] " . $message . ": " . $_SERVER['REQUEST_URI'] . "\r\n", 3, $fileName);
		}
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