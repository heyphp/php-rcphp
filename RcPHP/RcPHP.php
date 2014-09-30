<?php
/**
 * RcPHP file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        RcPHP
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

/**
 * RcPHP �������
 */

/**
 * Determine the PHP version.
 */
version_compare(PHP_VERSION, '5.3.0', '>') or exit('The wrong version number.');

// �汾��Ϣ
const RCPHP_VERSION = "1.0-dev";

/**
 * Set default timezone.
 */
date_default_timezone_set("Asia/Shanghai");

/**
 * ����DIRECTORY_SEPARATOR�s��
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * �趨Ĭ����ҳ
 */
!defined('BASE_URI') && define('BASE_URI', '/index.php');

defined('RCPHP_PATH') or define('RCPHP_PATH', dirname(__FILE__) . DS);
defined('PRO_PATH') or define('PRO_PATH', dirname(RCPHP_PATH) . DS);

defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');
defined('APP_DEBUG') or define('APP_DEBUG', false);
defined('CONTROLLER_PATH') or define('CONTROLLER_PATH', APP_PATH . 'Controller' . DS);
defined('MODEL_PATH') or define('MODEL_PATH', APP_PATH . 'Model' . DS);
defined('VIEW_PATH') or define('VIEW_PATH', APP_PATH . 'View' . DS);

defined('RUNTIME_PATH') or define('RUNTIME_PATH', PRO_PATH . 'Runtime' . DS);
defined('CORE_PATH') or define('CORE_PATH', RCPHP_PATH . 'Core' . DS);
defined('COMMON_PATH') or define('COMMON_PATH', PRO_PATH . 'Common' . DS);
defined('CONF_PATH') or define('CONF_PATH', PRO_PATH . 'Conf' . DS);
defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . 'Logs' . DS);
defined('CACHE_PATH') or define('CACHE_PATH', RUNTIME_PATH . 'Cache' . DS);
defined('DATA_PATH') or define('DATA_PATH', PRO_PATH . 'Data' . DS);
defined('EXT_PATH') or define('EXT_PATH', PRO_PATH . 'Class' . DS);
defined('UPLOAD_PATH') or define('UPLOAD_PATH', PRO_PATH . 'Public' . DS . 'Upload' . DS);

defined("URL_MODEL") or define("URL_MODEL", 1);
defined("RCPHP_LOG") or define("RCPHP_LOG", true);
defined('DEFAULT_CONTROLLER') or define('DEFAULT_CONTROLLER', 'index');
defined('DEFAULT_ACTION') or define('DEFAULT_ACTION', 'index');

/**
 * ����������ú�����
 */
include RCPHP_PATH . 'Function' . DS . 'Common.php';

/**
 * ��ʼ��
 */
include CORE_PATH . 'RcPHP.class.php';
\RCPHP\RcPHP::run();