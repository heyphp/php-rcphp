<?php
/**
 * Created by PhpStorm.
 * User: zhangwj
 * Date: 14-7-20
 * Time: ÏÂÎç9:07
 */
var_dump($_ENV);
if(!empty($_ENV['VCAP_SERVICES']))
{
	$conf = json_decode($_ENV['VCAP_SERVICES'], true);
	var_dump($conf);

	return array(
		'driver' => 'mysql',
		'charset' => 'GBK',
		'prefix' => 'rz_',
		'master' => array(
			'host' => $conf['mysql']['credentials']['hostname'],
			'port' => $conf['mysql']['credentials']['port'],
			'user' => $conf['mysql']['credentials']['username'],
			'password' => $conf['mysql']['credentials']['password'],
			'database' => $conf['mysql']['credentials']['name']
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