<?php
/**
 * Structure class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class    Structure extends Base
{

	/**
	 * Infomation
	 *
	 * @var array
	 */
	public static $message = array();

	/**
	 * Objects
	 *
	 * @var array
	 */
	public static $_objects = array();

	/**
	 * Create file.
	 *
	 * @param string $fileName
	 * @param string $str
	 * @return void
	 */
	public static function touch($fileName, $str)
	{
		if(!file_exists($fileName))
		{
			if(file_put_contents($fileName, $str))
			{
				self::$message[] = date('Y-m-d H:i:s', time()) . " Create file " . $fileName . " success.";
				Debug::addMessage("Create file " . $fileName . " success.");
			}
		}
	}

	/**
	 * Create dir.
	 *
	 * @param array|string $dirs
	 * @return void
	 */
	public static function mkdir($dirs)
	{
		if(is_array($dirs))
		{
			foreach($dirs as $dir)
			{
				if(!is_dir($dir))
				{
					if(mkdir($dir, 0755))
					{
						self::$message[] = date('Y-m-d H:i:s', time()) . "　Create directory " . $dir . " success.";
						Debug::addMessage("Create directory " . $dir . " success.");
					}
				}
			}
		}
		else
		{
			if(!is_dir($dirs))
			{
				if(mkdir($dirs, 0755))
				{
					self::$message[] = date('Y-m-d H:i:s', time()) . "　Create directory " . $dirs . " success.";
					Debug::addMessage("Create directory " . $dirs . " success.");
				}
			}
		}
	}

	/**
	 * Create runtime dir.
	 *
	 * @return void
	 */
	public static function runtime()
	{
		if(!is_writable(PRO_PATH . 'Runtime'))
		{
			Controller::halt('Do not write the runtime directory');
		}

		$dirs = array(
			PRO_PATH . "Runtime" . DS . "Cache" . DS,
			PRO_PATH . "Runtime" . DS . "Logs" . DS
		);

		self::mkdir($dirs);
	}

	/**
	 * 初始化系统结构
	 *
	 * @return void
	 */
	public static function run()
	{
		self::mkdir(PRO_PATH . 'Runtime' . DS);

		//locked file.
		$runFile = RUNTIME_PATH . md5(APP_PATH);

		if(!file_exists($runFile))
		{
			if(!is_writable(PRO_PATH))
			{
				Controller::halt('Do not write the [' . PRO_PATH . '] directory');
			}

			$remote = '';
			//create remote server file.
			if(defined('REMOTE_PATH'))
			{
				$remote = REMOTE_PATH . DS;
				self::mkdir(PRO_PATH . REMOTE_PATH . DS);
			}

			$sourceDirs = array(
				PRO_PATH . $remote . "Public",
				PRO_PATH . $remote . "Public" . DS . "Upload" . DS,
				PRO_PATH . $remote . "Public" . DS . "Style" . DS,
				PRO_PATH . $remote . "Public" . DS . "Script" . DS,
				PRO_PATH . $remote . "Public" . DS . "Image" . DS,
				PRO_PATH . "Class" . DS,
				PRO_PATH . "Common" . DS,
				PRO_PATH . "Data" . DS
			);

			self::mkdir($sourceDirs);

			// Create app directory.
			self::mkdir(APP_PATH);

			if(!is_writable(APP_PATH))
			{
				Controller::halt('Application [' . APP_PATH . '] cannot write, directory cannot be automatically generated');
			}

			$appDirs = array(
				APP_PATH,
				APP_PATH . "Model" . DS,
				APP_PATH . "Controller" . DS,
				APP_PATH . "View" . DS,
				APP_PATH . "View" . DS . "Layout" . DS
			);

			self::mkdir($appDirs);

			self::buildController();
		}

		self::runtime();

		if(!file_exists($runFile))
		{
			self::touch($runFile, implode("\n", self::$message));
		}
	}

	/**
	 * Write defaulr controller files.
	 *
	 * @return void
	 */
	public static function buildController()
	{
		// Copy default controller file.
		$destFile = CONTROLLER_PATH . 'indexController.class.php';

		if(!file_exists($destFile))
		{
			$content = file_get_contents(RCPHP_PATH . 'sources' . DS . 'html' . DS . 'defaultIndex.php');
			file_put_contents($destFile, $content);
		}
	}

	/**
	 * The singleton pattern returns the instance
	 *
	 * @param string $className
	 * @return object
	 */
	public static function singleton($className)
	{
		if(empty($className))
		{
			return false;
		}

		$key = trim($className);

		if(isset(self::$_objects[$key]))
		{
			return self::$_objects[$key];
		}

		return self::$_objects[$key] = new $className();
	}
}