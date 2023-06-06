<?php

/**
 * Cron.
 *
 * Cron controller.
 *
 * @package Application
 */

class Cron_Controller extends Controller_Cli
{
	public function  before() {
		parent::before();

	}

	// Update on 00:00 everyday
	public function actionDaily()
	{
		Model_Notifications::remove(array('createDate <= ?', date('Y-m-d H:m:i', time() - 60*60*24*40)));
		Model_Confirmations::remove(array('type = ? AND date <= ?', Confirmations::EMAIL, date('Y-m-d H:m:i', time() - 60*60*24*3)));
		Model_Confirmations::remove(array('type = ? AND date <= ?', Confirmations::PASSWORD, date('Y-m-d H:m:i', time() - 60*60*24*1)));
		Model_Confirmations::remove(array('type = ? AND date <= ?', Confirmations::COMPANY, date('Y-m-d H:m:i', time() - 60*60*24*3)));
		Model_User::remove(array('isConfirmed = 0 AND isRemoved = 0 AND createDate <= ?', date('Y-m-d H:m:i', time() - 60*60*24*3)));
		Model_User::updateAllUsersCountConnections();
	}

	public function actionMails()
	{
		Model_Mail::send();
	}

	public function actionConfirmRefresh()
	{
		Confirmations::refresh();
	}

	public function after()
	{
		$this->autorender = false;
		parent::after();
	}
}
