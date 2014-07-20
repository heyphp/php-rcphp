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

class indexController extends Controller
{

	public function index()
	{
		echo '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "Î¢ÈíÑÅºÚ"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>Welcome <b>RcPHP</b>!</p></div>';
	}
}