<?php

/**
 * Kit.
 *
 * Config library.
 *
 * @version $Id: config.php 33 2010-06-13 23:29:05Z eprev $
 * @package System
 */

class System_Config implements Iterator, Countable
{
	/**
	 * @var object  Class instance.
	 */
	protected static $instance;

	/**
	 * Returns a instance of class.
	 *
	 * @param string $name    Instance name.
	 * @param array  $config  Configuration array.
	 * @return object
	 */
	public static function getInstance()
	{
		if (null === self::$instance) {
			include APPLICATION_PATH . 'config.php';
			$data = get_defined_vars();
			self::$instance = new static($data);
		}
		return self::$instance;
	}

	/**
	 * @var array  Configuration data.
	 */
	protected $data = array();

	/**
	 * @var boolean  Whether modifications are allowed?
	 */
	protected $readOnly;

	/**
	 * Class constructor.
	 *
	 * @param array   $data      Configuration data.
	 * @param boolean $readOnly  Whether modifications are allowed.
	 * @return this
	 * @throws InvalidArgumentException
	 */
	public function __construct(array $data = array(), $readOnly = false)
	{
		$this->readOnly = $readOnly;
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$this->data[$key] = new static($value, $this->readOnly);
			} else {
				$this->data[$key] = $value;
			}
		}
	}

	/**
	 * Turns instance and children to readonly.
	 *
	 * @return this
	 */
	public function readOnly()
	{
		foreach ($this->data as $key => $value) {
			if ($value instanceof System_Config) {
				$value->readOnly();
			}
		}
		$this->readOnly = true;
		return $this;
	}

	/**
	 * Returns a value by the given path (properties separated by dots).
	 * Throws InvalidArgumentException if the path does not exist.
	 *
	 * @param string $path  Path.
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function get($path)
	{
		$pos = strpos($path, '.');
		if (false === $pos) {
			return $this->__get($path);
		}
		$property = substr($path, 0, $pos);
		if (array_key_exists($property, $this->data)) {
			if ($this->data[$property] instanceof System_Config) {
				$tail = substr($path, $pos + 1);
				return $this->data[$property]->get($tail);
			} else {
				throw new InvalidArgumentException('Property "' . $property . '" does not have children.');
			}
		} else {
			throw new InvalidArgumentException('Property "' . $property . '" does not exist.');
		}
	}

	/**
	 * Sets an value to the given path (properties separated by dots).
	 * Throws RuntimeException if modifications are disallowed.
	 * Throws InvalidArgumentException if the path does not exist.
	 *
	 * @param string $path  Path or array($path => $value).
	 * @param mixed  $value Value to set.
	 * @return mixed
	 * @throws RuntimeException,InvalidArgumentException
	 */
	public function set($path, $value = null)
	{
		if (is_array($path)) {
			foreach ($path as $k => $v) {
				$this->set($k, $v);
			}
		} else {
			$pos = strpos($path, '.');
			if (false === $pos) {
				return $this->__set($path, $value);
			}
			$property = substr($path, 0, $pos);
			if (array_key_exists($property, $this->data)) {
				if ($this->data[$property] instanceof System_Config) {
					$tail = substr($path, $pos + 1);
					return $this->data[$property]->set($tail, $value);
				} else {
					throw new InvalidArgumentException('Property "' . $property . '" does not have children.');
				}
			} else {
				if ($this->readOnly) {
					throw new RuntimeException('Instance is read only.');
				}
				$this->data[$property] = new static();
				$tail = substr($path, $pos + 1);
				return $this->data[$property]->set($tail, $value);
			}
		}
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Returns a value of the specified property.
	 * Throws InvalidArgumentException if the property does not exist.
	 *
	 * @param string $property  Property name.
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function __get($property)
	{
		if (array_key_exists($property, $this->data)) {
			return $this->data[$property];
		} else {
			throw new InvalidArgumentException('Property "' . $property . '" does not exist.');
		}
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Sets an value of the specified property.
	 * Throws RuntimeException if modifications are disallowed.
	 *
	 * @param string $property  Property name.
	 * @param mixed  $value     Value to set.
	 * @return void
	 * @throws RuntimeException
	 */
	public function __set($property, $value)
	{
		if ($this->readOnly) {
			throw new RuntimeException('Instance is read only.');
		}
		if (is_array($value)) {
			$this->data[$property] = new static($value);
		} else {
			$this->data[$property] = $value;
		}
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Checks whether the given property exist?
	 *
	 * @param string $property  Property name.
	 * @return boolean
	 */
	public function __isset($property)
	{
		return isset($this->data[$property]);
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Unset the specified property.
	 * Throws RuntimeException if modifications are disallowed.
	 *
	 * @param string $property  Property name.
	 * @return void
	 * @throws RuntimeException
	 */
	public function __unset($property)
	{
		if ($this->readOnly) {
			throw new RuntimeException('Instance is read only.');
		}
		unset($this->data[$property]);
	}

	/**
	 * Defined by Countable interface.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->data);
	}

	/**
	 * Defined by Iterator interface.
	 */
	public function current()
	{
		return current($this->data);
	}

	/**
	 * Defined by Iterator interface.
	 */
	public function key()
	{
		return key($this->data);
	}

	/**
	 * Defined by Iterator interface.
	 */
	public function next()
	{
		next($this->data);
	}

	/**
	 * Defined by Iterator interface.
	 */
	public function rewind()
	{
		reset($this->data);
	}

	/**
	 * Defined by Iterator interface.
	 */
	public function valid()
	{
		return (false !== current($this->data));
	}

	/**
	 * Returns a copy of the instance with existent default values.
	 *
	 * @param array   $data     Default configuration.
	 * @param boolean $readOnly Whether modifications are allowed in the cloned object (NULL to inherit value).
	 * @return System_Config
	 */
	public function cloneWith(array $data = array(), $readOnly = null)
	{
		$data = array_replace_recursive($data, $this->arrayize());
		return new static($data, null === $readOnly ? $this->readOnly : $readOnly);
	}

	/**
	 * Returns configuration data as an associative array.
	 *
	 * @return array
	 */
	public function arrayize()
	{
		$res = array();
		foreach ($this->data as $key => $value) {
			if ($value instanceof System_Config) {
				$res[$key] = $value->arrayize();
			} else {
				$res[$key] = $value;
			}
		}
		return $res;
	}
}

/* Local Variables:	   */
/* tab-width: 4		   */
/* indent-tabs-mode: t */
/* End:                */
