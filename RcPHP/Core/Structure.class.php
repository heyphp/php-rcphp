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
namespace RCPHP;

defined('IN_RCPHP') or exit('Access denied');

class Structure
{

	/**
	 * 初始化系统结构
	 *
	 * @return void
	 */
	public static function run()
	{
		//locked file.
		$runFile = RUNTIME_PATH . md5(APP_PATH);

		if(!file_exists($runFile))
		{
			if(!is_writable(PRO_PATH))
			{
				\RCPHP\Controller::halt('Do not write the [' . PRO_PATH . '] directory.');
			}

			$remote = '';
			//create remote server file.
			if(defined('REMOTE_PATH'))
			{
				$remote = REMOTE_PATH . DS;
				if(!is_dir(PRO_PATH . REMOTE_PATH . DS))
				{
					mkdir(PRO_PATH . REMOTE_PATH . DS, 0755, true);
				}
			}

			$sourceDirs = array(
				PRO_PATH . $remote . "Public",
				PRO_PATH . $remote . "Public" . DS . "Upload" . DS,
				PRO_PATH . $remote . "Public" . DS . "Style" . DS,
				PRO_PATH . $remote . "Public" . DS . "Script" . DS,
				PRO_PATH . $remote . "Public" . DS . "Image" . DS,
				EXT_PATH,
				COMMON_PATH,
				CONF_PATH,
				DATA_PATH
			);

			foreach($sourceDirs as $dir)
			{
				if(!is_dir($dir))
				{
					mkdir($dir, 0755, true);
				}
			}

			self::buildApp();

			self::buildController();

			self::buildRuntime();

			file_put_contents($runFile, implode("\n", $sourceDirs));
		}
	}

	/**
	 * Create app dir.
	 *
	 * @return void
	 */
	public static function buildApp()
	{
		// Create app directory.
		if(!is_dir(APP_PATH))
		{
			mkdir(APP_PATH, 0755, true);
		}

		if(!is_writable(APP_PATH))
		{
			\RCPHP\Controller::halt('Application [' . APP_PATH . '] cannot write, directory cannot be automatically generated.');
		}

		$appDirs = array(
			CONTROLLER_PATH,
			MODEL_PATH,
			VIEW_PATH,
			VIEW_PATH . "Layout" . DS
		);

		foreach($appDirs as $dir)
		{
			if(!is_dir($dir))
			{
				mkdir($dir, 0755, true);
			}
		}
	}

	/**
	 * Create runtime dir.
	 *
	 * @return void
	 */
	public static function buildRuntime()
	{
		if(!is_dir(RUNTIME_PATH))
		{
			mkdir(RUNTIME_PATH, 0755, true);
		}

		if(!is_writable(RUNTIME_PATH))
		{
			\RCPHP\Controller::halt('Do not write the runtime directory.');
		}

		if(!is_dir(CACHE_PATH))
		{
			mkdir(CACHE_PATH, 0755);
		}
		if(!is_dir(LOG_PATH))
		{
			mkdir(LOG_PATH, 0755);
		}
	}

	/**
	 * Write default controller files.
	 *
	 * @return void
	 */
	public static function buildController()
	{
		$file = CONTROLLER_PATH . 'indexController.class.php';

		if(!file_exists($file))
		{
			if(\RCPHP\Util\Check::isClient())
			{
				$controller = '<?php
class indexController extends \RCPHP\Controller {
    public function index(){
        echo "Welcome RcPHP!\n";
    }
}';
			}
			else
			{
				$controller = '<?php
class indexController extends \RCPHP\Controller {
    public function index(){
        echo \'<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>Welcome <b>RcPHP</b>!</p></div>\';
    }
}';
			}
			file_put_contents($file, $controller);
		}
	}
}