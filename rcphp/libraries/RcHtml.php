<?php
/**
 * RcHtml class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcHtml extends RcBase
{

	/**
	 * 转义字符串
	 * @param string $text
	 * @return string
	 */
	public static function encode($text)
	{

		if(!is_array($text))
		{
			return dhtmlspecialchars($text);
		}

		foreach($text as $key => $value)
		{
			$text[$key] = self::encode($value);
		}

		return $text;
	}

	/**
	 * 处理超级连接代码
	 * @param string $text
	 * @param string $href
	 * @param array  $options
	 * @return string
	 */
	public static function link($text, $href = '#', $options = array())
	{

		if(!empty($href))
		{
			$options['href'] = $href;
		}

		//为了SEO效果,link的title处理.
		if(empty($options['title']) && empty($options['TITLE']))
		{
			$options['title'] = $text;
		}

		return self::makeTag('a', $options, $text);
	}

	/**
	 * 处理图片代码
	 * @param string $src
	 * @param string $alt
	 * @param array  $options
	 * @return string
	 */
	public static function image($src, $options = array(), $alt = null)
	{

		//参数分析
		if(!$src)
		{
			return false;
		}

		$options['src'] = $src;

		if($alt)
		{
			$options['alt'] = $alt;
			//为了SEO效果,加入title.
			if(empty($options['title']))
			{
				$options['title'] = $alt;
			}
		}

		return self::makeTag('img', $options);
	}

	/**
	 * 处理标签代码
	 * @param string  $tag
	 * @param array   $options
	 * @param string  $content
	 * @param boolean $closeTag
	 * @return string
	 */
	public static function makeTag($tag, $options = array(), $content = null, $closeTag = true)
	{

		$optionStr = '';
		//当$options不为空或类型不为数组时
		if(!empty($options) && is_array($options))
		{
			foreach($options as $name => $value)
			{
				$optionStr .= ' ' . $name . '="' . $value . '"';
			}
		}

		$html = '<' . $tag . $optionStr;

		if(!is_null($content))
		{

			return $closeTag ? $html . '>' . $content . '</' . $tag . '>' : $html . '>' . $content;
		}
		else
		{

			return $closeTag ? $html . '/>' : $html . '>';
		}
	}

	/**
	 * 加载css文件
	 *
	 * @param string $url
	 * @param string $media
	 * @return string
	 */
	public static function cssFile($url, $media = null)
	{

		//参数分析
		if(!empty($media))
		{
			$media = ' media="' . $media . '"';
		}

		return "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . self::encode($url) . "\"" . $media . " />\r";
	}

	/**
	 * 加载JavaScript文件
	 *
	 * @param string $url
	 * @return string
	 */
	public static function scriptFile($url)
	{

		return "<script type=\"text/javascript\" src=\"" . self::encode($url) . "\"></script>\r";
	}

	/**
	 * 生成表格
	 * @param array $content
	 * @param array $options
	 * @return string
	 */
	public static function table($content = array(), $options = array())
	{

		//参数分析
		if(!$content)
		{
			return false;
		}

		$html = self::makeTag('table', $options, false, false);

		foreach($content as $lines)
		{
			if(is_array($lines))
			{
				$html .= '<tr>';
				foreach($lines as $value)
				{
					$html .= self::makeTag('td', '', $value);
				}
				$html .= '</tr>';
			}
		}

		return $html . '</table>';
	}

	/**
	 * form开始HTML代码,即:将<form>代码内容补充完整.
	 *
	 * @param string  $action
	 * @param string  $method
	 * @param array   $options
	 * @param boolean $enctypeItem
	 * @return string
	 */
	public static function formStart($action, $options = array(), $method = null, $enctypeItem = false)
	{

		//参数分析
		if(!$action)
		{
			return false;
		}

		$options['action'] = $action;
		$options['method'] = empty($method) ? 'post' : $method;
		if($enctypeItem === true)
		{
			$options['enctype'] = 'multipart/form-data';
		}

		return self::makeTag('form', $options, false, false);
	}

	/**
	 * form的HTML的结束代码
	 *
	 * @return string
	 */
	public static function formEnd()
	{

		return '</form>';
	}

	/**
	 * 处理input代码
	 *
	 * @param string $type
	 * @param array  $options
	 * @return string
	 */
	public static function input($type, $options = array())
	{

		//参数分析
		if(!$type)
		{
			return false;
		}

		$options['type'] = $type;

		return self::makeTag('input', $options);
	}

	/**
	 * 处理text表单代码
	 *
	 * @param array $options
	 * @return string
	 */
	public static function text($options = array())
	{

		return self::makeTag('text', $options);
	}

	/**
	 * 生成密码框
	 * @param array $options
	 * @return string
	 */
	public static function password($options = array())
	{

		return self::input('password', $options);
	}

	/**
	 * 生成提交按钮
	 * @param array $options
	 * @return string
	 */
	public static function submit($options = array())
	{
		return self::input('submit', $options);
	}

	/**
	 * 生成重置按钮
	 * @param array $options
	 * @return string
	 */
	public static function reset($options = array())
	{
		return self::input('reset', $options);
	}

	/**
	 * 生成普通按钮
	 * @param array $options
	 * @return string
	 */
	public static function button($options = array())
	{
		return self::input('button', $options);
	}

	/**
	 * 生成文本域
	 * @param array  $options
	 * @param string $content
	 * @return string
	 */
	public static function textarea($options = array(), $content = null)
	{

		$optionStr = '';
		//当$options不为空或类型不为数组时
		if(!empty($options) && is_array($options))
		{
			foreach($options as $name => $value)
			{
				$optionStr .= ' ' . $name . '="' . $value . '"';
			}
		}

		$html = '<textarea' . $optionStr . '>';

		return ($content == true) ? $html . $content . '</textarea>' : $html . '</textarea>';
	}

	/**
	 * 生成下拉列表
	 * @param array   $contentArray
	 * @param array   $options
	 * @param boolean $selected
	 * @return string
	 */
	public static function select($contentArray, $options = array(), $selected = false)
	{

		if(!$contentArray || !is_array($contentArray))
		{
			return false;
		}

		$optionStr = '';
		foreach($contentArray as $key => $value)
		{
			if($selected == true)
			{
				$optionStr .= ($key == $selected) ? '<option value="' . $key . '" selected="selected">' . $value . '</option>' : '<option value="' . $key . '">' . $value . '</option>';
			}
			else
			{
				$optionStr .= '<option value="' . $key . '">' . $value . '</option>';
			}
		}

		return self::makeTag('select', $options, $optionStr);
	}

	/**
	 * 生成复选框
	 * @param array   $contentArray
	 * @param array   $options
	 * @param boolean $selected
	 * @return string
	 */
	public static function checkbox($contentArray, $options = array(), $selected = false)
	{

		//参数分析
		if(!$contentArray || !is_array($contentArray))
		{
			return false;
		}

		$html = '';
		foreach($contentArray as $key => $value)
		{
			$options['value'] = $key;
			if(is_array($selected) && !empty($selected))
			{
				if(in_array($key, $selected))
				{
					$options['checked'] = 'checked';
				}
				else
				{
					if(isset($options['checked']))
					{
						unset($options['checked']);
					}
				}
			}
			$html .= '<label>' . self::input('checkbox', $options) . $value . '</label>';
		}

		return $html;
	}

	/**
	 * 生成单选框
	 * @param array   $contentArray
	 * @param array   $options
	 * @param boolean $selected
	 * @return string
	 */
	public static function radio($contentArray, $options = array(), $selected = 0)
	{

		//参数分析
		if(!$contentArray || !is_array($contentArray))
		{
			return false;
		}

		$html = '';
		foreach($contentArray as $key => $value)
		{
			$options['value'] = $key;
			if($selected == $key)
			{
				$options['checked'] = 'checked';
			}
			else
			{
				if(isset($options['checked']))
				{
					unset($options['checked']);
				}
			}
			$html .= '<label>' . self::input('radio', $options) . $value . '</label>';
		}

		return $html;
	}
}