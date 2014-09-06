<?php
/**
 * File class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Cache
 * @since          1.0
 */
namespace RCPHP\Cache;

defined('IN_RCPHP') or exit('Access denied');

class File
{

	/**
	 * �����ļ�·��
	 *
	 * @var string
	 */
	private $path;

	/**
	 * ���췽��
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->path = CACHE_PATH;

		$this->set_cache_path($this->path);
	}

	/**
	 * ���û�������
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $expire
	 * @return bool
	 */
	public function set($key, $value, $expire = 60)
	{
		if(empty($key))
		{
			return false;
		}

		$tmpDir = $this->path . $key . '.cae';

		return \RCPHP\Util\File::writeFile($tmpDir, '<?php exit;?>' . time() . '(' . $expire . ')' . serialize($value));
	}

	/**
	 * ��ȡ��������
	 *
	 * @param string $filename
	 * @return string
	 */
	public function get($key)
	{
		if(empty($key))
		{
			return false;
		}

		$tmpDir = $this->path . $key . '.cae';

		if(!file_exists($tmpDir))
		{
			return false;
		}

		$cacheData = file_get_contents($tmpDir);

		/**
		 * ��ȡ�ļ�����ʱ��
		 */
		$fileTime = substr($cacheData, 13, 10);

		/**
		 * ��ȡ����ʱ��
		 */
		$pos = strpos($cacheData, ')');
		$cacheTime = substr($cacheData, 24, $pos - 24);

		/**
		 * ���ǻ������� ���л���
		 */
		$cacheData = substr($cacheData, $pos + 1);

		if($cacheTime == 0)
		{
			return unserialize($cacheData);
		}

		if(time() > ($fileTime + $cacheTime))
		{
			unlink($key);

			return false;
		}

		return unserialize($cacheData);
	}

	/**
	 * ɾ����������
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete($key)
	{
		if(empty($key))
		{
			return false;
		}

		$fileName = $this->path . $key . '.cae';

		\RCPHP\Util\File::delete($fileName);

		return true;
	}

	/**
	 * ��������ļ����� ����
	 *
	 * @return bool
	 */
	public function clear()
	{
		@set_time_limit(3600);

		return \RCPHP\Util\File::clear($this->path);
	}

	/**
	 * �жϻ��������Ƿ����
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		if(empty($key))
		{
			return false;
		}

		$tmpDir = $this->path . $key . '.cae';

		return file_exists($tmpDir);
	}
}