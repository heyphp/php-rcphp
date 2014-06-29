<?php
/**
 * RcFile class file.
 *
 * @author         RcPHP Dev Team
 * @version        $Id: RcFile.php 0.2 2013-08-14 23:05 zhangwj $
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcFile extends RcBase
{

	/**
	 * 分析文件夹是否存在
	 * @param string $dirName
	 * @param string $isMake
	 * @return boolean string
	 */
	protected static function parseDir($dirName = null, $isMake = false)
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
				self::makeDir($dirName, 0755);
			}
		}
		else
		{
			if(!is_dir($dirName))
			{
				RcController::halt('The dir ' . $dirName . ' is not exists!');
			}
		}

		return $dirName;
	}

	/**
	 * 生成文件夹
	 * @param string $dirName
	 * @param number $mode
	 * @return boolean
	 */
	public static function makeDir($dirName = null, $mode = 0755)
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
	 * @param string $dirName
	 * @return boolean multitype:string
	 */
	public static function readDir($dirName = null)
	{
		if(is_null($dirName))
		{
			return false;
		}

		$filterArray = array(
			'.csv',
			'.svn',
			'.git'
		);

		$dir = self::parseDir($dirName);

		$handle = opendir($dir);

		$files = array();

		while(false !== ($file = readdir($handle)))
		{
			if($file == '.' || $file == '..' || in_array($file, $filterArray))
			{
				continue;
			}
			$files[] = $file;
		}

		closedir($handle);

		return $files;
	}

	/**
	 * 复制文件夹
	 * @param string $source
	 * @param string $dest
	 * @return boolean
	 */
	public static function copyDir($source, $dest)
	{
		if(empty($source) || empty($dest))
		{
			return false;
		}

		$parseDir = self::parseDir($source);
		$dest = self::parseDir($dest, true);

		$fileList = self::readDir($parseDir);

		foreach($fileList as $file)
		{
			if(is_dir($parseDir . '/' . $file))
			{
				self::copyDir($parseDir . '/' . $file, $dest . '/' . $file);
			}
			else
			{
				copy($parseDir . '/' . $file, $dest . '/' . $file);
			}
		}

		return true;
	}

	/**
	 * 移动文件夹 相当于剪切
	 * @param string $source
	 * @param string $dest
	 * @return boolean
	 */
	public static function moveDir($source, $dest)
	{
		if(empty($source) || empty($dest))
		{
			return false;
		}

		$dir = self::parseDir($source);
		$destDir = self::parseDir($dest, true);

		// 获取所有文件列表
		$fileList = self::readDir($dir);

		foreach($fileList as $file)
		{
			if(is_dir($dir . '/' . $file))
			{
				self::moveDir($dir . '/' . $file, $destDir . '/' . $file);
			}
			else
			{
				if(copy($dir . '/' . $file, $destDir . '/' . $file))
				{
					unlink($dir . '/' . $file);
				}
			}
		}
	}

	/**
	 * 删除文件夹
	 * @param string $dirName
	 * @return boolean
	 */
	public static function deleteDir($dirName)
	{
		if(empty($dirName))
		{
			return false;
		}

		self::clearDir($dirName);

		rmdir($dirName);

		return true;
	}

	/**
	 * 清空文件夹 包含子文件夹
	 * @param string $dirName
	 * @param string $option
	 * @return boolean
	 */
	public static function clearDir($dirName, $option = true)
	{
		if(empty($dirName))
		{
			return false;
		}

		$dir = self::parseDir($dirName);
		$fileList = self::readDir($dir);

		foreach($fileList as $file)
		{
			if(is_dir($dir . '/' . $file))
			{
				self::clearDir($dir . '/' . $file, $option);
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
	 * @param string $fileName
	 * @param string $content
	 * @return boolean number
	 */
	public static function writeFile($fileName, $content = '')
	{
		if(empty($fileName))
		{
			return false;
		}

		self::parseDir(dirname($fileName), true);

		return file_put_contents($fileName, $content, LOCK_EX);
	}

	/**
	 * 拷贝文件
	 * @param string $sourceFile
	 * @param string $destFile
	 * @return boolean
	 */
	public static function copyFile($sourceFile, $destFile)
	{
		if(empty($sourceFile) || empty($destFile))
		{
			return false;
		}

		if(!is_file($sourceFile))
		{
			RcController::halt('The file ' . $sourceFile . ' is not exists!');
		}

		self::parseDir(dirname($destFile), true);

		return copy($sourceFile, $destFile);
	}

	/**
	 * 移动文件（相当于win平台下的Ctrl + X）
	 *
	 * @param string $sourceFile
	 * @param string $destFile
	 * @return boolean
	 */
	public static function moveFile($sourceFile, $destFile)
	{
		if(empty($sourceFile) || empty($destFile))
		{
			return false;
		}

		if(!is_file($sourceFile))
		{
			RcController::halt('The file ' . $sourceFile . ' is not exists!');
		}

		self::parseDir(dirname($destFile), true);

		return rename($sourceFile, $destFile);
	}

	/**
	 * 删除文件
	 * @param string $fileName
	 * @return boolean
	 */
	public static function deleteFile($fileName)
	{
		if(empty($fileName))
		{
			return false;
		}

		if(!is_file($fileName) || file_exists($fileName))
		{
			return true;
		}

		return unlink($fileName);
	}

	/**
	 * 字节格式化
	 * @param string $bytes
	 * @return boolean string
	 */
	public static function formatBytes($bytes = null)
	{
		if(is_null($bytes))
		{
			return false;
		}

		return tosize($bytes);
	}
}