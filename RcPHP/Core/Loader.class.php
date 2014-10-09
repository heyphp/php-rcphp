<?php
/**
 * Loader class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
namespace RCPHP;

defined('IN_RCPHP') or exit('Access denied');

class Loader
{

	/**
	 * �����ļ�����
	 *
	 * @var array
	 */
	public static $CoreClassArray = array(
		'RCPHP\Controller' => 'Core/Controller.class.php',
		'RCPHP\Debug' => 'Core/Debug.class.php',
		'RCPHP\Log' => 'Core/Log.class.php',
		'RCPHP\Model' => 'Core/Model.class.php',
		'RCPHP\Request' => 'Core/Request.class.php',
		'RCPHP\Route' => 'Core/Route.class.php',
		'RCPHP\Structure' => 'Core/Structure.class.php',
		'RCPHP\View' => 'Core/View.class.php',
		'RCPHP\Net\Curl' => 'Net/Curl.class.php',
		'RCPHP\Net\Http' => 'Net/Http.class.php',
		'RCPHP\Net\Upload' => 'Net/Upload.class.php',
		'RCPHP\Net\Location' => 'Net/Location.class.php',
		'RCPHP\Storage\Redis' => 'Storage/Redis.class.php',
		'Captcha' => 'Util/Captcha.class.php',
		'RCPHP\Util\Check' => 'Util/Check.class.php',
		'Cookie' => 'Util/Cookie.class.php',
		'Csv' => 'Util/Csv.class.php',
		'Date' => 'Util/Date.class.php',
		'RCPHP\Util\File' => 'Util/File.class.php',
		'Image' => 'Util/Image.class.php',
		'Pinyin' => 'Util/Pinyin.class.php',
		'Session' => 'Util/Session.class.php',
		'RCPHP\Util\String' => 'Util/String.class.php',
		'Xml' => 'Util/Xml.class.php',
		'Yac' => 'Cache/Yac.class.php',
		'Apc' => 'Cache/Apc.class.php',
		'Memcache' => 'Cache/Memcache.class.php',
		'Xcache' => 'Cache/Xcache.class.php',
		'Mysql' => 'Db/Mysql.class.php',
		'Oauth' => 'Oauth/Oauth.class.php',
		'Baidu' => 'Oauth/Baidu.class.php',
		'Github' => 'Oauth/Github.class.php',
		'Weibo' => 'Oauth/Weibo.class.php',
		'Google' => 'Oauth/Google.class.php',
		'Des' => 'Crypt/Des.class.php'
	);

	/**
	 * ע���Զ����غ���
	 *
	 * @return void
	 */
	public static function registerAutoloader()
	{
		spl_autoload_register('RCPHP\Loader::autoload');
	}

	/**
	 * �Զ����� __autoload
	 *
	 * @param string $class
	 * @return void
	 */
	private static function autoload($class)
	{
		if(!empty(self::$CoreClassArray[$class]))
		{
			include RCPHP_PATH . self::$CoreClassArray[$class];
		}
		elseif(substr($class, -10) == 'Controller')
		{
			//controller�ļ��Զ��ط���
			if(is_file(CONTROLLER_PATH . $class . '.class.php'))
			{
				//���ļ���controller��Ŀ¼�´���ʱ,ֱ�Ӽ���.
				\RCPHP\RcPHP::loadFile(CONTROLLER_PATH . $class . '.class.php');
			}
			else
			{
				\RCPHP\Controller::halt('The Controller File:' . $class . '.class.php is not exists!');
			}
		}
		else if(substr($class, -5) == 'Model')
		{
			//modlel�ļ��Զ����ط���
			if(is_file(MODEL_PATH . $class . '.class.php'))
			{
				//����Ҫ���ص�model�ļ�����ʱ
				\RCPHP\RcPHP::loadFile(MODEL_PATH . $class . '.class.php');
			}
			else
			{
				//����Ҫ���ص��ļ�������ʱ,��ʾ������ʾ��Ϣ
				\RCPHP\Controller::halt('The Model file: ' . $class . ' is not exists!');
			}
		}
		else
		{
			//������չĿ¼�ļ�
			if(is_file(EXT_PATH . $class . '.class.php'))
			{
				//����չĿ¼���ļ�����ʱ,������ļ�
				\RCPHP\RcPHP::loadFile(EXT_PATH . $class . '.class.php');
			}
			else
			{
				//�����Զ����Զ�����
				$config = RcPHP::getConfig('autoload');

				if(empty($config))
				{
					\RCPHP\Controller::halt('Autoload config file is not exists!');
				}

				$autoStatus = false;
				foreach((array)$config as $key => $rule)
				{
					//���Զ����Զ����ص��ļ�����ʱ
					if($key == $class)
					{
						\RCPHP\RcPHP::loadFile($config[$class]);
						$autoStatus = true;
						break;
					}
				}
				//��ִ�����Զ����Զ����ع����,��û���ҵ���Ҫ���ص��ļ�ʱ,��ʾ������Ϣ
				if($autoStatus == false)
				{
					\RCPHP\Controller::halt('The file of class ' . $class . ' is not exists!');
				}
			}
		}
	}
}