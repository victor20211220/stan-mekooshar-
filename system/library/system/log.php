<?php

/**
 * Kit.
 *
 * Log library.
 *
 * @version $Id: log.php 37 2010-06-14 01:20:46Z eprev $
 * @package System
 */

class System_Log
{
	/**
	 * @var array  Class instances.
	 */
	protected static $instances = array();

	/**
	 * Returns an instance of class.
	 *
	 * @param string $profile  Config profile.
	 * @return object
	 * @throws InvalidArgumentError
	 */
	public static function getInstance($profile = 'default')
	{
		if (false == isset(self::$instances[$profile])) {
			$config = Config::getInstance()->log->$profile;
			self::$instances[$profile] = new static($config);
		}
		return self::$instances[$profile];
	}

	/**
	 * @var System_Config Log config.
	 */
	protected $config;

	/**
	 * @var array  Log messages.
	 */
	protected $messages = array();

	/**
	 * Class constructor.
	 *
	 * @param System_Config $config Log options.
	 * @return this
	 */
	public function __construct(System_Config $config)
	{
		$this->config = $config->cloneWith(array(
			'enabled'  => true,
			'filename' => null
		));

		if(!file_exists($this->config->filename)) {
			$pathname = substr($this->config->filename, 0, strrpos($this->config->filename, '/'));
			if($pathname && !is_dir($pathname)) {
				mkdir($pathname, 0777, true);
			}
		}
	}

	/**
	 * Class destructor.
	 *
	 * @return this
	 */
	public function __destruct()
	{
		// No exceptions are allowed there
		try {
			$this->flush();
		} catch (Exception $e) {
		}
	}

	/**
	 * Writes a message to log.
	 *
	 * @param string $message  Message.
	 * @param string $scope    Scope (e.g. __CLASS__).
	 * @return void
	 */
	public function write($message, $scope = '')
	{
		if ($this->config->enabled) {
			$this->messages[] = date('[Y.m.d H:i:s]') . ($scope ? ' [' . $scope . '] ' : ' ') . $message;
		}
	}

	/**
	 * Flush the message buffer.
	 *
	 * @return void
	 */
	public function flush() {
		if (count($this->messages)) {
			file_put_contents($this->config->filename, implode(chr(10), $this->messages) . chr(10), FILE_APPEND);
		}
	}
}
