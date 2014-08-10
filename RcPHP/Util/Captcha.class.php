<?php
/**
 * Captcha class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Util
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Captcha
{

	/**
	 * ��֤��
	 *
	 * @var string
	 */
	private $code;

	/**
	 * ��֤�볤��
	 *
	 * @var int
	 */
	private $codelen = 4;

	/**
	 * ���
	 *
	 * @var int
	 */
	private $width = 80;

	/**
	 * �߶�
	 *
	 * @var int
	 */
	private $height = 30;

	/**
	 * ͼ����Դ���
	 *
	 * @var object
	 */
	private $img;

	/**
	 * ָ��������
	 *
	 * @var string
	 */
	private $font;

	/**
	 * ָ�������С
	 *
	 * @var int
	 */
	private $fontsize = 20;

	/**
	 * ָ��������ɫ
	 *
	 * @var string
	 */
	private $fontcolor;

	/**
	 * �����ַ���
	 *
	 * @var string
	 */
	private $key = "RcPHP.2345";

	/**
	 * ����ʱ��
	 *
	 * @var int
	 */
	private $expire = 1200;

	/**
	 * ���췽��
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->font = RCPHP_PATH . 'Tpl' . DS . 'ttfs' . DS . 'elephant.ttf';
	}

	/**
	 * Set the font
	 *
	 * @param $fontName
	 * @return $this
	 */
	public function setFont($fontName)
	{
		$this->font = RCPHP_PATH . 'Tpl' . DS . 'ttfs' . DS . $fontName . '.ttf';

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
	 * ������֤�볤��
	 *
	 * @param int $len
	 * @return $this
	 */
	public function setLength($len)
	{
		if(!empty($len))
		{
			$this->codelen = (int)$len;
		}

		return $this;
	}

	/**
	 * ���������
	 *
	 * @return void
	 */
	private function createCode()
	{
		String::rand_string($this->codelen);
	}

	/**
	 * ���ɱ���
	 *
	 * @return void
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
	 * @return void
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

		$key = $this->authcode($this->key);

		$code = array();
		$code['verify_code'] = $this->authcode(strtolower($this->code)); // ��У���뱣�浽session
		$code['verify_time'] = time(); // ��֤�봴��ʱ��

		$session = RcPHP::import("Util/Session");
		$session->set($key, $code);
		unset($session);

		header('Content-type:image/png');
		header("Expires: -1");
		header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0");
		header("Pragma: no-cache");
		imagepng($this->img);
		imagedestroy($this->img);
	}

	/**
	 * ��֤
	 *
	 * @param string $code
	 * @return bool
	 */
	public function check($code)
	{
		if(empty($code))
		{
			return false;
		}

		$key = $this->authcode($this->key);

		$session = RcPHP::import("Util/Session");
		$secode = $session->get($key);

		if(empty($key))
		{
			return false;
		}

		if(time() - $secode['verify_time'] > $this->expire)
		{
			$session->set($key, null);
			unset($session);

			return false;
		}

		if($session['verify_code'] == $this->authcode(strtolower($code)))
		{
			$session->set($key, null);
			unset($session);

			return true;
		}

		unset($session);

		return false;
	}

	/**
	 * ��������
	 *
	 * @param stirng $str
	 * @return string
	 */
	private function authcode($str)
	{
		$key = substr(md5($this->key), 5, 8);
		$str = substr(md5($str), 8, 10);

		return md5($key . $str);
	}
}