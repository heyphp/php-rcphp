<?php
/**
 * Upload class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Net
 * @since          1.0
 */
namespace RCPHP\Net;

defined('IN_RCPHP') or exit('Access denied');

class Upload
{

	/**
	 * File upload max size.
	 *
	 * @var int
	 */
	private $allowFileSize;

	/**
	 * Check allow upload mime type.
	 *
	 * @var array
	 */
	private $checkAllowType = array();

	/**
	 * Allow upload mime type.
	 *
	 * @var array
	 */
	private $allowType = array();

	/**
	 * Upload file error message.
	 *
	 * @var string
	 */
	private $error = '';

	/**
	 * File extension name.
	 *
	 * @var string
	 */
	public $ext;

	/**
	 * Upload file name.
	 *
	 * @var string
	 */
	public $fileName;

	/**
	 * __construct
	 *
	 * @return boolean
	 */
	public function __construct()
	{
		// Set default upload file size.
		$this->allowFileSize = 2097152;

		\RCPHP\Debug::addMessage('Upload Class Initialized');

		return true;
	}

	/**
	 * Additional allowed to upload the file type.
	 *
	 * @param array $file
	 * @return $this
	 */
	public function setAllowType($type = array())
	{
		if(empty($type) || !is_array($type))
		{
			return false;
		}

		foreach($type as $key => $val)
		{
			$this->allowType[$key] = $val;
		}

		return $this;
	}

	/**
	 * Setting allows the upload file type.
	 *
	 * @param array $type
	 * @return $this
	 */
	public function setCheckAllowType(array $type)
	{
		if(empty($type))
		{
			return $this;
		}

		$this->checkAllowType = $type;

		return $this;
	}

	/**
	 * Setting allows the upload file type.
	 *
	 * @param int $size
	 * @return $this|bool
	 */
	public function setAllowFileSize($size)
	{
		$this->allowFileSize = intval($size);

		return $this;
	}

	/**
	 * Check upload file mime type.
	 *
	 * @return bool
	 */
	protected function checkFileMimeType($mime)
	{
		if(empty($this->checkAllowType) || empty($mime))
		{
			return false;
		}

		$this->allowType['ppt'] = "application/vnd.ms-powerpoint";
		$this->allowType['xls'] = "application/vnd.ms-excel";
		$this->allowType['xlsx'] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
		$this->allowType['doc'] = "application/msword";
		$this->allowType['docx'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
		$this->allowType['rar'] = "application/octet-stream";
		$this->allowType['pdf'] = "application/pdf";
		$this->allowType['zip'] = "application/x-zip-compressed";
		$this->allowType['gif'] = "image/gif";
		$this->allowType['jpeg'] = "image/pjpeg";
		$this->allowType['jpg'] = "image/jpeg";
		$this->allowType['jpe'] = "image/pjpeg";
		$this->allowType['bmp'] = "image/bmp";
		$this->allowType['png'] = "image/png";
		$this->allowType['txt'] = "text/plain";

		$mimeTypeKey = array_keys($this->allowType);

		foreach($this->checkAllowType as $type)
		{
			if(!in_array($type, $mimeTypeKey))
			{
				return false;
			}
		}

		$allowTypeArray = array();
		foreach($this->checkAllowType as $type)
		{
			$allowTypeArray[] = $this->allowType[$type];
		}

		if(!in_array($mime, $allowTypeArray))
		{
			return false;
		}

		$this->ext = array_search($mime, $this->allowType);

		return true;
	}

	/**
	 * Set upload error message.
	 *
	 * @param string $message
	 * @return void
	 */
	public function error($message = '未知错误，上传文件失败')
	{
		$this->error = $message;
	}

	/**
	 * Upload.
	 *
	 * @param string $fileUpload
	 * @param string $fileName
	 * @return bool
	 */
	public function doUpload($fileUpload, $fileName)
	{
		if(empty($fileUpload) || empty($fileName))
		{
			return false;
		}

		$uploadObject = null;

		if(is_array($fileUpload))
		{
			$uploadObject = $fileUpload;
		}
		else
		{
			$uploadObject = $_FILES[$fileUpload];
		}

		// Upload error
		if(!empty($uploadObject['error']))
		{
			switch($uploadObject['error'])
			{
				case 1:
				case 2:
					$this->error('文件大小超出限制，上传文件失败');
					break;
				case 3:
					$this->error('文件上传不全，上传文件失败');
					break;
				case 4:
					$this->error('没有找到要上传的文件，上传文件失败');
					break;
				case 5:
					$this->error('服务器临时文件夹错误，上传文件失败');
					break;
				case 6:
					$this->error('文件写入错误，上传文件失败');
					break;
			}

			return false;
		}

		// Check upload file mime type.
		if($this->checkFileMimeType($uploadObject['type']) === false)
		{
			$this->error('文件MIME类型错误，上传文件失败');

			return false;
		}

		// Check upload file size.
		if($this->allowFileSize < intval($uploadObject['size']))
		{
			$this->error('文件大小超出限制，上传文件失败');

			return false;
		}

		// Retrieves the extension.
		if(empty($this->ext))
		{
			$this->ext = pathinfo($uploadObject['name'], PATHINFO_EXTENSION);
		}

		if(!move_uploaded_file($uploadObject['tmp_name'], $fileName . '.' . $this->ext))
		{
			$this->error();

			return false;
		}

		$this->fileName = $fileName . '.' . $this->ext;

		unset($uploadObject);

		return true;
	}
}