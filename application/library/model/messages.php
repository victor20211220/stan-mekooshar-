<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Messages extends Model{

	protected static $table = 'messages';

	public static function getItemByMessageidFriendid($message_id, $user_id)
	{
		$result = new self(array(
			'where' => array('id = ? AND friend_id = ? AND isFriendRemoved = 0', $message_id, $user_id)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'friend_id');
		return $result;
	}

	public static function getListReceivedByUser($user_id)
	{
		$filter = Request::get('filter', false);
		if($filter == 'unread') {
			$where = array('messages.friend_id = ? AND messages.isFriendRemoved = 0 AND typeForFriend = 0 AND isFriendView = 0', $user_id);
		} else {
			$where = array('messages.friend_id = ? AND messages.isFriendRemoved = 0 AND typeForFriend = 0', $user_id);
		}

		$results = self::getList(array(
			'select' => '
							messages.*,

							users.id 					AS userId,
							users.firstName 			AS userFirstName,
							users.lastName 				AS userLastName,
							users.avaToken 				AS userAvaToken,
							users.alias 				AS userAlias,
							users.setInvisibleProfile 	AS userSetInvisibleProfile
						',
			'where' => $where,
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = messages.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
			),
			'order' => 'messages.isFriendView ASC, messages.createDate DESC'
		), true, false, 10);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getListSentByUser($user_id)
	{
		$results = self::getList(array(
			'select' => '
							messages.*,

							users.id 					AS userId,
							users.firstName 			AS userFirstName,
							users.lastName 				AS userLastName,
							users.avaToken 				AS userAvaToken,
							users.alias 				AS userAlias,
							users.setInvisibleProfile 	AS userSetInvisibleProfile
						',
			'where' => array('messages.user_id = ? AND messages.isUserRemoved = 0 AND typeForUser = 0', $user_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = messages.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
			),
			'order' => 'messages.isFriendView ASC, messages.createDate DESC'
		), true, false, 10);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getListArchiveByUser($user_id)
	{
		$filter = Request::get('filter', false);
		if($filter == 'unread') {
			$where = array('users.id IS NOT NULL AND friend.id IS NOT NULL AND ((messages.friend_id = ? AND messages.isFriendRemoved = 0 AND typeForFriend = 1 AND isFriendView = 0))', $user_id, $user_id);
		} else {
			$where = array('users.id IS NOT NULL AND friend.id IS NOT NULL AND ((messages.friend_id = ? AND messages.isFriendRemoved = 0 AND typeForFriend = 1) OR (messages.user_id = ? AND messages.isUserRemoved = 0 AND typeForUser = 1))', $user_id, $user_id);
		}

		$results = self::getList(array(
			'select' => '
							messages.*,

							users.id 					AS userId,
							users.firstName 			AS userFirstName,
							users.lastName 				AS userLastName,
							users.avaToken 				AS userAvaToken,
							users.alias 				AS userAlias,
							users.setInvisibleProfile 	AS userSetInvisibleProfile,

							friend.id 					AS friendId,
							friend.firstName 			AS friendFirstName,
							friend.lastName				AS friendLastName,
							friend.avaToken 			AS friendAvaToken,
							friend.alias 				AS friendAlias,
							friend.setInvisibleProfile 	AS friendSetInvisibleProfile
						',
			'where' => $where,
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = messages.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'noQuotes' => true,
					'table' => 'users as friend',
					'where' => array('friend.id = messages.friend_id AND friend.isRemoved = 0 AND friend.isConfirmed = 1')
				),
			),
			'order' => 'messages.createDate DESC'
		), true, false, 10);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getListTrashByUser($user_id)
	{
		$filter = Request::get('filter', false);
		if($filter == 'unread') {
			$where = array('users.id IS NOT NULL AND friend.id IS NOT NULL AND ((messages.friend_id = ? AND messages.isFriendRemoved = 0 AND typeForFriend = 2 AND isFriendView = 0))', $user_id, $user_id);
		} else {
			$where = array('users.id IS NOT NULL AND friend.id IS NOT NULL AND ((messages.friend_id = ? AND messages.isFriendRemoved = 0 AND typeForFriend = 2) OR (messages.user_id = ? AND messages.isUserRemoved = 0 AND typeForUser = 2))', $user_id, $user_id);
		}

		$results = self::getList(array(
			'select' => '
							messages.*,

							users.id 					AS userId,
							users.firstName 			AS userFirstName,
							users.lastName 				AS userLastName,
							users.avaToken 				AS userAvaToken,
							users.alias 				AS userAlias,
							users.setInvisibleProfile 	AS userSetInvisibleProfile,

							friend.id 					AS friendId,
							friend.firstName 			AS friendFirstName,
							friend.lastName				AS friendLastName,
							friend.avaToken 			AS friendAvaToken,
							friend.alias 				AS friendAlias,
							friend.setInvisibleProfile 	AS friendSetInvisibleProfile
						',
			'where' => $where,
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = messages.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'noQuotes' => true,
					'table' => 'users as friend',
					'where' => array('friend.id = messages.friend_id AND friend.isRemoved = 0 AND friend.isConfirmed = 1')
				),
			),
			'order' => 'messages.createDate DESC'
		), true, false, 10);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getListHistoryByUserId($friend_id, $user_id)
	{
		$results = self::getList(array(
			'select' => '
							messages.*,

							users.id 					AS userId,
							users.firstName 			AS userFirstName,
							users.lastName 				AS userLastName,
							users.avaToken 				AS userAvaToken,
							users.alias 				AS userAlias,
							users.setInvisibleProfile 	AS userSetInvisibleProfile,

							friend.id 					AS friendId,
							friend.firstName 			AS friendFirstName,
							friend.lastName				AS friendLastName,
							friend.avaToken 			AS friendAvaToken,
							friend.alias 				AS friendAlias,
							friend.setInvisibleProfile 	AS friendSetInvisibleProfile
						',
			'where' => array('users.id IS NOT NULL AND friend.id IS NOT NULL AND ((messages.user_id = ? AND messages.friend_id = ? AND messages.isFriendRemoved = 0 AND typeForFriend in (0, 1)) OR (messages.friend_id = ? AND messages.user_id = ? AND messages.isUserRemoved = 0 AND typeForUser in (0, 1)))',  $friend_id, $user_id, $friend_id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = messages.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'noQuotes' => true,
					'table' => 'users as friend',
					'where' => array('friend.id = messages.friend_id AND friend.isRemoved = 0 AND friend.isConfirmed = 1')
				),
			),
			'order' => 'messages.createDate DESC'
		), true, false, 10);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getItemReplayByMessageId($message_id, $user_id)
	{
		$result =  new self(array(
			'select' => '
							messages.*,

							users.id 					AS userId,
							users.firstName 			AS userFirstName,
							users.lastName 				AS userLastName,
							users.avaToken 				AS userAvaToken,
							users.alias 				AS userAlias,
							users.setInvisibleProfile 	AS userSetInvisibleProfile,

							friends.id 					AS friendId,
							friends.firstName 			AS friendFirstName,
							friends.lastName			AS friendLastName,
							friends.avaToken 			AS friendAvaToken,
							friends.avaToken 			AS friendAvaToken,
							friends.alias 				AS friendAlias,
							friends.setInvisibleProfile AS friendSetInvisibleProfile
						',
			'where' => array('messages.id = ? AND (messages.friend_id = ? OR messages.user_id = ?) AND messages.typeForFriend in (0, 1, 2) AND messages.isFriendRemoved = 0', $message_id, $user_id, $user_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = messages.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'noQuotes' => true,
					'table' => 'users as friends',
					'where' => array('friends.id = messages.friend_id AND friends.isRemoved = 0 AND friends.isConfirmed = 1')
				),
			),
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'friend_id');
		return $result;
	}

	public static function getCountNewReceived($user_id)
	{
		$count = self::query(array(
			'select' => 'COUNT(isFriendView) as countItems',
			'where' => array('friend_id = ? AND typeForFriend = 0 AND isFriendView = 0', $user_id),
			'group' => 'isFriendView'
		))->fetch();

		if(!is_null($count)){
			return $count->countItems;
		} else {
			return 0;
		}
	}


}