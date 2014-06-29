<?php
/**
 * RcLoader class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        core
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcLoader
{

	/**
	 * Class map.
	 *
	 * @var array
	 */
	private static $coreClassMap = array(
		'RcBase' => 'core/RcBase.php',
		'RcController' => 'core/RcController.php',
		'RcModel' => 'core/RcModel.php',
		'RcDebug' => 'core/RcDebug.php',
		'RcRoute' => 'core/RcRoute.php',
		'RcStructure' => 'core/RcStructure.php',
		'RcRequest' => 'core/RcRequest.php',
		'RcLog' => 'core/RcLog.php',
		'RcView' => 'core/RcView.php',
		'RcDbMysql' => 'db/RcDbMysql.php',
		'RcDbMysqli' => 'db/RcDbMysqli.php',
		'RcDbPdoMysql' => 'db/RcDbPdoMysql.php',
		'RcCookie' => 'libraries/RcCookie.php',
		'RcCurl' => 'libraries/RcCurl.php',
		'RcRedis' => 'libraries/RcRedis.php',
		'RcCache' => 'libraries/RcCache.php',
		'RcThread' => 'libraries/RcThread.php',
		'RcCsv' => 'libraries/RcCsv.php',
		'RcXml' => 'libraries/RcXml.php',
		'RcZip' => 'libraries/RcZip.php',
		'RcUpload' => 'libraries/RcUpload.php',
		'RcMcrypt' => 'libraries/RcMcrypt.php',
		'RcPinyin' => 'libraries/RcPinyin.php',
		'RcCheck' => 'libraries/RcCheck.php',
		'RcClient' => 'libraries/RcClient.php',
		'RcHtml' => 'libraries/RcHtml.php',
		'RcImage' => 'libraries/RcImage.php',
		'RcFile' => 'libraries/RcFile.php',
		'RcDate' => 'libraries/RcDate.php',
		'RcCacheApc' => 'libraries/cache/RcCacheApc.php',
		'RcCacheEaccelerator' => 'libraries/cache/RcCacheEaccelerator.php',
		'RcCacheFile' => 'libraries/cache/RcCacheFile.php',
		'RcCacheMemcache' => 'libraries/cache/RcCacheMemcache.php',
		'RcCacheWinCache' => 'libraries/cache/RcCacheWinCache.php',
		'RcCacheXcache' => 'libraries/cache/RcCacheXcache.php',
		'RcRss' => 'utility/RcRss.php',
		'RcCaptcha' => 'utility/RcCaptcha.php',
		'RcSvn' => 'utility/RcSvn.php'
	);

	/**
	 * Autoload class.
	 *
	 * @param string $className
	 * @return void
	 */
	public static function autoload($className)
	{
		if(isset(self::$coreClassMap[$className]))
		{
			RcPHP::loadFile(RCPHP_PATH . self::$coreClassMap[$className]);
		}
		else
		{
			$userLoad = RcPHP::getConfig('autoload');

			$fileName = APP_PATH . 'controllers' . DS . $className . '.class.php';
			if(file_exists($fileName))
			{
				RcPHP::loadFile($fileName);
			}
			elseif($userLoad !== false && !empty($userLoad[$className]))
			{
				RcPHP::loadFile(APP_PATH . $userLoad[$className]);
			}
			else
			{
				RcController::halt('The ' . $className . ' class file does not exist');
			}
		}
	}

	/**
	 * Register __autoload method.
	 *
	 * @return void
	 */
	public static function registerAutoloader()
	{
		if(function_exists("spl_autoload_register"))
		{
			spl_autoload_register(array(
				'RcLoader',
				'autoload'
			));
		}
		else
		{
			RcController::halt('The spl_autoload_register method does not exist.');
		}
	}
}