<?php

abstract class Controller_Cli extends Controller
{
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