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
 * ����RcPHP����ļ�����·��
 */
!defined('RCPHP_PATH') && define('RCPHP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * ����DIRECTORY_SEPARATOR�s��
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * ������Ŀ�ĸ�·��
 */
define("PRO_PATH", dirname(RCPHP_PATH) . '/');

/**
 * ������Ŀcontroller����·��
 */
define('CONTROLLER_PATH', APP_PATH . 'controllers' . DS);

/**
 * ������Ŀmodel����·��
 */
define('MODEL_PATH', APP_PATH . 'models' . DS);

/**
 * ������Ŀview����·��
 */
define('VIEW_PATH', APP_PATH . 'views' . DS);

/**
 * ������Ŀconfig����·��
 */
define('CONFIG_PATH', APP_PATH . 'config' . DS);

/**
 * ������Ŀlogs����·��
 */
!defined('LOG_PATH') && define('LOG_PATH', PRO_PATH . 'runtime' . DS . 'logs' . DS);

/**
 * ������Ŀcache����·��
 */
!defined('CACHE_PATH') && define('CACHE_PATH', PRO_PATH . 'runtime' . DS . 'cache' . DS);

/**
 * �趨Ĭ��Debug���ܿ��� Ĭ��Ϊ�ر�Debug����
 */
!defined("RCPHP_DEBUG") && define("RCPHP_DEBUG", false);

/**
 * �趨Ĭ����־���ܿ��� Ĭ��Ϊ������־����
 */
!defined("RCPHP_LOG") && define("RCPHP_LOG", true);

/**
 * �趨Ĭ�Ͽ�����
 */
!defined('DEFAULT_CONTROLLER') && define('DEFAULT_CONTROLLER', 'index');

/**
 * �趨Ĭ��ִ�ж���
 */
!defined('DEFAULT_ACTION') && define('DEFAULT_ACTION', 'index');

/**
 * ����������ú�����
 */
include_once RCPHP_PATH . 'functions' . DS . 'common.php';

/**
 * ����·�ɽ�����
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