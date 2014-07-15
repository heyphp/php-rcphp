<?php
/**
 * Base class file
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Core
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

abstract class Base
{

	/**
	 * Set variable
	 *
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		if(property_exists($this, $name))
		{
			$this->$name = $value;
		}
	}

	/**
	 * Get variable
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if(isset($this->$name))
		{
			return $this->$name;
		}
		else
		{
			return false;
		}
	}
}