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
 * 冒泡排序
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
 * 插入排序
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
		$flag = $arr[$i]; //待插入的数
		for($j = $i - 1; $arr[$j] > $flag && $j >= 0; $j--) //向前插入合适位置
		{
			$arr[$j + 1] = $arr[$j]; //比它大的逐个后移
		}
		$arr[$j + 1] = $flag;
	}

	return $arr;
}

/**
 * 选择排序
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
		for($j = $i + 1; $j < $len; $j++) //记录剩下中最小那的
		{
			if($arr[$j] < $arr[$min])
			{
				$min = $j;
			}
		}
		if($min != $i) //把当前数与该最小数交换
		{
			$temp = $arr[$min];
			$arr[$min] = $arr[$i];
			$arr[$i] = $temp;
		}
	}

	return $arr;
}

/**
 * 快速排序
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

	$mid = $arr[0]; //以此数为基准拆分为两个数组
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