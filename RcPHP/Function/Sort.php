<?php
/**
 * Sort function file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Function
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

/**
 * ð������
 *
 * @param array $arr
 * @return array
 */
function bubble_sort(Array $arr)
{
	$len = count($arr);

	if($len <= 0)
	{
		return $arr;
	}

	for($i = 0; $i < $len - 1; $i++)
	{
		for($j = $i + 1; $j < $len - 1; $j++)
		{
			if($arr[$i] > $arr[$j])
			{
				$t = $arr[$i];
				$arr[$i] = $arr[$j];
				$arr[$j] = $t;
			}
		}
	}

	return $arr;
}

/**
 * ��������
 *
 * @param array $arr
 * @return array
 */
function insert_sort(Array $arr)
{
	$len = count($arr);

	if($len <= 0)
	{
		return $arr;
	}

	for($i = 1; $i < $len; $i++)
	{
		$flag = $arr[$i]; //���������
		for($j = $i - 1; $arr[$j] > $flag && $j >= 0; $j--) //��ǰ�������λ��
		{
			$arr[$j + 1] = $arr[$j]; //��������������
		}
		$arr[$j + 1] = $flag;
	}

	return $arr;
}

/**
 * ѡ������
 *
 * @param array $arr
 * @return array
 */
function select_sort(Array $arr)
{
	$len = count($arr);

	if($len <= 0)
	{
		return $arr;
	}

	for($i = 0; $i < $len; $i++)
	{
		$min = $i;
		for($j = $i + 1; $j < $len; $j++) //��¼ʣ������С�ǵ�
		{
			if($arr[$j] < $arr[$min])
			{
				$min = $j;
			}
		}
		if($min != $i) //�ѵ�ǰ�������С������
		{
			$temp = $arr[$min];
			$arr[$min] = $arr[$i];
			$arr[$i] = $temp;
		}
	}

	return $arr;
}

/**
 * ��������
 *
 * @param array $arr
 * @return array
 */
function quick_sort(Array $arr)
{
	$len = count($arr);

	if($len <= 0)
	{
		return $arr;
	}

	$mid = $arr[0]; //�Դ���Ϊ��׼���Ϊ��������
	$l = $r = array();
	for($i = 1; $i < $len; $i++)
	{
		if($arr[$i] < $mid)
		{
			$l[] = $arr[$i];
		}
		else
		{
			$r[] = $arr[$i];
		}
	}
	$l = quick_sort($l);
	$r = quick_sort($r);

	return array_merge($l, array($mid), $r);
}