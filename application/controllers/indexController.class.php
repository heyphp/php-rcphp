<?php
/**
 * indexController.class.php
 * Ĭ�Ͽ�����
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
		$numbers = range (1,20); //��1��20���һ������
		shuffle ($numbers); //����������������
		$result = array_slice($numbers,1,5); //ȡ����ǰ5��Ԫ��
		print_r($result);

		echo implode("",$result);


	}

	public function test()
	{
		echo Request::get("id");
	}
}