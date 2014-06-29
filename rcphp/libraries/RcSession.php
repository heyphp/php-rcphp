<?php
/**
 * RcSession class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcSession extends RcBase
{

	/**
	 * Open session
	 *
	 * @return void
	 */
	public function __construct()
	{
		session_start();
		register_shutdown_function(array(
										$this,
										'close'
								   ));
	}

	/**
	 * Set session data
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return void
	 */
	public function set($key, $val)
	{
		$_SESSION[$key] = $val;
	}

	/**
	 * Get session data
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		if(isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}

		return false;
	}

	/**
	 * Delete the session data parameter to null to empty the session
	 *
	 * @param string $key
	 * @return void
	 */
	public function delete($key = null)
	{
		if(!is_null($key))
		{
			$_SESSION[$key] = null;
			unset($_SESSION[$key]);
		}

		$_SESSION = array();
	}

	/**
	 * Cancel session
	 *
	 * @return void
	 */
	public function destory()
	{
		if(session_id())
		{
			unset($_SESSION);
			session_destroy();
		}
	}

	/**
	 * Program at the end of the session writing is prohibited.
	 *
	 * @return void
	 */
	public function close()
	{
		if(session_id())
		{
			session_write_close();
		}
	}

	/**
	 * Exists session
	 *
	 * @param string $key
	 * @return bool
	 */
	public function is_set($key)
	{
		if(session_id())
		{
			return array_key_exists($key, $_SESSION);
		}

		return false;
	}

	/**
	 * Session is start
	 *
	 * @return bool
	 */
	public function is_start()
	{
		return session_id() ? true : false;
	}

	/**
	 * Session storage paths
	 *
	 * @param string $type
	 * @param string $path
	 * @return bool|string
	 */
	public function save_path($type = "get", $path = null)
	{
		if($type === "get")
		{
			return session_save_path();
		}
		else
		{
			if(is_dir($path))
			{
				return session_save_path($path);
			}

			return false;
		}
	}

	/**
	 * Session timeout
	 *
	 * @param string $type
	 * @param string $time
	 * @return bool|int|string
	 */
	public function timeout($type = "get", $time = null)
	{
		if($type === "get")
		{
			return intval(ini_get('session.gc_maxlifetime'));
		}
		else
		{
			if(!is_null($time))
			{
				return ini_set('session.gc_maxlifetime', intval($time));
			}

			return false;
		}
	}

	/**
	 * Seesion id
	 *
	 * @param string $type
	 * @param string $id
	 * @return bool|string
	 */
	public function session_id($type = "get", $id = null)
	{
		if($type === "get")
		{
			return session_id();
		}
		else
		{
			if(!is_null($id))
			{
				return session_id($id);
			}

			return false;
		}
	}

	/**
	 * Seesion name
	 *
	 * @param string $type
	 * @param string $name
	 * @return bool|string
	 */
	public function session_name($type = "get", $name = null)
	{
		if($type === "get")
		{
			return session_name();
		}
		else
		{
			if(!is_null($name))
			{
				return session_name($name);
			}

			return false;
		}
	}
} 