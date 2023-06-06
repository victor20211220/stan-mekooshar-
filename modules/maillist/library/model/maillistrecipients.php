<?php

/**
 * Clients model.
 *
 * @version  $Id$
 * @package  Application
 */
class Model_Maillistrecipients extends Model {

	protected static $table = 'mailListRecipients';

	public static function getRecipients($message_id) {
		return self::query(array(
			    'where' => array('`messageId` = ?', $message_id)
			))->fetchAll();
	}

	public static function getRecipientsMessageId($message_id) {
		$where = array('maillistrecipients.messageId = ?', $message_id);
		$query = array(
		    'select' => '*,users.name,users.email',
		     'join' => array(
			array(
			    'table' => 'users',
			    'where' => array("maillistrecipients.subscriberId = users.id"),
			    'type' => 'left'
			)
		    ),
		    'where' => $where,
		);
		$result = array();
		foreach (self::query($query) as $recipient) {
//			if($subscriber_id == $recipient->subscriberId){
				$result[$recipient->subscriberId] = array('name' => $recipient->name,'email' => $recipient->email);
//			}
		}
		return $result;
	}

	public static function getUserResipient() {
		$query = array(
		    'select' => '*',
		);
		$user_recipient = array();
		foreach (self::query($query) as $recipient) {
			$user_recipient[$recipient->messageId][$recipient->subscriberId] = true;
		}
		return $user_recipient;
	}

	public static function getByMessage($message_id) {
		return self::query(array(
			    'where' => array('`sent` = ? AND `messageId` = ?', 0, $message_id)
			))->fetchAll();
	}

}
