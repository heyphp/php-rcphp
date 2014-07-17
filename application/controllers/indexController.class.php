<?php
/**
 * indexController.class.php
 * 默认控制器
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
		$numbers = range (1,20); //将1到20组成一个数组
		shuffle ($numbers); //对数组进行随机排序
		$result = array_slice($numbers,1,5); //取数组前5个元素
		print_r($result);

		echo implode("",$result);


	}

	public function test()
	{
		echo Request::get("id");
	}
}