<?php
/**
 * Bootstrap.
 *
 * @author    zhangwj<zhangwj@2345.com>
 * @copyright Copyright (c) 2013,RcPHP Dev Team
 */
header("Content-Type:text/html;charset=GBK");

define('IN_RCPHP', true);

/**
 * 定义项目所在路径
 */
define("APP_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR);

/**
 * 定义项目使用编码
 */
define('CHARSET', 'GBK');

/**
 * 路由模式
 */
define('URL_MODEL', 2);

/**
 * 定义项目是否开启debug模式
 */
define('APP_DEBUG', true);

/**
 * 应用版本号
 */
define('APP_VERSION', 0.1);

/**
 * 引入框架主文件
 */
require 'RcPHP/RcPHP.php';