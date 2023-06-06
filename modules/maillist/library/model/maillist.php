<?php

/**
 * Clients model.
 *
 * @version  $Id$
 * @package  Application
 */

class Model_Maillist extends Model
{
	protected static $table = 'mailList';
	
//	public function confirm($token)
//	{
//		if (($subscriber = $this->db->query('SELECT * FROM `mailList` WHERE `token` = ?', $token)->fetch())) {
//			Model_Table::instance('mailList')->update(array('confirmed' => 1), (int)$subscriber['id']);
//			return true;
//		} else {
//			return false;
//		}
//	}

//	public function remove($token)
//	{
//		if (($subscriber = $this->db->query('SELECT * FROM `mailList` WHERE `token` = ?', $token)->fetch())) {
//			Model_Table::instance('mailList')->delete((int)$subscriber['id']);
//			return true;
//		} else {
//			return false;
//		}
//	}

	public static function getItemByAlias($alias)
	{
		return new self(array(
		    'where' => array('`alias`=?', $id)
		));
	}
	
	public static function getSubscribers($confirmed = false)
	{
		if ($confirmed) {
			return self::query(array(
				'where' => array('`confirmed` = ?', 1),
			))->fetchAll();
		} else {
			return self::query(array(
				'order' => array('`confirmed` = DESC, `name` ASC'),
			))->fetchAll();
		}
	}
	
//	public function getMessages($id = null)
//	{
//		if ($id) {
//			return $this->db->query('SELECT * FROM `mailListMessages` WHERE `id` = ?', $id)->fetch();
//		} else {
//			return $this->db->query('SELECT * FROM `mailListMessages` ORDER BY `dateTime` DESC')->fetchAll();
//		}
//	}
//
//	public function getRecipients($messageId)
//	{
//		return $this->db->query('SELECT * FROM `mailListRecipients` WHERE `messageId` = ?', $messageId)->fetchAll();
//	}
//
//	public function getAttachments($messageId)
//	{
//		return $this->db->query('SELECT * FROM `mailListAttachments` WHERE `parentId` = ? ORDER by `id` ASC', array($messageId))->fetchAll();
//	}
//
//	public function getAttachment($attachmentId)
//	{
//		return $this->db->query('SELECT * FROM `mailListAttachments` WHERE `id` = ?', $attachmentId)->fetch();
//	}

//	public function getSubscribers($confirmed = false)
//	{
//		if ($confirmed) {
//			return $this->db->query('SELECT * FROM `mailList` WHERE `confirmed` = 1')->fetchAll();
//		} else {
//			return $this->db->query('SELECT * FROM `mailList` ORDER BY `confirmed` DESC, `name` ASC')->fetchAll();
//		}
//	}

//	public function getSubscribersById($confirmed = false)
//	{
//		$result = array();
//		if ($confirmed) {
//			$subscribers = $this->db->query('SELECT * FROM `mailList` WHERE `confirmed` = 1')->fetchAll();
//		} else {
//			$subscribers = $this->db->query('SELECT * FROM `mailList` ORDER BY `confirmed` DESC, `name` ASC')->fetchAll();
//		}
//		foreach ($subscribers as $subscriber) {
//			$result[$subscriber['id']] = $subscriber;
//		}
//		return $result;
//	}

//	public function getSubscriber($id = null, $email = null)
//	{
//		if ($id) {
//			return $this->db->query('SELECT * FROM `mailList` WHERE `id` = ?', $id)->fetch();
//		} elseif ($email) {
//			return $this->db->query('SELECT * FROM `mailList` WHERE `email` = ?', $email)->fetch();
//		}
//	}
	
//	public function getTypes()
//	{
//		$types = array();
//		
//		$typesDB = $this->db->query('SELECT `type` FROM `mailList` WHERE `confirmed`=1 GROUP by `type` ORDER by `type`');
//		foreach($typesDB as $k => $v) {
//			$types[$v['type']] = $v['type'];
// 		}
//		
//		return $types;
//	}
//	
//	public function getCategories()
//	{
//		$categories = array();
//		
//		$categoriesDB = $this->db->query('SELECT `category` FROM `mailList` WHERE `confirmed`=1 GROUP by `category` ORDER by `category`');
//		foreach($categoriesDB as $k => $v) {
//			$categories[$v['category']] = $v['category'];
// 		}
//		
//		return $categories;
//	}
}
