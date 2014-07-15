<?php
/**
 * XML class php
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Library.Util
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Xml
{

	/**
	 * 加载xml文件.支持文件名及xml代码
	 *
	 * @param string $fileName
	 * @return string
	 */
	public static function loadXml($fileName)
	{

		return is_file($fileName) ? simplexml_load_file($fileName) : simplexml_load_string($fileName);
	}

	/**
	 * 将XML代码转化为数组
	 *
	 * @param string $string
	 * @return array
	 */
	public static function xmlDecode($string)
	{

		$xml = self::loadXml($string);

		return json_decode(json_encode($xml), true);
	}

	/**
	 * 数据转化为xml代码
	 *
	 * @param array $data
	 * @return string
	 */
	protected static function dataToXml($data)
	{

		if(is_object($data))
		{
			$data = get_object_vars($data);
		}

		$xml = '';
		foreach($data as $key => $val)
		{
			is_numeric($key) && $key = "item id=\"$key\"";
			$xml .= "<$key>";
			$xml .= (is_array($val) || is_object($val)) ? self::dataToXml($val) : str_replace(array(
				"&",
				"<",
				">",
				"\"",
				"'",
				"-"
			), array(
				"&amp;",
				"&lt;",
				"&gt;",
				"&quot;",
				"&apos;",
				"&#45;"
			), $val);
			list ($key,) = explode(' ', $key);
			$xml .= "</$key>";
		}

		return $xml;
	}

	/**
	 * 进行对xml编码
	 *
	 * @param string $data
	 * @param string $root
	 * @return string
	 */
	public static function xmlEncode($data, $root = null, $encoding = 'UTF-8')
	{

		if(!$data)
		{
			return false;
		}

		$root = is_null($root) ? 'root' : trim($root);
		$xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\" ?>\r";
		$xml .= "<" . $root . ">\r";
		$xml .= self::dataToXml($data);
		$xml .= "</" . $root . ">";

		return $xml;
	}
}