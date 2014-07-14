<?php
/**
 * Captcha class file.
 *
*@author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Library.Util
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class Captcha
{

	/**
	 * �������
	 *
*@var string
	 */
	private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ234567890';

	/**
	 * ��֤��
	 *
*@var string
	 */
	private $code;

	/**
	 * ��֤�볤��
	 *
*@var int
	 */
	private $codelen = 4;

	/**
	 * ���
	 *
*@var int
	 */
	private $width = 80;

	/**
	 * �߶�

	 *
*@var int
	 */
	private $height = 30;

	/**
	 * ͼ����Դ���

	 *
*@var object
	 */
	private $img;

	/**
	 * ָ��������

	 *
*@var string
	 */
	private $font;

	/**
	 * ָ�������С

	 *
*@var int
	 */
	private $fontsize = 20;

	/**
	 * ָ��������ɫ

	 *
*@var string
	 */
	private $fontcolor;

	/**
	 * ���췽��

	 *
*@return void
	 */
	public function __construct()
	{
		$this->font = RCPHP_PATH . 'sources/font/elephant.ttf';
	}

	/**
	 * Set the font

	 *
*@param $fontName
	 * @return $this
	 */
	public function setFont($fontName)
	{
		$this->font = $fontName;

		return $this;
	}

	/**
	 * ����ͼƬ��С
	 *
	 * @param int $width
	 * @param int $height
	 * @return $this
	 */
	public function setSize($width, $height)
	{
		$this->width = intval($width);
		$this->height = intval($height);

		return $this;
	}

	/**
	 * ���������
	 *
	 * @return void
	 */
	private function createCode()
	{
		$_len = strlen($this->charset) - 1;
		for($i = 0; $i < $this->codelen; $i++)
		{
			$this->code .= $this->charset[mt_rand(0, $_len)];
		}
	}

	/**
	 * ���ɱ���

	 *
*@return void
	 */
	private function createBg()
	{
		$this->img = imagecreatetruecolor($this->width, $this->height);
		$color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
		imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
	}

	/**
	 * ��������
	 *
	 * @return void
	 */
	private function createFont()
	{
		$_x = $this->width / $this->codelen;
		for($i = 0; $i < $this->codelen; $i++)
		{
			$this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
			imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
		}
	}

	/**
	 * ����������ѩ��

	 *
*@return void
	 */
	private function createLine()
	{
		for($i = 0; $i < 6; $i++)
		{
			$color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
			imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
		}
		for($i = 0; $i < 100; $i++)
		{
			$color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
			imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
		}
	}

	/**
	 * ���
	 *
	 * @return void
	 */
	private function outPut()
	{
		header('Content-type:image/png');
		header("Expires: -1");
		header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0");
		header("Pragma: no-cache");
		imagepng($this->img);
		imagedestroy($this->img);
	}

	/**
	 * ��������
	 *
	 * @return void
	 */
	public function doimg()
	{
		$this->createBg();
		$this->createCode();
		$this->createLine();
		$this->createFont();
		$this->outPut();
	}

	/**
	 * ��ȡ��֤��
	 *
	 * @return string
	 */
	public function getCode()
	{
		$code = strtolower($this->code);

		return $code;
	}
}