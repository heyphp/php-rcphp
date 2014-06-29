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
define("APP_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

/**
 * ������Ŀʹ�ñ���
 */
define('CHARSET', 'UTF-8');

/**
 * ������Ŀ�Ƿ���debugģʽ
 */
define('RCPHP_DEBUG', true);

/**
 * ������Ŀ�Ƿ�ʹ��PHPģ��
 */
define('RCPHP_VIEW', true);

/**
 * ����������·��
 */
define('RCPHP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rcphp' . DIRECTORY_SEPARATOR);

/**
 * ���������ļ�
 */
require RCPHP_PATH . 'RcPHP.php';

/**
 * ��������
 */
RcPHP::run();