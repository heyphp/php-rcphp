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
namespace RCPHP;

defined('IN_RCPHP') or exit('Access denied');

class RcPHP
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
	 * Function method
	 *
	 * @var array
	 */
	public static $_params = array();

	/**
	 * Include file hash
	 *
	 * @var array
	 */
	public static $_includes = array();

	/**
	 * Config hash
	 *
	 * @var array
	 */
	private static $_config = array();

	/**
	 * Object hash
	 *
	 * @var array
	 */
	private static $_instance = array();

	/**
	 * Create App Process
	 *
	 * @return object
	 */
	public static function run()
	{
		if(!is_php("5.4"))
		{
			ini_set('magic_quotes_runtime', 0);
		}

		self::loadFile(RCPHP_PATH . 'Core' . DS . 'Loader.class.php');

		\RCPHP\Loader::registerAutoloader();

		if(defined('APP_DEBUG') && APP_DEBUG === true)
		{
			error_reporting(E_ALL ^ E_NOTICE);

			//Open script computing time
			\RCPHP\Debug::start();
			//Set to capture system anomalies
			set_error_handler('RCPHP\Debug::catcher');
		}
		else
		{
			$errorLog = RUNTIME_PATH . 'System' . DS;
			\RCPHP\Util\File::mkdir($errorLog);

			ini_set('display_errors', 'Off');
			ini_set('log_errors', 'On');
			ini_set('error_log', $errorLog . date('Ymd', time()) . '.log');
		}

		\RCPHP\Structure::run();

		\RCPHP\Route::dispatch();

		$controller_file = CONTROLLER_PATH . self::$_controller . 'Controller.class.php';

		if(file_exists($controller_file))
		{
			self::loadFile($controller_file);
		}
		else
		{
			\RCPHP\Controller::halt("The controller file does not exist");
		}

		$controller = self::$_controller . "Controller";

		$appObject = new $controller();

		if(method_exists($controller, self::$_action))
		{
			$action = self::$_action;

			$appObject->$action(self::$_params);
			unset($action);
		}
		else
		{
			\RCPHP\Controller::halt('The controller method ' . self::$_action . ' does not exist');
		}

		//End time output debugging information.
		if(defined('APP_DEBUG') && APP_DEBUG === true)
		{
			\RCPHP\Debug::stop();
			\RCPHP\Debug::output();
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
	 * Request method.
	 *
	 * @return string
	 */
	public static function getMethod()
	{
		if(\RcPHP\Util\Check::isAjax())
		{
			return "Ajax";
		}

		return $_SERVER['REQUEST_METHOD'];
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
			$filePath = CONF_PATH . $fileName . '.php';

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
	 * The singleton pattern returns the instance
	 *
	 * @param string $class
	 * @param string $method
	 * @return mixed
	 */
	public static function instance($class, $method = '')
	{
		$key = $class . $method;

		if(!isset(self::$_instance[$key]))
		{
			if(class_exists($class))
			{
				$obj = new $class;

				if(!empty($method) && method_exists($class, $method))
				{
					self::$_instance[$key] = call_user_func(array(
						&$obj,
						$method
					));
				}
				else
				{
					self::$_instance[$key] = $obj;
				}
			}
			else
			{
				\RCPHP\Controller::halt("The " . $class . " class file does not exist");
			}
		}

		return self::$_instance[$key];
	}

	/**
	 * 引用类库 同java的Import
	 *
	 * @param $package
	 * @return bool|null
	 */
	public static function import($package)
	{
		if(empty($package))
		{
			return false;
		}

		$class = str_replace('.', DS, $package);

		$class_strut = explode('/', $class);

		$classfile = '';

		if(in_array($class_strut[0], array(
			'Cache',
			'Core',
			'DB',
			'Crypt',
			'Net',
			'Storage',
			'Util'
		)))
		{
			$classfile = RCPHP_PATH . $class . '.class.php';
		}
		elseif(is_dir(EXT_PATH . $class_strut[0]))
		{
			$classfile = EXT_PATH . $class . '.class.php';
		}

		if(class_exists(basename($class)) === true && !empty($classfile))
		{
			// 如果类不存在 则导入类库文件
			self::loadFile($classfile);

			return Structure::singleton(basename($class));
		}

		return null;
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