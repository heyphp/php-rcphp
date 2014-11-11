<?php
/**
 * Date class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Util
 * @since          1.0
 */
namespace RCPHP\Util;

defined('IN_RCPHP') or exit('Access denied');

class Date
{

	/**
	 * Judge a leap year
	 *
	 * @param string $year
	 * @return bool
	 */
	public static function is_leap_year($year = '')
	{
		if(empty($year))
		{
			//������������趨Ϊ��ǰ���
			$year = date('Y', time());
		}

		return date('L', $year) == 1 ? true : false;
	}

	/**
	 * Conversion time stamp
	 *
	 * @param string $date
	 * @return bool|int
	 */
	public static function unix_time($date)
	{
		if(empty($date))
		{
			return false;
		}

		return strtotime($date);
	}

	/**
	 * Calculating the time difference
	 *
	 * @param string $date
	 * @param string $new_date
	 * @return int
	 */
	public static function difference_date($date, $newDate = null)
	{
		$date = strtotime($date);
		$new_date = is_null($newDate) ? time() : strtotime($newDate);

		return abs(ceil(($date - $newDate) / 86400));
	}

	/**
	 * For one day in a year.
	 *
	 * @param int $day
	 * @param int $year
	 * @return string
	 */
	public static function get_year_day($day, $year = '')
	{
		if(empty($year))
		{
			$year = date('Y', time());
		}

		$unixTime = mktime(0, 0, 0, 1, 1, $year) + $day * 86400;

		return date('Y-m-d', $unixTime);
	}

	/**
	 * Formatted time stamp
	 *
	 * @param int $time
	 * @return string
	 */
	public static function format_date($time = null)
	{
		$text = '';
		$time = is_null($time) || $time > time() ? time() : intval($time);

		//ʱ��� ���룩
		$t = time() - $time;
		if($t <= 3)
		{
			$text = '�ո�';
		}
		else if($t < 60)
		{
			$text = $t . '��ǰ';
		}
		else if($t < 60 * 60)
		{
			$text = floor($t / 60) . '����ǰ';
		}
		else if($t < 60 * 60 * 24)
		{
			$text = floor($t / (60 * 60)) . 'Сʱǰ';
		}
		else if($t < 60 * 60 * 24 * 3)
		{
			$text = floor($time / (60 * 60 * 24)) == 1 ? '���� ' . date('H:i', $time) : 'ǰ�� ' . date('H:i', $time);
		}
		else if($t < 60 * 60 * 24 * 30)
		{
			$text = date('m��d�� H:i', $time);
		}
		else if($t < 60 * 60 * 24 * 365)
		{
			$text = date('m��d��', $time);
		}
		else
		{
			$text = date('Y��m��d��', $time);
		}

		return $text;
	}
}
