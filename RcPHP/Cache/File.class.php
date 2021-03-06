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
	 * 缓存文件路径
	 *
	 * @var string
	 */
	private $path;

	/**
	 * 构造方法
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->path = CACHE_PATH;

		$this->set_cache_path($this->path);
	}

	/**
	 * 设置缓存数据
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
	 * 获取缓存数据
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
		 * 截取文件创建时间
		 */
		$fileTime = substr($cacheData, 13, 10);

		/**
		 * 截取过期时间
		 */
		$pos = strpos($cacheData, ')');
		$cacheTime = substr($cacheData, 24, $pos - 24);

		/**
		 * 真是缓存数据 序列化后
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
	 * 删除缓存数据
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
	 * 清除所有文件缓存 慎用
	 *
	 * @return bool
	 */
	public function clear()
	{
		@set_time_limit(3600);

		return \RCPHP\Util\File::clear($this->path);
	}

	/**
	 * 判断缓存数据是否存在
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