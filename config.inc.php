<?php
return array(
	'driver' => 'pdo_mysql',
	'charset' => 'GBK',
	'prefix' => 'book_',
	'master' => array(
		'host' => '123.123.123.123',
		'port' => 3306,
		'user' => '123',
		'password' => '123',
		'database' => '123123'
	),
	'slave' => array(
		0 => array(
			'host' => '123.123.123.123',
			'port' => 3306,
			'user' => '123',
			'password' => '123123',
			'database' => '123'
		)
	)
);