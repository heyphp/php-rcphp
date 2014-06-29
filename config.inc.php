<?php
return array(
	'driver' => 'pdo_mysql',
	'charset' => 'GBK',
	'prefix' => 'book_',
	'master' => array(
		'host' => '172.16.0.233',
		'port' => 3306,
		'user' => 'root',
		'password' => 'rcroot',
		'database' => 'book_2345'
	),
	'slave' => array(
		0 => array(
			'host' => '172.16.0.233',
			'port' => 3306,
			'user' => 'root',
			'password' => 'rcroot',
			'database' => 'book_2345'
		)
	)
);