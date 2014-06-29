<?php
/**
 * RcStructure class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcStructure extends RcBase
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
				self::$message[] = date('Y-m-d H:i:s', time()) . "　Create file " . $fileName . " success.";
				RcDebug::addMessage("Create file " . $fileName . " success.");
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
						RcDebug::addMessage("Create directory " . $dir . " success.");
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
					RcDebug::addMessage("Create directory " . $dirs . " success.");
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
		if(!is_writable(PRO_PATH . 'runtime'))
		{
			RcController::halt('Do not write the runtime directory');
		}

		$dirs = array(
			PRO_PATH . "runtime/cache/",
			PRO_PATH . "runtime/logs/"
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
		self::mkdir(PRO_PATH . 'runtime/');

		//locked file.
		$appName = explode(DS, APP_PATH);
		$scriptName = explode('/', $_SERVER["SCRIPT_NAME"]);
		$runFile = PRO_PATH . "runtime/" . $appName[count($appName) - 2] . "_" . end($scriptName);

		if(!file_exists($runFile))
		{
			if(!is_writable(PRO_PATH))
			{
				RcController::halt('Do not write the [' . PRO_PATH . '] directory');
			}

			$fileName = PRO_PATH . 'config.inc.php';
			self::touch($fileName, "<?php\n\t//Config file.");

			$remote = '';
			//create remote server file.
			if(defined('REMOTE_PATH'))
			{
				$remote = REMOTE_PATH . "/";
				self::mkdir(PRO_PATH . REMOTE_PATH . "/");
			}

			$sourceDirs = array(
				PRO_PATH . $remote . "public",
				PRO_PATH . $remote . "public/uploads/",
				PRO_PATH . $remote . "public/styles/",
				PRO_PATH . $remote . "public/scripts/",
				PRO_PATH . $remote . "public/images/",
				PRO_PATH . "classes/",
				PRO_PATH . "commons/",
				PRO_PATH . "data/"
			);

			self::mkdir($sourceDirs);

			// Create app directory.
			self::mkdir(APP_PATH);

			if(!is_writable(APP_PATH))
			{
				RcController::halt('Application [' . APP_PATH . '] cannot write, directory cannot be automatically generated');
			}

			$appDirs = array(
				APP_PATH,
				APP_PATH . "models/",
				APP_PATH . "controllers/",
				APP_PATH . "config/",
				APP_PATH . "views/",
				APP_PATH . "views/layout"
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

		if(!$className)
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