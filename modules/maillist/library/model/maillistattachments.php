<?php

/**
 * Clients model.
 *
 * @version  $Id$
 * @package  Application
 */

class Model_Maillistattachments extends Model
{
	protected static $table = 'mailListAttachments';

	public static function getAttachments($messageId)
	{
		return self::query(array(
			'where' => array('`messageId` = ?', $messageId),
			'order' => '`id` ASC'
		))->fetchAll();
	}
	
	public static function getAttachment($attachmentId)
	{
		return new self($attachmentId);
	}
}
