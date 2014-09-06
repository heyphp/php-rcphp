<?php
/**
 * Debug class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 * @filesource
 */
namespace RCPHP;

defined('IN_RCPHP') or exit('Access denied');

class Debug
{

	/**
	 * The execution time.
	 *
	 * @var array
	 */
	public static $time = array();

	/**
	 * Use memory.
	 *
	 * @var array
	 */
	public static $memory = array();

	/**
	 * Include files.
	 *
	 * @var array
	 */
	public static $includeFile = array();

	/**
	 * Debug information.
	 *
	 * @var array
	 */
	public static $info = array();

	/**
	 * The SQL statement.
	 *
	 * @var array
	 */
	public static $sqls = array();

	/**
	 * System
	 *
	 * @var array
	 */
	public static $systems = array();

	/**
	 * Error message corresponding to prompt.
	 *
	 * @var array
	 */
	public static $errMsg = array(
		E_WARNING => 'Warning',
		E_NOTICE => 'Notice',
		E_STRICT => 'Strict',
		E_USER_ERROR => 'User Error',
		E_USER_WARNING => 'User Warning',
		E_USER_NOTICE => 'User Notice',
		'Unkown ' => 'Unkown'
	);

	/**
	 * Start data.
	 *
	 * @return void
	 */
	public static function start()
	{
		self::$time['start'] = microtime(true);
		self::$memory['start'] = memory_get_usage();
	}

	/**
	 * End data.
	 *
	 * @return void
	 */
	public static function stop()
	{
		self::$time['stop'] = microtime(true);
		self::$memory['stop'] = memory_get_usage();
	}

	/**
	 * Calculate the perform data.
	 *
	 * @return number
	 */
	public static function spent()
	{
		$time = round(self::$time['stop'] - self::$time['start'], 4);
		$memory = round(self::$memory['stop'] - self::$memory['start'], 4);

		return array(
			'time' => $time,
			'memory' => $memory
		);
	}

	/**
	 * Capture the error message.
	 *
	 * @param number $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param number $errline
	 * @return void
	 */
	public static function catcher($errno, $errstr, $errfile, $errline)
	{
		$message = '';

		if(!isset(self::$errMsg[$errno])) $errno = 'Unkown';

		$color = 'red';

		if($errno == E_NOTICE || $errno == E_USER_NOTICE)
		{
			$color = "#000088";
		}

		if(defined('RCPHP_WEB') && RCPHP_WEB === true)
		{
			$message = '<font color=' . $color . '>';
			$message .= '<b>' . self::$errMsg[$errno] . "</b>[在文件 {$errfile} 中,第 $errline 行]:";
			$message .= $errstr;
			$message .= '</font>';
		}
		else
		{
			$message .= self::$errMsg[$errno] . "[在文件 {$errfile} 中,第 $errline 行]:";
			$message .= $errstr;
		}

		self::addMessage($message);
	}

	/**
	 * Add the error message.
	 *
	 * @param string $message
	 * @param int    $type
	 */
	public static function addMessage($message, $type = 0)
	{
		//判断是否开启调试
		if((defined('APP_DEBUG') && APP_DEBUG == 1) || (defined('APP_DEBUG') && APP_DEBUG === true))
		{
			switch($type)
			{
				case 0:
					self::$info[] = $message;
					break;
				case 1:
					self::$includeFile[] = $message;
					break;
				case 2:
					self::$sqls[] = $message;
					break;
				case 3:
					self::$systems[] = $message;
					break;
			}
		}
	}

	/**
	 * Output debugging information.
	 *
	 * @return void
	 */
	public static function output()
	{
		if(php_sapi_name() == 'cli')
		{
			self::outputInCmd();
		}
		else
		{
			$efficiency = self::spent();
			$memory = tosize($efficiency['memory']);
			echo '<div id="page_trace" style="bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;text-align:left;font-family:\'微软雅黑\';">';
			echo '<div style="float:left;width:100%;"><span style="float:left;width:400px;"><b>Run time</b> : <font color="red">' . $efficiency['time'] . 's</font>&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<b>Use memory</b> : <font color="red">' . $memory . '</font>';
			echo '</span></div><br>';
			echo '<div id="page_trace_tab" style="display: ;background:white;margin:0;height: 250px;">';
			echo '<div id="page_trace_tab_cont" style="padding: 0; line-height: 24px">';
			echo '<div>';
			echo '<ol style="padding: 0; margin:0">';
			if(count(self::$includeFile) > 0)
			{
				echo '[Autoload files]';
				foreach(self::$includeFile as $file)
				{
					echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . $file . '</li>';
				}
			}
			if(count(self::$info) > 0)
			{
				echo '<br>[System information]';
				foreach(self::$info as $info)
				{
					echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . $info . '</li>';
				}
			}

			if(count(self::$sqls) > 0)
			{
				echo '<br>[SQL statement]';
				foreach(self::$sqls as $sql)
				{
					echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . $sql . '</li>';
				}
			}

			if(count(get_included_files()) > 0)
			{
				echo '<br>[Include files]';
				foreach(get_included_files() as $file)
				{
					echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . $file . '&nbsp;(' . tosize(abs(filesize($file))) . ')</li>';
				}
			}

			if(count(self::$systems) > 0)
			{
				echo '<br>[Trace]';
				foreach(self::$systems as $system)
				{
					echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . $system . '</li>';
				}
			}
			echo '</ol>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
	}
}