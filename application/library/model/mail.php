<?php

class Model_Mail extends Model
{
	/**
	 * @var string Table name.
	 */
	public static $table = 'mails';
	
	public static function send($limit = 30)
	{
		foreach(self::query(array(
		    'where' => array('`sendAfter` <= ?', date('Y-m-d H-i-s', CURRENT_TIMESTAMP)),
		    'order' => '`id` ASC',
		    'limit' => $limit
		)) as $result) {
			
			$message = unserialize($result->message);
			if(Smtp::getInstance()->send($result->recipient, $message['subject'], $message['body'], $message['headers'])) {
				self::remove($result->id);
			} else {
				Log::getInstance()->write('Mail failure', __METHOD__);
			}
			usleep(300000);
		}
	}
}