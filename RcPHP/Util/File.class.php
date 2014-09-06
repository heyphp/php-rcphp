<?php
/**
 * File class file.
 *
 * @author         RcPHP Dev Team
 * @version        $Id: File.class.php 0.2 2013-08-14 23:05 zhangwj $
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Util
 * @since          1.0
 */
namespace RCPHP\Util;

defined('IN_RCPHP') or exit('Access denied');

class File
{

	/**
	 * 分析文件夹是否存在
	 *
	 * @param string $dirName
	 * @param string $isMake
	 * @return boolean string
	 */
	private static function parse($dirName = null, $isMake = false)
	{
		if(is_null($dirName))
		{
			return false;
		}

		if($isMake === true)
		{
			if(is_dir($dirName))
			{
				if(!is_writable($dirName))
				{
					@chmod($dirName, 0755);
				}
			}
			else
			{
				self::mkdir($dirName, 0755);
			}
		}
		else
		{
			if(!is_dir($dirName))
			{
				Controller::halt('The dir ' . $dirName . ' is not exists!');
			}
		}

		return $dirName;
	}

	/**
	 * 生成文件夹
	 *
	 * @param string $dirName
	 * @param number $mode
	 * @return boolean
	 */
	public static function mkdir($dirName = null, $mode = 0755)
	{
		if(is_null($dirName))
		{
			return false;
		}

		if(is_dir($dirName))
		{
			return true;
		}

		mkdir($dirName, $mode, true);

		return true;
	}

	/**
	 * 读取文件夹文件
	 *
	 * @param string       $dirName
	 * @param string|array $ignore
	 * @return array
	 */
	public static function read($dirName, $ignore = '')
	{
		$ignoreArray = array(
			'.csv',
			'.svn',
			'.git'
		);

		if(!empty($ignore))
		{
			if(is_string($ignore))
			{
				$ignoreArray[] = $ignore;
			}

			if(is_array($ignore))
			{
				$ignoreArray = array_merge($ignoreArray, $ignore);
			}
		}

		$dir = self::parse($dirName);

		$handle = opendir($dir);

		$files = array();

		while(false !== ($file = readdir($handle)))
		{
			if($file == '.' || $file == '..' || in_array($file, $ignoreArray))
			{
				continue;
			}
			$files[] = $file;
		}

		closedir($handle);

		return $files;
	}

	/**
	 * 复制
	 *
	 * @param string $source
	 * @param string $dest
	 * @return bool
	 */
	public static function copy($source, $dest)
	{
		if(is_file($source))
		{
			self::parse(dirname($dest), true);

			return copy($source, $dest);
		}

		if(is_dir($source))
		{
			$parseDir = self::parse($source);

			$dest = self::parse($dest, true);

			$files = self::read($parseDir);

			foreach($files as $file)
			{
				if(is_dir($parseDir . '/' . $file))
				{
					self::copy($parseDir . '/' . $file, $dest . '/' . $file);
				}
				else
				{
					copy($parseDir . '/' . $file, $dest . '/' . $file);
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * 移动
	 *
	 * @param string $source
	 * @param string $dest
	 * @return bool
	 */
	public static function move($source, $dest)
	{
		if(is_file($source))
		{
			self::parse(dirname($source), true);

			return rename($source, $dest);
		}

		if(is_dir($source))
		{
			$dir = self::parse($source);

			$destDir = self::parse($dest, true);

			// 获取所有文件列表
			$files = self::read($dir);

			foreach($files as $file)
			{
				if(is_dir($dir . '/' . $file))
				{
					self::move($dir . '/' . $file, $destDir . '/' . $file);
				}
				else
				{
					if(copy($dir . '/' . $file, $destDir . '/' . $file))
					{
						unlink($dir . '/' . $file);
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * 删除
	 *
	 * @param string $fileName
	 * @return bool
	 */
	public static function delete($fileName)
	{
		if(is_file($fileName))
		{
			if(!file_exists($fileName))
			{
				return true;
			}

			return unlink($fileName);
		}

		if(is_dir($fileName))
		{
			if(empty($dirName))
			{
				return false;
			}

			self::clear($dirName);

			rmdir($dirName);

			return true;
		}

		return false;
	}

	/**
	 * 清空文件夹
	 *
	 * @param string $dirName
	 * @param string $option
	 * @return bool
	 */
	public static function clear($dirName, $option = true)
	{
		if(empty($dirName))
		{
			return false;
		}

		$dir = self::parse($dirName);
		$files = self::read($dir);

		foreach($files as $file)
		{
			if(is_dir($dir . '/' . $file))
			{
				self::clear($dir . '/' . $file, $option);
				if($option)
				{
					rmdir($dir . '/' . $file);
				}
			}
			else
			{
				unlink($dir . '/' . $file);
			}
		}

		return true;
	}

	/**
	 * 写文件
	 *
	 * @param string $fileName
	 * @param string $content
	 * @param bool   $lock
	 * @return bool
	 */
	public static function write($fileName, $content = '', $lock = true)
	{
		self::parse(dirname($fileName), true);

		if($lock === true)
		{
			return file_put_contents($fileName, $content, LOCK_EX);
		}
		else
		{
			return file_put_contents($fileName, $content);
		}
	}
}