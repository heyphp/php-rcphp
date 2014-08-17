<?php
/**
 * Created by PhpStorm.
 * User: zhangwj
 * Date: 14-7-20
 * Time: ÏÂÎç9:07
 */

if(!empty($_ENV['VCAP_SERVICES']))
{
	$conf = json_decode($_ENV['VCAP_SERVICES'], true);

	return array(
		'driver' => 'mysql',
		'charset' => 'GBK',
		'prefix' => 'rz_',
		'master' => array(
			'host' => $conf['mysql'][0]['credentials']['hostname'],
			'port' => $conf['mysql'][0]['credentials']['port'],
			'user' => $conf['mysql'][0]['credentials']['username'],
			'password' => $conf['mysql'][0]['credentials']['password'],
			'database' => $conf['mysql'][0]['credentials']['name']
		),
	);
}
else
{
	return array(
		'driver' => 'mysql',
		'charset' => 'GBK',
		'prefix' => 'rz_',
		'master' => array(
			'host' => '127.0.0.1',
			'port' => 3306,
			'user' => 'root',
			'password' => '',
			'database' => 'renzhi'
		),
	);
}