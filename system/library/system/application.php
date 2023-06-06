<?php

/**
 * Kit.
 *
 * Application library.
 *
 * @version  $Id: application.php 27 2010-06-10 11:12:48Z eprev $
 * @package  System
 */

class System_Application
{
	/**
	 * Application initialization.
	 *
	 * @param Config $config  Application config instance.
	 * @return void
	 */
	protected static function initialize(Config $config = null)
	{
		set_error_handler(array('System', 'errorHandler'));
		set_exception_handler(array('System', 'exceptionHandler'));

		$config = $config ?: Config::getInstance();

		System::initialize($config);
		Request::initialize();
	}

	/**
	 * Runs application.
	 *
	 * @return void
	 */
	public static function execute()
	{
		static::initialize();

		try {
			$response = Request::execute(Request::$uri);
			if (System::$debugEnabled) {
				$response->send(function ($output) {
					return str_replace(array(
						'{execution_time}',
						'{memory_usage}',
						'{included_files}',
						'{database_queries}',
                                                '{database_queries_log}',
					), array(
						number_format((microtime(true) - START_TIME) * 1000, 3),
						number_format(memory_get_usage(true) / 1048576, 2),
						count(get_included_files()),
						Database::$queriesCount,
                                                Database::$queriesLog,
					), $output);
				});
			} else {
				$response->send();
			}
		} catch (Exception $e) {
			if (System::$debugEnabled) {
				if ($e instanceof ForbiddenException) {
					$response = new Response(403, new View(System::$inCli ? 'cli/forbidden' : 'forbidden', array('exception' => $e)));
				} elseif ($e instanceof NotFoundException) {
					$response = new Response(404, new View(System::$inCli ? 'cli/not-found' : 'not-found', array('exception' => $e)));
				} else {
					throw $e;
				}
				$response->send();
			} else {
				throw $e;
			}
		}
	}
}
