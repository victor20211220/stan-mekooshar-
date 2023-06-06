<?php

/**
 * Kit.
 *
 * Memcached Cache library.
 *
 * @version $Id: memcache.php 35 2010-06-13 23:52:13Z eprev $
 * @package System
 */

class System_Cache_Memcache
{
	/**
	 * @var resource  Server connection.
	 */
	protected $link;

	/**
	 * @var System_Config Cache config.
	 */
	protected $config;

	/**
	 * Sets up the memcached configuration and connects to the server.
	 *
	 * @param System_Config $config Cache config.
	 * @return this
	 */
	public function __construct(System_Config $config)
	{
		$this->config = $config->cloneWith(array(
			'host'   => null,
			'port'   => null,
			'expire' => 3600,
			'prefix' => ''
		));
		try {
			$this->link = memcache_connect($this->config->host, $this->config->port);
		} catch (Exception $e) {
			Log::getInstance()->write(
				'Connetion faild to Memcached server ' . $this->config->hostname . ':'. $this->config->port . '.',
				__CLASS__
			);
		}
	}

	/**
	 * Store data at the server.
	 *
	 * @param string  $key     The key that will be associated with the item.
	 * @param mixed   $value   The variable to store.
	 * @param integer $expire  Expiration time of the item.
	 * @return boolean
	 */
	public function set($key, $value, $expire = null)
	{
		if (null == $this->link) return false;

		if ('' == $key) return false;

		if (null === $expire) {
			$expire = $this->config->expire;
		}

		return memcache_set($this->link, $this->config->prefix . $key, $value, 0, $expire);
	}

	/**
	 * Fetchs item from the server.
	 *
	 * @param string $key  The key or arrays of keys to fetch.
	 * @return mixed
	 */
	public function get($key)
	{
		if (null == $this->link) return false;
		return memcache_get($this->link, $this->config->prefix . $key);
	}

	/**
	 * Deletes item from the server.
	 *
	 * @param string $key  The key to delete.
	 * @return boolean
	 */
	public function delete($key)
	{
		if (null == $this->link) return false;
		return memcache_delete($this->link, $this->config->prefix . $key);
	}
}
