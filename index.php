<?php
/**
 * Bootstrap.
 *
 * @author        zhangwj<zhangwj@2345.com>
 * @copyright     Copyright (c) 2013,RcPHP Dev Team
 */
header("Content-Type:text/html;charset=GBK");

define('IN_RCPHP', true);

/**
 * ������Ŀ����·��
 */
define("APP_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR);

/**
 * ������Ŀʹ�ñ���
 */
define('CHARSET', 'GBK');

/**
 * ·��ģʽ
 */
define('URL_MODEL', 2);

/**
 * ������Ŀ�Ƿ���debugģʽ
 */
define('APP_DEBUG', true);

/**
 * Ӧ�ð汾��
 */
define('APP_VERSION', 0.1);

/**
 * ���������ļ�
 */
require 'RcPHP/RcPHP.php';