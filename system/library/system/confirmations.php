<?php

/**
 * Kit.
 *
 * Confirmations library.
 *
 * @version  $Id: confirmations.php 113 2010-07-30 07:38:57Z perfilev $
 * @package  System
 */

class System_Confirmations
{
	const EMAIL	= 1;
	const PASSWORD	= 2;
	const CREATECOMPANY	= 3;
	const CREATESCHOOL	= 4;

	const USER = 1;
	const COMPANY = 2;

	/**
	 * Creates a new confirmation record in database.
	 *
	 * @param  integer  $sender  Sender Id.
	 * @param  integer  $senderType    Sender type: admin, user, meintenance.
	 * @param  integer  $type    Confirmation type.
	 * @param  string   $value   User value.
	 * @return string   Confirmation code.
	 * @throws Exception
	 */
	public static function generate($sender, $senderType, $type, $value = null)
	{
		self::deleteOld();
		$db = Database::getInstance();
		$counter = 0;
		$unique  = true;
		do {
			$counter++;
			$code = Text::random('alphanuml', 32);
			try {
				$db->query(
					'INSERT INTO `confirmations` (`sender`, `senderType`, `type`, `code`, `value`, `date`, `isRefreshed`) VALUES (?, ?, ?, ?, ?, NOW(), ?)',
					$sender, $senderType, $type, $code, $value, true
				);
				$unique = true;
			} catch (DatabaseException $e) {
				if (1062 == $e->getCode()) {
					$unique = false;
					if (++$counter >= 10) {
						throw new Exception('Cannot generate unique confirmation code.');
					}
				} else {
					throw $e;
				}
			}
		} while (false == $unique);
		return $code;
	}

	/**
	 * Deletes old confirmation codes.
	 *
	 * @return void
	 */
	private static function deleteOld()
	{
		Database::getInstance()->query('DELETE FROM `confirmations` WHERE `date` + INTERVAL 4 DAY < NOW()');
	}

	/**
	 * Returns code's data and removes it.
	 *
	 * Returns associative array with sender, type and value keys.
	 *
	 * @param  string   $code  Confirmation code.
	 * @return array
	 */
	public static function confirm($code)
	{
		self::deleteOld();
		$db  = Database::getInstance();
		$res = $db->query('SELECT `sender`, `senderType`, `type`, `value` FROM `confirmations` WHERE `code` = ?', $code)->fetch();
		if (null !== $res) {
			$db->query('DELETE FROM `confirmations` WHERE `code` = ?', $code);
			return $res;
		}
		return false;
	}

	/**
	 * Returns confirmation by sender, if exists.
	 *
	 * Returns associative array with all confirmation fields.
	 *
	 * @param  integer   $id  Sender id.
	 * @return array
	 */
	public static function getBySender($id, $type)
	{
		self::deleteOld();
		$db  = Database::getInstance();
		$res = $db->query('SELECT * FROM `confirmations` WHERE `sender` = ? AND `senderType` = ?', $id, $type)->fetch();
		if (null !== $res) {
			return $res;
		}
		return false;
	}

	/**
	 * Set confirmation date to now.
	 *
	 * @param  integer   $id  Confirmation id.
	 * @return array
	 */
	public static function refreshDate($id)
	{
		$db = Database::getInstance();
		$db->query('UPDATE `confirmations` SET `date` = NOW() WHERE `id` = ?', $id);
		self::deleteOld();
	}


	/**
	 * Resend confirmation after 2 days of no confirming
	 */
	public static function refresh()
	{
		self::deleteOld();
		$db = Database::getInstance();
		$items =  $db->query("SELECT * FROM `confirmations` WHERE DATEDIFF(NOW(), `date`) > 2 AND `isRefreshed` = 0")->fetchAll();

		foreach($items as $item) {
			$db->query('UPDATE `confirmations` SET `date` = NOW(),`isRefreshed` = 1 WHERE `id` = ?', $item->id);
			self::sendEmail($item);
		}
	}

	/**
	 * Send confirmation email
	 * @param object $confirmation Item from table
	 */
	protected static function sendEmail($confirmation)
	{
		$mail = new Mailer('testdrive/user/email-confirm-renew');
		$mail->code = $confirmation->code;
		$mail->send($confirmation->value);
	}


}
