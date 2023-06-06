<?php

/**
 * Clients model.
 *
 * @version  $Id$
 * @package  Application
 */

class Model_Maillistmessages extends Model
{
	protected static $table = 'mailListMessages';
	
	public static function getMessages($id = null)
	{
		if ($id) {
			return new self($id);
		} else {
			return self::query(array(
				'order' => '`dateTime` DESC'
			))->fetchAll();
		}
	}
	
	public static function getPending()
	{
		return self::query(array(
			'where' => array('`status` = ?', 'pending')
		))->fetchAll();
	}
}
