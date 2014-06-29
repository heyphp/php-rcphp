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
 * 定义项目所在路径
 */
define("APP_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

/**
 * 定义项目使用编码
 */
define('CHARSET', 'UTF-8');

/**
 * 定义项目是否开启debug模式
 */
define('RCPHP_DEBUG', true);

/**
 * 定义项目是否使用PHP模板
 */
define('RCPHP_VIEW', true);

/**
 * 定义框架所在路径
 */
define('RCPHP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rcphp' . DIRECTORY_SEPARATOR);

/**
 * 引入框架主文件
 */
require RCPHP_PATH . 'RcPHP.php';

/**
 * 开启进程
 */
RcPHP::run();