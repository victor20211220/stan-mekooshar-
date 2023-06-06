<?php

/**
 * Kit.
 *
 * Application library.
 *
 * @version  $Id: application.php 27 2010-06-10 11:12:48Z eprev $
 * @package  Application
 */

require_once APPLICATION_PATH . 'defines.php';

class Application extends System_Application
{
	/**
	 * Application initialization.
	 *
	 * @return void
	 */
	public static function initialize(Config $config = null)
	{
		parent::initialize();

	}
	
	/**
	 * Runs application.
	 *
	 * @return void
	 */
	public static function execute()
	{
		static::initialize();

		if(!System::$inCli) {
			Visitor::instance();
		}
		
		$handler = false;

		try {
			$response = Request::execute(Request::$uri);
			
			if (System::$debugEnabled) {
				$handler = function ($output) {
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
				};
			}
		} catch (Exception $e) {
			Log::getInstance()->write(
				$e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
				'PHP ' . System::$errorLevels[E_USER_ERROR]
			);
			
			if ($e instanceof ForbiddenException) {
				$response = new Response(403, new View(System::$inCli ? 'cli/forbidden' : 'common/errors/forbidden', array('exception' => $e)));
			} elseif ($e instanceof NotFoundException) {
				$response = new Response(404, new View(System::$inCli ? 'cli/not-found' : 'common/errors/not-found', array('exception' => $e)));
			} else {
				$response = new Response(500, new View(System::$inCli ? 'cli/exception' : 'common/errors/exception', array('exception' => $e)));
			}

			if(Request::$host != 'system') {
				$mail = new Mailer('exception');
				$mail->exception = $e;
				$mail->send(Config::getInstance()->devEmail);
			}

			if(!System::$inCli) {
				Visitor::instance()->setBad()->reportError($e);
			}
		}
		
		$response->send($handler);
	}
}

function dump()
{
	return call_user_func_array(array('System', 'dump'), func_get_args());
}
function dump2()
{
	$user = Auth::getInstance()->getIdentity();
	if(isset($user)) {
		$config = System::$global;
		$allowDump2 = $config->config->allowDump2;
		$emails = array();
		foreach($allowDump2 as $key => $value) {
			$emails[] = $value;
		}
		if(in_array($user->email, $emails)) {
			return call_user_func_array(array('System', 'dump2'), func_get_args());
		}
	}
}
function t()
{
	return call_user_func_array(array('Text', 'get'), func_get_args());
}
function dumpLog(){
	ob_start();
	return call_user_func_array(array('System', 'dump'), func_get_args());
	Log::getInstance()->write(ob_get_clean());
}
