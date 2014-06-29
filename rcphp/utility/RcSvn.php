<?php
/**
 * RcSvn class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcSvn extends RcBase
{

	/**
	 * 列出版本库中的目录
	 *
	 * @param string $repository
	 * @return string
	 */
	public static function ls($repository)
	{
		$command = "svn ls " . $repository;
		$output = self::runCmd($command);
		$output = implode("<br>", $output);
		if(strpos($output, 'non-existent in that revision'))
		{
			return false;
		}

		return "<br>" . $command . "<br>" . $output;
	}

	/**
	 * 版本库文件拷贝
	 *
	 * @param string $src
	 * @param string $dst
	 * @param string $comment
	 * @return boolen|string
	 */
	public static function copy($src, $dst, $comment)
	{
		$command = "svn cp " . $src . " " . $dst . " -m '" . $comment . "'";
		$output = self::runCmd($command);
		$output = implode("<br>", $output);
		if(strpos($output, 'Committed revision'))
		{
			return true;
		}

		return "<br>" . $command . "<br>" . $output;
	}

	/**
	 * 删除文件
	 *
	 * @param string $url
	 * @param string $comment
	 * @return boolen|string
	 */
	public static function delete($url, $comment)
	{
		$command = "svn del " . $url . " -m '" . $comment . "'";
		$output = self::runCmd($command);
		$output = implode('<br>', $output);
		if(strpos($output, 'Committed revision'))
		{
			return true;
		}

		return "<br>" . $command . "<br>" . $output;
	}

	/**
	 * 移动/剪切文件
	 *
	 * @param string $src
	 * @param string $dst
	 * @param string $comment
	 * @return boolen|string
	 */
	public static function move($src, $dst, $comment)
	{
		$command = "svn mv " . $src . " " . $dst . " -m '" . $comment . "'";
		$output = self::runCmd($command);
		$output = implode('<br>', $output);
		if(strpos($output, 'Committed revision'))
		{
			return true;
		}

		return "<br>" . $command . "<br>" . $output;
	}

	/**
	 * 创建文件夹
	 *
	 * @param string $url
	 * @param string $comment
	 * @return boolen|string
	 */
	public static function mkdir($url, $comment)
	{
		$command = "svn mkdir " . $url . " -m '" . $comment . "'";
		$output = self::runCmd($command);
		$output = implode('<br>', $output);
		if(strpos($output, 'Committed revision'))
		{
			return true;
		}

		return "<br>" . $command . "<br>" . $output;
	}

	/**
	 * svn文件比对
	 *
	 * @param string $pathA
	 * @param string $pathB
	 * @return string
	 */
	public static function diff($pathA, $pathB)
	{
		$output = self::runCmd("svn diff " . $pathA . " " . $pathB);

		return implode('<br>', $output);
	}

	/**
	 * Checkout repository.
	 *
	 * @param string $url
	 * @param string $dir
	 * @return boolen | string
	 */
	public static function checkout($url, $dir)
	{
		$command = "cd " . $dir . " && svn co " . $url;
		$output = self::runCmd($command);
		$output = implode('<br>', $output);
		if(strstr($output, 'Checked out revision'))
		{
			return true;
		}

		return "<br>" . $command . "<br>" . $output;
	}

	/**
	 * Update.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function update($path)
	{
		$command = "cd " . $path . " && svn up";
		$output = self::runCmd($command);
		$output = implode('<br>', $output);
		preg_match_all("/[0-9]+/", $output, $ret);
		if(!$ret[0][0])
		{
			return "<br>" . $command . "<br>" . $output;
		}

		return $ret[0][0];
	}

	/**
	 * Merge.
	 *
	 * @param int    $revision
	 * @param string $url
	 * @param string $dir
	 * @return string|boolen
	 */
	public static function merge($revision, $url, $dir)
	{
		$command = "cd " . $dir . " && svn merge -r1:" . $revision . " " . $url;
		$output = implode('<br>', self::runCmd($command));
		if(strstr($output, 'Text conflicts'))
		{
			return 'Command: ' . $command . '<br>' . $output;
		}

		return true;
	}

	/**
	 * Commit.
	 *
	 * @param string $dir
	 * @param string $comment
	 * @return boolen|string
	 */
	public static function commit($dir, $comment)
	{
		$command = "cd " . $dir . " && svn commit -m'" . $comment . "'";
		$output = implode('<br>', self::runCmd($command));
		if(strpos($output, 'Committed revision') || empty($output))
		{
			return true;
		}

		return $output;
	}

	/**
	 * 获取目录状态
	 *
	 * @param string $dir
	 * @return mixed
	 */
	public static function getStatus($dir)
	{
		$command = "cd " . $dir . " && svn st";

		return self::runCmd($command);
	}

	/**
	 * 判断冲突
	 *
	 * @param string $dir
	 * @return boolen
	 */
	public static function hasConflict($dir)
	{
		$output = self::getStatus($dir);
		foreach($output as $line)
		{
			if('C' == substr(trim($line), 0, 1) || ('!' == substr(trim($line), 0, 1)))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Show log info.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getLog($path)
	{
		$command = "svn log " . $path . " --xml";
		$output = self::runCmd($command);

		return implode('', $output);
	}

	/**
	 * 获取目录版本号
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getPathRevision($path)
	{
		$command = "svn info " . $path . " --xml";
		$output = self::runCmd($command);
		$string = implode('', $output);
		$xml = new SimpleXMLElement($string);
		foreach($xml->entry[0]->attributes() as $key => $value)
		{
			if('revision' == $key)
			{
				return $value;
			}
		}
	}

	/**
	 * Get last version number.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getHeadRevision($path)
	{
		$command = "cd " . $path . " && svn up";
		$output = self::runCmd($command);
		$output = implode('<br>', $output);
		preg_match_all("/[0-9]+/", $output, $ret);
		if(!$ret[0][0])
		{
			return "<br>" . $command . "<br>" . $output;
		}

		return $ret[0][0];
	}

	/**
	 * Run command.
	 *
	 * @param string $command
	 * @return array
	 */
	protected static function runCmd($command)
	{
		$config = rcphp::getConfig('svn');
		if(empty($config) || empty($config['username']) || empty($config['password']) || empty($config['config']) || $config === false)
		{
			RcController::halt('Svn repository configuration file error.');
		}
		$authCommand = ' --username ' . $config['username'] . ' --password ' . $config['password'] . ' --no-auth-cache --non-interactive --config-dir ' . $config['config'] . '.subversion';
		exec($command . $authCommand . " 2>&1", $output);

		return $output;
	}
}