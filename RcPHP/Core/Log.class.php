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
	 * ��������: ����ϵͳ�����޷�ʹ��
	 */
	const FATAL = 'FATAL';
	/**
	 * һ�����: һ���Դ���
	 */
	const ERR = 'ERR';
	/**
	 * �����Դ���: ��Ҫ��������Ĵ���
	 */
	const WARN = 'WARN';
	/**
	 * ���Ƽ��÷����ɷ���
	 */
	const DEPRECATED = 'DEPRECATED';
	/**
	 * ֪ͨ: ����������е��ǻ����������Ĵ���
	 */
	const NOTICE = 'NOTIC';
	/**
	 * ��Ϣ: ���������Ϣ
	 */
	const INFO = 'INFO';
	/**
	 * ����: ������Ϣ
	 */
	const DEBUG = 'DEBUG';
	/**
	 * SQL��SQL��� ע��ֻ�ڵ���ģʽ����ʱ��Ч
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