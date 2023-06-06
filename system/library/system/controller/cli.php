<?php

/**
 * Kit.
 *
 * Template CLI library.
 *
 * @version  $Id: cli.php 94 2010-07-19 03:44:18Z eprev $
 * @package  System
 */

abstract class System_Controller_Cli extends Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->view     = null;
		$this->resource = null;
	}

	public function before()
	{
		if (false == System::$inCli) {
			throw new ForbiddenException('Access denied to the CLI controller.');
		}
		ob_start();
	}

	public function after()
	{
		$this->response->body = ob_get_contents();
		ob_end_clean();
	}
}
