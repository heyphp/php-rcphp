<?php
/**
 * Bootstrap.
 *
 * @author        zhangwj<zhangwj@2345.com>
 * @copyright     Copyright (c) 2013,RcPHP Dev Team
 * @filesource
 */
define('IN_RCPHP', true);

/**
 * ������Ŀ����·��
 */
define("APP_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR);

/**
 * ������Ŀʹ�ñ���
 */
define('CHARSET', 'UTF-8');

/**
 * ������Ŀ�Ƿ���debugģʽ
 */
define('APP_DEBUG', true);

/**
 * ���������ļ�
 */
require 'RcPHP/RcPHP.php';