<?php
/**
 * RcDate class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcDate extends RcBase
{

	/**
	 * 判断闰年
	 * @param int $year
	 * @return boolen
	 */
	public static function isLeapyear($year = null)
	{
		if(is_null($year))
		{
			//不存在年份则设定为当前年份
			$year = date('Y', time());
		}

		return date('L', $year);
	}

	/**
	 * 获取星期
	 * @param int    $time
	 * @param boolen $chinese
	 * @return int|string
	 */
	public static function getWeek($time = null, $chinese = false)
	{
		if(is_null($time))
		{
			//不存在时间则设定为当前时间
			$time = time();
		}

		$week = date('w', $time);

		$weekArr = array(
			"星期天",
			"星期一",
			"星期二",
			"星期三",
			"星期四",
			"星期五",
			"星期六"
		);

		return $chinese === false ? intval($week) : $weekArr[intval($week)];
	}

	/**
	 * 日期转换时间戳
	 * @param string $date
	 * @return int
	 */
	public static function getUnixTime($date = null)
	{
		if(is_null($date))
		{
			return false;
		}

		return strtotime($date);
	}

	/**
	 * 计算时间差
	 * @param string $date
	 * @param string $new_date
	 * @return int
	 */
	public static function getDifference($date, $new_date = null)
	{
		$date = strtotime($date);
		$new_date = is_null($new_date) ? time() : strtotime($new_date);

		return abs(ceil(($date - $new_date) / 86400));
	}

	/**
	 * 获取一年中的某一天
	 * @param int $day
	 * @param int $year
	 * @return string
	 */
	public static function getYearDay($day, $year = null)
	{
		if(is_null($year))
		{
			$year = date('Y', time());
		}

		$unixTime = mktime(0, 0, 0, 1, 1, $year) + $day * 86400;

		return date('Y-m-d', $unixTime);
	}

	/**
	 * 输出友好时间
	 * @param int $time
	 * @return string
	 */
	public static function mdate($time = null)
	{
		$text = '';
		$time = is_null($time) || $time > time() ? time() : intval($time);
		$t = time() - $time; //时间差 （秒）
		if($t <= 3)
		{
			$text = '刚刚';
		}
		else if($t < 60)
		{
			$text = $t . '秒前'; // 一分钟内
		}
		else if($t < 60 * 60)
		{
			$text = floor($t / 60) . '分钟前'; //一小时内
		}
		else if($t < 60 * 60 * 24)
		{
			$text = floor($t / (60 * 60)) . '小时前'; // 一天内
		}
		else if($t < 60 * 60 * 24 * 3)
		{
			$text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time); //昨天和前天
		}
		else if($t < 60 * 60 * 24 * 30)
		{
			$text = date('m月d日 H:i', $time); //一个月内
		}
		else if($t < 60 * 60 * 24 * 365)
		{
			$text = date('m月d日', $time); //一年内
		}
		else
		{
			$text = date('Y年m月d日', $time); //一年以前
		}

		return $text;
	}
}