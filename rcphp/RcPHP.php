<?php
/**
 * RcPHP class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
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
include_once RCPHP_PATH . 'core' . DS . 'RcRoute.php';

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
		self::loadFile(RCPHP_PATH . 'core' . DS . 'RcLoader.php');

		RcLoader::registerAutoloader();

		if(defined('RCPHP_DEBUG') && RCPHP_DEBUG === true)
		{
			error_reporting(E_ALL ^ E_NOTICE);

			//Open script computing time
			RcDebug::start();
			//Set to capture system anomalies
			set_error_handler(array(
				"RcDebug",
				'catcher'
			));
		}
		else
		{
			$error_log_path = PRO_PATH . 'runtime' . DS . 'error_log' . DS;
			RcFile::makeDir($error_log_path);

			ini_set('display_errors', 'Off');
			ini_set('log_errors', 'On');
			ini_set('error_log', $error_log_path . 'error_log_' . date('Y_m_d', time()) . '.log');
		}

		RcStructure::run();

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
			RcDebug::stop();
			RcDebug::output();
		}
	}

	/**
	 * Client Mode
	 *
	 * @return object
	 */
	public static function client()
	{
		self::loadFile(RCPHP_PATH . 'core' . DS . 'RcBase.php');
		self::loadFile(RCPHP_PATH . 'core' . DS . 'RcController.php');
		self::loadFile(RCPHP_PATH . 'core' . DS . 'RcLog.php');
		self::loadFile(RCPHP_PATH . 'core' . DS . 'RcLoader.php');

		RcLoader::registerAutoloader();

		if(defined('RCPHP_DEBUG') && RCPHP_DEBUG === true)
		{
			$GLOBALS["debug"] = 1; // 初例化开启debug
			error_reporting(E_ALL ^ E_NOTICE);

			//Open script computing time
			RcDebug::start();
			//Set to capture system anomalies
			set_error_handler(array(
				"RcDebug",
				'catcher'
			));
		}
		else
		{
			ini_set('display_errors', 'Off');
			ini_set('log_errors', 'On');
			ini_set('error_log', PRO_PATH . 'runtime/error_log.log');
		}

		RcStructure::run();

		//Parsing the client parameters
		$argvs = $_SERVER['argv'];

		if(in_array('--help', $argvs))
		{
			$str = "example" . PHP_EOL;
			$str .= "\tphp index.php -c=test -a=index -category=xiuzhen" . PHP_EOL;
			$str .= "parameters" . PHP_EOL;
			$str .= "\t-c\tController Name example:\$_GET['c']" . PHP_EOL;
			$str .= "\t-a\tAction Name example:\$_GET['a']" . PHP_EOL;
			$str .= "\t-category\tParameter example:\$_GET['category']" . PHP_EOL . PHP_EOL;
			echo $str;
			exit();
		}

		//Control the debug
		if(in_array('--debug', $argvs))
		{
			debug(intval($argvs['--debug']));
		}

		$argvCount = count($argvs);

		if($argvCount > 1)
		{
			foreach($argvs as $key => $argv)
			{
				if($key == 0)
				{
					continue;
				}

				$tmp = explode('=', $argv);
				if(strpos($tmp[0], '--') !== false && $tmp[0] != '--debug')
				{
					echo "Please input -- help for help" . PHP_EOL . PHP_EOL;
					exit();
				}

				$tmp[0] = str_replace('-', '', $tmp[0]);
				$array[$tmp[0]] = $tmp[1];
			}

			$urlParams = $array;

			if(!isset($array['c']))
			{
				$urlParams['c'] = DEFAULT_CONTROLLER;
			}
			if(!isset($array['a']))
			{
				$urlParams['a'] = DEFAULT_ACTION;
			}
		}
		else
		{
			$urlParams['c'] = DEFAULT_CONTROLLER;
			$urlParams['a'] = DEFAULT_ACTION;
		}

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
			RcDebug::stop();
			RcDebug::output();
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
			$filePath = $fileName === 'config' ? PRO_PATH . 'config.inc.php' : CONFIG_PATH . $fileName . '.inc.php';

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