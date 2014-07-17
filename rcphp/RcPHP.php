<?php
/**
 * RcPHP class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

/**
 * Determine the PHP version.
 */
version_compare(PHP_VERSION, '5.3.0', '>') or exit('The wrong version number');

/**
 * Set default timezone.
 */
date_default_timezone_set("Asia/Shanghai");

/**
 * Set RcPHP framework version.
 */
define('RCPHP_VERSION', 1.0);

/**
 * 定义RcPHP框架文件所在路径
 */
!defined('RCPHP_PATH') && define('RCPHP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * 定义DIRECTORY_SEPARATOR縮寫
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * 定义项目的根路径
 */
define("PRO_PATH", dirname(RCPHP_PATH) . '/');

/**
 * 定义项目controller所在路径
 */
define('CONTROLLER_PATH', APP_PATH . 'controllers' . DS);

/**
 * 定义项目model所在路径
 */
define('MODEL_PATH', APP_PATH . 'models' . DS);

/**
 * 定义项目view所在路径
 */
define('VIEW_PATH', APP_PATH . 'views' . DS);

/**
 * 定义项目config所在路径
 */
define('CONFIG_PATH', APP_PATH . 'config' . DS);

/**
 * 定义项目logs所在路径
 */
!defined('LOG_PATH') && define('LOG_PATH', PRO_PATH . 'runtime' . DS . 'logs' . DS);

/**
 * 定义项目cache所在路径
 */
!defined('CACHE_PATH') && define('CACHE_PATH', PRO_PATH . 'runtime' . DS . 'cache' . DS);

/**
 * 设定默认Debug功能开启 默认为关闭Debug功能
 */
!defined("RCPHP_DEBUG") && define("RCPHP_DEBUG", false);

/**
 * 设定默认日志功能开启 默认为开启日志功能
 */
!defined("RCPHP_LOG") && define("RCPHP_LOG", true);

/**
 * 设定默认控制器
 */
!defined('DEFAULT_CONTROLLER') && define('DEFAULT_CONTROLLER', 'index');

/**
 * 设定默认执行动作
 */
!defined('DEFAULT_ACTION') && define('DEFAULT_ACTION', 'index');

/**
 * 包含框架内置函数库
 */
include_once RCPHP_PATH . 'functions' . DS . 'common.php';

/**
 * 包含路由解析类
 */
include_once RCPHP_PATH . 'Core' . DS . 'RcRoute.php';

abstract class RcPHP
{

	/**
	 * Controller Name
	 *
	 * @var string
	 */
	public static $_controller;

	/**
	 * Action name
	 *
	 * @var string
	 */
	public static $_action;

	/**
	 * Include file hash
	 *
	 * @var array
	 */
	public static $_includes = array();

	/**
	 * Object hash
	 *
	 * @var array
	 */
	public static $_objects = array();

	/**
	 * Config hash
	 *
	 * @var array
	 */
	private static $_config = array();

	/**
	 * Create App Process
	 *
	 * @return object
	 */
	public static function run()
	{
		self::loadFile(RCPHP_PATH . 'Core' . DS . 'RcLoader.php');

		RcLoader::registerAutoloader();

		if(defined('RCPHP_DEBUG') && RCPHP_DEBUG === true)
		{
			error_reporting(E_ALL ^ E_NOTICE);

			//Open script computing time
			Debug::start();
			//Set to capture system anomalies
			set_error_handler(array(
				"RcDebug",
				'catcher'
			));
		}
		else
		{
			$error_log_path = PRO_PATH . 'runtime' . DS . 'error_log' . DS;
			File::mkdir($error_log_path);

			ini_set('display_errors', 'Off');
			ini_set('log_errors', 'On');
			ini_set('error_log', $error_log_path . 'error_log_' . date('Y_m_d', time()) . '.log');
		}

		Structure::run();

		$urlParams = RcRoute::parseUrl();

		self::$_controller = $urlParams['c'];
		self::$_action = $urlParams['a'];

		$controller = self::$_controller . 'Controller';
		$action = self::$_action;

		$loadControllerName = CONTROLLER_PATH . $controller . '.class.php';

		self::loadFile($loadControllerName);

		$appObject = new $controller();

		if(method_exists($controller, $action))
		{
			$appObject->$action();
		}
		else
		{
			RcController::halt('The controller method ' . $action . ' does not exist');
		}
		//End time output debugging information.
		if(defined('RCPHP_DEBUG') && RCPHP_DEBUG === true)
		{
			Debug::stop();
			Debug::output();
		}
	}

	/**
	 * Controller Name
	 *
	 * @return string
	 */
	public static function getController()
	{
		return self::$_controller;
	}

	/**
	 * Action Name
	 *
	 * @return string
	 */
	public static function getAction()
	{
		return self::$_action;
	}

	/**
	 * Configuration information
	 *
	 * @param string $fileName
	 * @return array
	 */
	public static function getConfig($fileName)
	{
		if(empty(self::$_config[$fileName]))
		{
			$filePath = CONFIG_PATH . $fileName . '.inc.php';

			if(file_exists($filePath))
			{
				self::$_config[$fileName] = include_once $filePath;
			}
		}

		return !empty(self::$_config[$fileName]) ? self::$_config[$fileName] : false;
	}

	/**
	 * String the version of RcPHP framework.
	 *
	 * @return string
	 */
	public static function getVersion()
	{
		return RCPHP_VERSION;
	}

	/**
	 * Static load file is not repeated loading
	 *
	 * @param string $fileName
	 * @return bool
	 */
	public static function loadFile($fileName = null)
	{
		if(is_null($fileName))
		{
			return false;
		}

		//Judgment document ever loaded, loaded directly returns true
		if(!isset(self::$_includes[$fileName]))
		{

			//load file
			if(file_exists($fileName))
			{
				include_once $fileName;
				self::$_includes[$fileName] = true;
			}
		}

		return !empty(self::$_includes[$fileName]) ? self::$_includes[$fileName] : false;
	}
}