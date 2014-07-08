<?php
/**
 * indexController.class.php
 * Ä¬ÈÏ¿ØÖÆÆ÷
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class indexController extends RcController
{

	public function index()
	{
		echo 123;
	}

	public function test()
	{
		echo RcRequest::get("id");
	}
}