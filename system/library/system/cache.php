<?php

/**
 * Kit.
 *
 * Cache library.
 *
 * @version $Id: cache.php 54 2010-06-24 06:54:24Z eprev $
 * @package System
 */

/**
 * Cache driver interface.
 *
 * @package Cache
 */
interface Cache_Driver
{
	/**
	 * Constructor.
	 *
	 * @param System_Config $config Cache config.
	 * @return this
	 */
	public function __construct(System_Config $config);

	/**
	 * Store data at the server.
	 *
	 * @param string  $key     The key that will be associated with the item.
	 * @param mixed   $value   The variable to store.
	 * @param integer $expire  Expiration time of the item.
	 * @return boolean
	 */
	public function set($key, $value, $expire);

	/**
	 * Fetchs item from the server.
	 *
	 * Returns FALSE on failure or if given key was not found.
	 *
	 * @param mixed $key  The key or arrays of keys to fetch.
	 * @return mixed
	 */
	public function get($key);

	/**
	 * Deletes item from the server.
	 *
	 * @param string $key  The key to delete.
	 * @return boolean
	 */
	public function delete($key);
}

/**
 * Cache library.
 *
 * @package Cache
 */
class System_Cache
{
	/**
	 * @var array  Class instances.
	 */
	protected static $instances = array();

	/**
	 * Returns an instance of class.
	 *
	 * @param string $profile Config profile.
	 * @param mixed  $config  Config data (optional).
	 * @return object
	 * @throws InvalidArgumentError
	 */
	public static function getInstance($profile = 'default', $config = null)
	{
		if (false == isset(self::$instances[$profile])) {
			if (null == $config) {
				$config = Config::getInstance()->cache->$profile;
			}
			self::$instances[$profile] = new static($config);
		}
		return self::$instances[$profile];
	}

	/**
	 * @var Cache_Driver  Cache driver.
	 */
	protected $driver;

	/**
	 * @var array  Cache for tags.
	 */
	private $tags = array();

	/**
	 * Class constructor.
	 *
	 * Options is an associative arraya or the URI string.
	 * The URI can specify a local or remote provider.
	 *
	 *   file:///path/to/cache/file
	 *   memcached://localhost:11211/?expire=3600
	 *
	 * @param mixed $config  Cache options (array or string path).
	 * @return this
	 * @throws InvalidArgumentException
	 */
	public function __construct($config)
	{
		if (false === is_array($config) && is_string($config) === false) {
			throw new InvalidArgumentException('Config expected to be an associative array or the URI string.');
		}
		if (is_string($config)) {
			$cmps = parse_url($config);
			$data = array();
			if (array_key_exists('scheme', $cmps)) {
				$data['driver'] = $cmps['scheme'];
			}
			if (array_key_exists('host', $cmps)) {
				$data['host'] = $cmps['host'];
			}
			if (array_key_exists('port', $cmps)) {
				$data['port'] = $cmps['port'];
			}
			if (array_key_exists('query', $cmps)) {
				parse_str($cmps['query'], $vars);
				$data = array_replace($data, $vars);
			}
			$config = new Config($data);
		}

		$driverClass = 'Cache_' . $config->driver;
		$this->driver = new $driverClass($config);
	}

	/**
	 * Put data in the cache.
	 *
	 * @param string  $key     The key that will be associated with the item.
	 * @param mixed   $value   The variable to store.
	 * @param array   $tags    Array of tags.
	 * @param integer $expire  Expiration time of the item.
	 * @return boolean
	 */
	public function set($key, $value, $tags = null, $expire = null)
	{
		$value = array(
			'data' => $value,
			'time' => time()
		);
		if (is_array($tags)) {
			$value['tags'] = $this->getTagVersion($tags);
		}
		return $this->driver->set($key, $value, $expire);
	}

	/**
	 * Fetchs item from the cache.
	 *
	 * Returns FALSE on failure, if the given key was not found or the source timestamp
	 * is greater than cache one.
	 *
	 * @param string $key  The key to fetch.
	 * @param int    $time Source timestamp (optional).
	 * @return mixed
	 */
	public function get($key, $time = null)
	{
		if (false === ($value = $this->driver->get($key))) {
			return false;
		}
		if ($time > $value['time']) {
			return false;
		}
		if (array_key_exists('tags', $value)) {
			$tags = $this->getTagVersion(array_keys($value['tags']));
			foreach ($tags as $tag => $v) {
				if ($v > $value['tags'][$tag]) {
					return false;
				}
			}
		}
		return $value['data'];
	}

	/**
	 * Deletes item from the cache.
	 *
	 * @param string $key  The key to delete.
	 * @return boolean
	 */
	public function delete($key)
	{
		return $this->driver->delete($key);
	}

	/**
	 * Updates tag version.
	 *
	 * @return boolean
	 */
	public function touchTag($tag)
	{
		$v = microtime(true);
		$this->tags[$tag] = $v;
		return $this->driver->set('#' . $tag, $v);
	}

	/**
	 * Returns the specified tag version.
	 *
	 * @param array $tags  Array of tags to fetch version.
	 * @return array
	 */
	protected function getTagVersion(array $tags)
	{
		$res = array();
		foreach ($tags as $tag) {
			if (false === array_key_exists($tag, $this->tags)) {
				if (false === ($v = $this->driver->get('#' . $tag))) {
					$v = microtime(true);
					$this->driver->set('#' . $tag, $v);
				}
				$this->tags[$tag] = $v;
			}
			$res[$tag] = $this->tags[$tag];
		}
		return $res;
	}
}
