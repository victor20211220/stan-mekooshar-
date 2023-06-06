<?php

/**
 * Kit.
 *
 * System library.
 *
 * @version $Id: system.php 55 2010-06-25 06:52:45Z eprev $
 * @package System
 */

class NotFoundException extends Exception {};
class ForbiddenException extends Exception {};

final class System
{
	/**
	 * @var array  Names of the PHP errors.
	 */
	public static $errorLevels = Array(
		//E_ERROR              => 'Error',
		E_WARNING            => 'Warning',
		//E_PARSE              => 'Parsing Error',
		E_NOTICE             => 'Notice',
		//E_CORE_ERROR         => 'Core Error',
		//E_CORE_WARNING       => 'Core Warning',
		//E_COMPILE_ERROR      => 'Compile Error',
		//E_COMPILE_WARNING    => 'Compile Warning',
		E_USER_ERROR         => 'User Error',
		E_USER_WARNING       => 'User Warning',
		E_USER_NOTICE        => 'User Notice',
		E_STRICT             => 'Runtime Notice',
		E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
		E_DEPRECATED         => 'Deprecated',
		E_USER_DEPRECATED    => 'User Deprecated'
	);

	/**
	 * @var array  Paths.
	 */
	public static $paths = array(APPLICATION_PATH, SYSTEM_PATH);

	/**
	 * @var boolean  Is debug enabled?
	 */
	public static $debugEnabled = true;

	/**
	 * @var boolean  Is in CLI?
	 */
	public static $inCli = false;
	
	/**
	 * @var stdclass  List of global variables.
	 */
	public static $global;

	/**
	 * System initializtion.
	 *
	 * @param Config $config  Configuration object.
	 * @return void
	 */
	public static function initialize(Config $config)
	{
		self::$global = new StdClass();
		
		// Output buffering
		if ($config->gzipOutput) {
			ob_start('ob_gzhandler');
		} else {
			ob_start();
		}

		// Debug mode
		self::$debugEnabled = $config->debugEnabled;

		self::$inCli = php_sapi_name() == 'cli';

		// Modules
		$paths    = array(APPLICATION_PATH);
		$autoload = array();
		foreach ($config->modules as $name => $params) {
			if ($params->enabled) {
				$paths[] = MODULES_PATH . strtolower($name) . '/';
				if (false === empty($params->autoload)) {
					$autoload[] = $name;
				}
			}
		}
		$paths[] = SYSTEM_PATH;
		self::$paths = $paths;

		// Autoload modules
		foreach ($autoload as $module) {
			self::load($module);
		}
	}

	/**
	 * Locates the full path of a file.
	 *
	 * @param string $filename  Filename to search.
	 * @return string
	 */
	public static function locate($filename)
	{
		foreach (self::$paths as $path) {
			if (is_file($path . $filename)) {
				return $path . $filename;
			}
		}
		return null;
	}


	/**
	 * Returns an array of all found full paths of a file.
	 *
	 * @param string $filename  Filename to search.
	 * @return array
	 */
	public static function findAll($filename)
	{
		$res = array();
		foreach (self::$paths as $path) {
			if (is_file($path . $filename)) {
				$res[] = $path . $filename;
			}
		}
		return array_reverse($res);
	}

	/**
	 * Loads library by the given class name.
	 *
	 * @param string  $class  Class to load.
	 * @return boolean
	 */
	public static function load($class)
	{
		$filename = str_replace('_', '/', strtolower($class));
		if ($path = System::locate('library/' . $filename . '.php')) {
			require $path;
			return true;
		}
		return false;
	}

	/**
	 * Error handler.
	 *
	 * @param integer $level   Error level.
	 * @param string  $message Error message.
	 * @param string  $file    Filename that the error was raised.
	 * @param integer $line    Line number that the error was raised.
	 * @return boolean
	 */
	public static function errorHandler($level, $message, $file, $line)
	{
		if ($level & error_reporting()) {
			Log::getInstance()->write(
				$message . ' in ' . $file . ':' . $line,
				'PHP ' . self::$errorLevels[$level]
			);
			if (self::$debugEnabled || E_USER_ERROR == $level) {
				throw new ErrorException($message, 0, $level, $file, $line);
			}
			return true;
		}
		return false;
	}

	/**
	 * Exception handler.
	 *
	 * @param Exception $exception  Exception.
	 * @return boolean
	 */
	public static function exceptionHandler($exception)
	{
		try {
			Log::getInstance()->write(ucfirst($exception));
			ob_start();
			if (self::$inCli) {
				include self::locate('views/cli/exception.php');
			} else {
				header('HTTP/1.1 500 Internal Server Error', true, 500);
				include self::locate('views/exception.php');
			}
			ob_end_flush();
		} catch (Exception $e) {
			echo 'Uncaught '. $e;
		}
	}

	/**
	 * Prints dump of the given variable.
	 *
	 * @param string $var  Variable to dump.
	 * @return void
	 */
	public static function dump($var, $exit = false)
	{
		echo self::$inCli ? "\n@~~\n" : '<div style="display: block; font-family: \'Courier New\',courier; font-size: .8em; line-height: 1em; '
		   . 'white-space: pre; background-color: #eee; margin: .5em 0; padding: .5em;">';
		if (is_array($var)) {
			print_r($var);
		} else {
			var_dump($var);
		}
		echo self::$inCli ? "\n~~.\n" : '</div>';
		
		if($exit) {
			exit();
		}
	}

	/**
	 * Prints dump of the given variable.
	 *
	 * @param string $var  Variable to dump.
	 * @return void
	 */
	public static function dump2($var, $exit = false)
	{
		echo self::$inCli ? "\n@~~\n" : '<div class="dump_data_box">'
				. '<div class="dump_data_selector dump_data_selector_plus" onclick="$(this).parent().addClass(\'active\');">+</div>'
				. '<div class="dump_data_selector dump_data_selector_minus" onclick="$(this).parent().removeClass(\'active\');">-</div>'
				. '<div class="dump_data_data">';
		if (is_array($var)) {
			print_r($var);
		} else {
			var_dump($var);
		}
		echo self::$inCli ? "\n~~.\n" : '</div></div>';

		if($exit) {
			exit();
		}
	}


	/**
	 * Prints dump of the given variable.
	 *
	 * @param string $var  Variable to dump.
	 * @return void
	 */
	public static function setGlobal($var, $value)
	{
		self::$global->$var = View::$global->$var = $value;
	}
}

spl_autoload_register(array('System', 'load'));

/* Local Variables:	   */
/* tab-width: 4		   */
/* indent-tabs-mode: t */
/* End:                */
