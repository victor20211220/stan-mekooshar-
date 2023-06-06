<?php

/**
 * Mailer CLI controller.
 *
 * @version $Id$
 * @package Application
 */

class Cli_Test_Controller extends Controller_Cli
{
	/**
	 * Test.
	 *
	 * @returns void
	 */
	public function actionVar($var)
	{
		echo $var;
	}
}