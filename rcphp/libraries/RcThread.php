<?php
/**
 * RcThread class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 * @example
 *                 $thread = load_class('thread','system');
 *                 $thread->addthread('action_log','a');
 *                 $thread->addthread('action_log','b');
 *                 $thread->addthread('action_log','c');
 *                 $thread->runthread();
 *                 function action_log($info) {
 *                 $log = 'log/' . microtime() . '.log';
 *                 $txt = $info . "\r\n\r\n" . 'Set in ' . Date('h:i:s', time()) . (double)microtime() . "\r\n";
 *                 $fp = fopen($log, 'w');
 *                 fwrite($fp, $txt);
 *                 fclose($fp);
 *                 }
 */
defined('IN_RCPHP') or exit('Access denied');

class RcThread extends RcBase
{

	/**
	 * 钩子
	 * @var array
	 */
	private $hooks = array();

	/**
	 * 参数
	 * @var array
	 */
	private $args = array();

	/**
	 * 构造函数
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * 添加进程
	 * @param string $func
	 * @return void
	 */
	public function addthread($func)
	{
		$args = array_slice(func_get_args(), 1);
		$this->hooks[] = $func;
		$this->args[] = $args;

		return true;
	}

	/**
	 * 运行多进程
	 * @return void
	 */
	public function runthread()
	{
		if(isset($_GET['flag']))
		{
			$flag = intval($_GET['flag']);
		}
		if($flag || $flag === 0)
		{
			call_user_func_array($this->hooks[$flag], $this->args[$flag]);
		}
		else
		{
			for($i = 0, $size = count($this->hooks); $i < $size; $i++)
			{
				$fp = fsockopen($_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT']);
				if($fp)
				{
					$out = "GET {$_SERVER['PHP_SELF']}?flag=$i HTTP/1.1\r\n";
					$out .= "Host: {$_SERVER['HTTP_HOST']}\r\n";
					$out .= "Connection: Close\r\n\r\n";
					fputs($fp, $out);
					fclose($fp);
				}
			}
		}
	}
}