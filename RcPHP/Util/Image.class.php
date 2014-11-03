<?php
/**
 * Image class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Util
 * @since          1.0
 */
namespace RCPHP\Util;

defined('IN_RCPHP') or exit('Access denied');

class Image
{

	/**
	 * 原图片路径,在水印图片时指水印图片.
	 *
	 * @var string
	 */
	public $imageUrl;

	/**
	 * 字体名称
	 *
	 * @var string
	 */
	public $fontName;

	/**
	 * 字体大小
	 *
	 * @var int
	 */
	public $fontSize;

	/**
	 * 图片实例化名称
	 *
	 * @var object
	 */
	protected $image;

	/**
	 * 图象宽度
	 *
	 * @var int
	 */
	protected $width;

	/**
	 * 图象高度
	 *
	 * @var int
	 */
	protected $height;

	/**
	 * 图片格式, 如:jpeg, gif, png
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * 文字的横坐标
	 *
	 * @var int
	 */
	public $fontX;

	/**
	 * 文字的纵坐标
	 *
	 * @var int
	 */
	public $fontY;

	/**
	 * 字体颜色
	 *
	 * @var string
	 */
	protected $fontColor;

	/**
	 * 生成水印图片的原始图片的宽度
	 *
	 * @var int
	 */
	protected $imageWidth;

	/**
	 * 生成水印图片的原始图片的高度
	 *
	 * @var int
	 */
	protected $imageHeight;

	/**
	 * 生成缩略图的实际宽度
	 *
	 * @var int
	 */
	protected $widthNew;

	/**
	 * 生成缩略图的实际高度
	 *
	 * @var int
	 */
	protected $heightNew;

	/**
	 * 水印图片的实例化对象
	 *
	 * @var object
	 */
	protected $waterImage;

	/**
	 * 生成水印区域的横坐标
	 *
	 * @var int
	 */
	protected $waterX;

	/**
	 * 生成水印区域的纵坐标
	 *
	 * @var int
	 */
	protected $waterY;

	/**
	 * 生成水印图片的水印区域的透明度
	 *
	 * @var int
	 */
	protected $alpha;

	/**
	 * 文字水印字符内容
	 *
	 * @var string
	 */
	protected $textContent;

	/**
	 * 水印图片的宽度
	 *
	 * @var int
	 */
	protected $waterWidth;

	/**
	 * 水印图片的高度
	 *
	 * @var int
	 */
	protected $waterHeight;

	/**
	 * 构造函数
	 *
	 * @return void
	 */
	public function __construct()
	{

		$this->fontSize = 14;
		$this->fontName = RCPHP_PATH . 'sources/font/aispec.ttf';
	}

	/**
	 * 初始化运行环境,获取图片格式并实例化.
	 *
	 * @param string $url
	 * @return bool
	 */
	protected function parseImageInfo($url)
	{
		list ($this->imageWidth, $this->imageHeight, $type) = getimagesize($url);

		$typeArr = array(
			1 => 'gif',
			2 => 'jpg',
			3 => 'png'
		);
		if(array_key_exists($type, $typeArr))
		{
			$this->type = $typeArr[$type];

			$imageSource = file_get_contents($url);
			$this->image = imagecreatefromstring($imageSource);
		}
		else
		{
			return false;
		}

		return true;
	}

	/**
	 * 设置字体名称.
	 *
	 * @param string  $name
	 * @param integer $size
	 * @return $this
	 */
	public function setFontName($name, $size = null)
	{
		if(!empty($name))
		{
			$this->fontName = $name;
		}

		if(!is_null($size))
		{
			$this->fontSize = (int)$size;
		}

		return $this;
	}

	/**
	 * 设置字体大小.
	 *
	 * @param integer $size
	 * @return $this
	 */
	public function setFontSize($size)
	{

		if(!empty($size))
		{
			$this->fontSize = intval($size);
		}

		return $this;
	}

	/**
	 * 获取颜色参数.
	 *
	 * @param integer $r
	 * @param integer $g
	 * @param integer $b
	 * @return $this
	 */
	public function setFontColor($r = false, $g = false, $b = false)
	{

		$this->fontColor = (is_int($r) && is_int($g) && is_int($b)) ? array(
			$r,
			$g,
			$b
		) : array(
			255,
			255,
			255
		);

		return $this;
	}

	/**
	 * 水印图片的URL.
	 *
	 * @param string $url
	 * @return $this
	 */
	public function setImageUrl($url)
	{
		if(!empty($url))
		{
			$this->imageUrl = $url;
		}

		return $this;
	}

	/**
	 * 设置生成图片的大小.
	 *
	 * @param integer $width
	 * @param integer $height
	 * @return $this
	 */
	public function setImageSize($width, $height)
	{
		if(!empty($width))
		{
			$this->width = intval($width);
		}
		if(!empty($height))
		{
			$this->height = intval($height);
		}

		return $this;
	}

	/**
	 * 设置文字水印字符串内容.
	 *
	 * @param string $content
	 * @return $this
	 */
	public function setTextContent($content)
	{
		if(!empty($content))
		{
			$this->textContent = $content;
		}

		return $this;
	}

	/**
	 * 设置文字水印图片文字的坐标位置.
	 *
	 * @param integer $x
	 * @param integer $y
	 * @return $this
	 */
	public function setTextPosition($x, $y)
	{
		if(!empty($x))
		{
			$this->fontX = intval($x);
		}
		if(!empty($y))
		{
			$this->fontY = intval($y);
		}

		return $this;
	}

	/**
	 * 设置水印图片水印的坐标位置.
	 *
	 * @param integer $x
	 * @param integer $y
	 * @return $this
	 */
	public function setWatermarkPosition($x, $y)
	{
		if(!empty($x))
		{
			$this->waterX = intval($x);
		}
		if(!empty($y))
		{
			$this->waterY = intval($y);
		}

		return $this;
	}

	/**
	 * 设置水印图片水印区域的透明度.
	 *
	 * @param integer $param
	 * @return $this
	 */
	public function setWatermarkAlpha($param)
	{
		if(!empty($param))
		{
			$this->alpha = intval($param);
		}

		return $this;
	}

	/**
	 * 调整文字水印区域的位置
	 *
	 * @param bool $limitOption
	 * @return bool
	 */
	protected function handleWatermarkFontPlace($limitOption = false)
	{
		if(!$this->fontX || !$this->fontY)
		{
			if(!$this->textContent)
			{
				Controller::halt('The watermark text set the image error');
			}

			$bbox = imagettfbbox($this->fontSize, 0, $this->fontName, $this->textContent);

			//文字margin_right为5px,特此加5
			$fontW = $bbox[2] - $bbox[0] + 5;
			$fontH = abs($bbox[7] - $bbox[1]);

			if($limitOption === true && $this->heightNew && $this->heightNew)
			{

				$this->fontX = ($this->widthNew > $fontW) ? $this->widthNew - $fontW : 0;
				$this->fontY = ($this->heightNew > $fontH) ? $this->heightNew - $fontH : 0;
			}
			else
			{

				$this->fontX = ($this->imageWidth > $fontW) ? $this->imageWidth - $fontW : 0;
				$this->fontY = ($this->imageHeight > $fontH) ? $this->imageHeight - $fontH : 0;
			}
		}

		return true;
	}

	/**
	 * 常设置的文字颜色转换为图片信息.
	 *
	 * @return bool
	 */
	protected function handleFontColor()
	{

		if(empty($this->fontColor))
		{
			$this->fontColor = array(
				255,
				255,
				255
			);
		}

		return imagecolorallocate($this->image, $this->fontColor[0], $this->fontColor[1], $this->fontColor[2]);
	}

	/**
	 * 根据图片原来的宽和高的比例,自适应性处理缩略图的宽度和高度
	 *
	 * @param bool $scale
	 * @return bool
	 */
	protected function handleImageSize($scale = true)
	{
		//当没有所生成的图片的宽度和高度设置时.
		if(!$this->width || !$this->height)
		{
			Controller::halt('The height or width size set the image error');
		}

		if($scale !== true)
		{
			$this->widthNew = $this->width;
			$this->heightNew = $this->height;
		}
		else
		{

			$perW = $this->width / $this->imageWidth;
			$perH = $this->height / $this->imageHeight;
			if(ceil($this->imageHeight * $perW) > $this->height)
			{
				$this->widthNew = ceil($this->imageWidth * $perH);
				$this->heightNew = $this->height;
			}
			else
			{
				$this->widthNew = $this->width;
				$this->heightNew = ceil($this->imageHeight * $perW);
			}
		}

		return true;
	}

	/**
	 * 生成图片的缩略图.
	 *
	 * @param string $url
	 * @param string $distName
	 * @param bool   $scale
	 * @return bool
	 */
	public function makeLimitImage($url, $distName = null, $scale = true)
	{
		//原图片分析.
		$this->parseImageInfo($url);
		$this->handleImageSize($scale);

		//新图片分析.
		$imageDist = imagecreatetruecolor($this->widthNew, $this->heightNew);

		//生成新图片.
		imagecopyresampled($imageDist, $this->image, 0, 0, 0, 0, $this->widthNew, $this->heightNew, $this->imageWidth, $this->imageHeight);

		$this->createImage($imageDist, $distName, $this->type);
		imagedestroy($imageDist);
		imagedestroy($this->image);

		return true;
	}

	/**
	 * 生成目标图片.
	 *
	 * @param string $imageDist
	 * @param string $distName
	 * @param string $imageType
	 * @return bool
	 */
	protected function createImage($imageDist, $distName = null, $imageType = 'jpg')
	{

		//参数分析
		if(!$imageDist || !$imageType)
		{
			return false;
		}

		if(!is_null($distName))
		{
			switch($imageType)
			{

				case 'gif':
					imagegif($imageDist, $distName);
					break;

				case 'jpg':
					imagejpeg($imageDist, $distName);
					break;

				case 'png':
					imagepng($imageDist, $distName);
					break;

				case 'bmp':
					imagewbmp($imageDist, $distName);
					break;
			}
		}
		else
		{
			switch($imageType)
			{

				case 'gif':
					header('Content-type:image/gif');
					imagegif($imageDist);
					break;

				case 'jpg':
					header('Content-type:image/jpeg');
					imagejpeg($imageDist);
					break;

				case 'png':
					header('Content-type:image/png');
					imagepng($imageDist);
					break;

				case 'bmp':
					header('Content-type:image/png');
					imagewbmp($imageDist);
					break;
			}
		}

		return true;
	}

	/**
	 * 生成文字水印图片.
	 *
	 * @param string $imageUrl
	 * @param string $distName
	 * @return bool
	 */
	public function makeTextWatermark($imageUrl, $distName = null)
	{
		//分析原图片.
		$this->parseImageInfo($imageUrl);

		//当所要生成的文字水印图片有大小尺寸限制时(缩略图功能)
		if($this->width && $this->height)
		{

			$this->handleImageSize();
			//新图片分析.
			$imageDist = imagecreatetruecolor($this->widthNew, $this->heightNew);

			//生成新图片.
			imagecopyresampled($imageDist, $this->image, 0, 0, 0, 0, $this->widthNew, $this->heightNew, $this->imageWidth, $this->imageHeight);

			//所生成的图片进行分析.
			$this->handleWatermarkFontPlace(true);

			$fontColor = $this->handleFontColor();

			//生成新图片.
			imagettftext($imageDist, $this->fontSize, 0, $this->fontX, $this->fontY, $fontColor, $this->fontName, $this->textContent);
			$this->createImage($imageDist, $distName, $this->type);
			imagedestroy($imageDist);
		}
		else
		{

			//所生成的图片进行分析.
			$this->handleWatermarkFontPlace();

			$fontColor = $this->handleFontColor();

			//生成新图片.
			imagettftext($this->image, $this->fontSize, 0, $this->fontX, $this->fontY, $fontColor, $this->fontName, $this->textContent);
			$this->createImage($this->image, $distName, $this->type);
		}

		imagedestroy($this->image);

		return true;
	}

	/**
	 * 获取水印图片信息
	 *
	 * @return bool
	 */
	protected function handleWatermarkImage()
	{

		if($this->image && !$this->waterImage)
		{
			if(empty($this->imageUrl))
			{
				Controller::halt('The watermark image is not set');
			}

			$waterUrl = $this->imageUrl;

			list ($this->waterWidth, $this->waterHeight, $type) = getimagesize($waterUrl);

			switch($type)
			{

				case 1:
					$this->waterImage = imagecreatefromgif($waterUrl);
					break;

				case 2:
					$this->waterImage = imagecreatefromjpeg($waterUrl);
					break;

				case 3:
					$this->waterImage = imagecreatefrompng($waterUrl);
					break;

				case 4:
					$this->waterImage = imagecreatefromwbmp($waterUrl);
					break;
			}
		}

		return true;
	}

	/**
	 * 调整水印区域的位置,默认位置距图片右下角边沿5像素.
	 *
	 * @return bool
	 */
	protected function handleWatermarkImagePlace($limitOption = false)
	{
		if(!$this->waterX || !$this->waterY)
		{

			if($limitOption === true && $this->widthNew && $this->heightNew)
			{

				$this->waterX = ($this->widthNew - 5 > $this->waterWidth) ? $this->widthNew - $this->waterWidth - 5 : 0;
				$this->waterY = ($this->heightNew - 5 > $this->waterHeight) ? $this->heightNew - $this->waterHeight - 5 : 0;
			}
			else
			{

				$this->waterX = ($this->imageWidth - 5 > $this->waterWidth) ? $this->imageWidth - $this->waterWidth - 5 : 0;
				$this->waterY = ($this->imageHeight - 5 > $this->waterHeight) ? $this->imageHeight - $this->waterHeight - 5 : 0;
			}
		}

		return true;
	}

	/**
	 * 生成图片水印.
	 *
	 * @param string $imageUrl
	 * @param string $distName
	 * @return bool
	 */
	public function makeImageWatermark($imageUrl, $distName = null)
	{

		//分析图片信息.
		$this->parseImageInfo($imageUrl);

		//水印图片的透明度参数
		$this->alpha = empty($this->alpha) ? 85 : $this->alpha;

		//对水印图片进行信息分析.
		$this->handleWatermarkImage();

		if($this->width && $this->height)
		{

			$this->handleImageSize();
			//新图片分析.
			$imageDist = imagecreatetruecolor($this->widthNew, $this->heightNew);

			//生成新图片.
			imagecopyresampled($imageDist, $this->image, 0, 0, 0, 0, $this->widthNew, $this->heightNew, $this->imageWidth, $this->imageHeight);

			//分析新图片的水印位置.
			$this->handleWatermarkImagePlace(true);

			//生成新图片.
			imagecopymerge($imageDist, $this->waterImage, $this->waterX, $this->waterY, 0, 0, $this->waterWidth, $this->waterHeight, $this->alpha);
			$this->createImage($imageDist, $distName, $this->type);
			imagedestroy($imageDist);
		}
		else
		{

			//分析新图片的水印位置.
			$this->handleWatermarkImagePlace();

			//生成新图片.
			imagecopymerge($this->image, $this->waterImage, $this->waterX, $this->waterY, 0, 0, $this->waterWidth, $this->waterHeight, $this->alpha);
			$this->createImage($this->image, $distName, $this->type);
		}

		imagedestroy($this->image);
		imagedestroy($this->waterImage);

		return true;
	}
}