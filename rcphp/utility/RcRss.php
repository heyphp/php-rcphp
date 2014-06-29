<?php
/**
 * RcRss class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcRss extends RcBase
{

	/**
	 * RSS频道名
	 *
	 * @var string
	 */
	protected $channelTitle = '';

	/**
	 * RSS频道链接
	 *
	 * @var string
	 */
	protected $channelLink = '';

	/**
	 * RSS频道描述
	 *
	 * @var string
	 */
	protected $channelDescription = '';

	/**
	 * RSS频道使用的小图标的URL
	 *
	 * @var string
	 */
	protected $channelImgurl = '';

	/**
	 * RSS频道所使用的语言
	 *
	 * @var string
	 */
	protected $language = 'zh_CN';

	/**
	 * RSS文档创建日期，默认为今天
	 *
	 * @var string
	 */
	protected $pubDate = '';

	/**
	 * RSS文档最后更新日期
	 *
	 * @var string
	 */
	protected $lastBuildDate = '';

	/**
	 * RSS文档的创建者
	 *
	 * @var string
	 */
	protected $generator = 'RcPHP RSS Generator';

	/**
	 * RSS单条信息的数组
	 *
	 * @var string
	 */
	protected $items = array();

	/**
	 * 构造函数
	 * @param string $title
	 * @param string $link
	 * @param string $description
	 * @param string $imgurl
	 * @return void
	 */
	public function __construct()
	{
		//初始化RSS订阅的信息日期为今天
		$this->pubDate = Date('Y-m-d H:i:s', time());
		$this->lastBuildDate = Date('Y-m-d H:i:s', time());
	}

	/**
	 * 设置基本信息
	 * @param string $title
	 * @param string $link
	 * @param string $description
	 * @param string $imgurl
	 * @param string $pubDate
	 * @param string $lastBuildDate
	 * @return void
	 */
	public function setOption($title, $link, $description, $imgurl = '', $pubDate = '', $lastBuildDate = '')
	{
		$this->channelTitle = $title;
		$this->channelLink = $link;
		$this->channelDescription = $description;
		$this->channelImgurl = $imgurl;
		$this->pubDate = empty($pubDate) ? date('Y-m-d H:i:s', time()) : $pubDate;
		$this->lastBuildDate = empty($lastBuildDate) ? date('Y-m-d H:i:s', time()) : $lastBuildDate;
	}

	/**
	 * 设置私有变量
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function config($key, $value)
	{
		$this->$key = $value;
	}

	/**
	 * 添加RSS项
	 *
	 * @param string $title
	 * @param string $link
	 * @param string $description
	 * @param string $pubDate
	 * @return void
	 */
	public function addItem($title, $link, $description, $pubDate)
	{
		$this->items[] = array(
			'title' => $title,
			'link' => $link,
			'description' => $description,
			'pubDate' => $pubDate
		);
	}

	/**
	 * 输出RSS的XML为字符串
	 *
	 * @return string
	 */
	public function fetch()
	{
		$rss = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n";
		$rss .= "<rss version=\"2.0\">\r\n";
		$rss .= "<channel>\r\n";
		$rss .= "<title><![CDATA[" . $this->channelTitle . "]]></title>\r\n";
		$rss .= "<description><![CDATA[" . $this->channelDescription . "]]></description>\r\n";
		$rss .= "<link>" . $this->channelLink . "</link>\r\n";
		$rss .= "<language>" . $this->language . "</language>\r\n";

		if(!empty($this->pubDate))
		{
			$rss .= "<pubDate>" . $this->pubDate . "</pubDate>\r\n";
		}

		if(!empty($this->lastBuildDate))
		{
			$rss .= "<lastBuildDate>" . $this->lastBuildDate . "</lastBuildDate>\r\n";
		}

		if(!empty($this->generator))
		{
			$rss .= "<generator>" . $this->generator . "</generator>\r\n";
		}

		$rss .= "<ttl>5</ttl>\r\n";

		if(!empty($this->channel_imgurl))
		{
			$rss .= "<image>\r\n";
			$rss .= "<title><![CDATA[" . $this->channelTitle . "]]></title>\r\n";
			$rss .= "<link>" . $this->channelLink . "</link>\r\n";
			$rss .= "<url>" . $this->channelImgurl . "</url>\r\n";
			$rss .= "</image>\r\n";
		}

		for($i = 0; $i < count($this->items); $i++)
		{
			$rss .= "<item>\r\n";
			$rss .= "<title><![CDATA[" . $this->items[$i]['title'] . "]]></title>\r\n";
			$rss .= "<link>" . $this->items[$i]['link'] . "</link>\r\n";
			$rss .= "<description><![CDATA[" . $this->items[$i]['description'] . "]]></description>\r\n";
			$rss .= "<pubDate>{" . $this->items[$i]['pubDate'] . "</pubDate>\r\n";
			$rss .= "</item>\r\n";
		}

		$rss .= "</channel>\r\n</rss>";

		return $rss;
	}

	/**
	 * 输出RSS的XML到浏览器
	 *
	 * @return void
	 */
	public function display()
	{
		header("Content-Type: text/xml; charset=utf-8");
		echo $this->Fetch();
		exit();
	}
}
