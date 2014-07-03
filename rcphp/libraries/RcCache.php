<?php
/**
 * RcCache class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcCache extends RcBase
{

	/**
	 * 工厂模式实例化缓存类
	 *
	 * @param string $adapter
	 * @param array  $options
	 * @return object
	 */
	public static function getInstance($adapter, $options = null)
	{
		if(empty($adapter))
		{
			RcController::halt("The cache adapter cannot be empty.");
		}

		$adapter = strtolower($adapter);

		//当为memcache时
		if($adapter == 'memcache')
		{
			return is_null($options) ? RcCacheMemcache::getInstance($options) : RcStructure::singleton('RcCacheMemcache');
		}
		if(in_array($adapter, array(
			'file',
			'apc',
			'xcache',
			'eaccelerator'
		))
		)
		{
			return RcController::instance('RcCache' . $adapter);
		}

		return false;
	}
}