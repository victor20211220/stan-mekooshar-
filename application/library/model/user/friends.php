<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_User_Friends extends Model{

	protected static $table = 'user_friends';

	public static function createEmptyFriends($user_id)
	{
		self::create(array(
			'user_id' => $user_id
		));
	}

	public static function checkAndCreateEmptyFriendsForAllUser()
	{
		$users = self::getList(array(
			'select' => 'id',
			'where' => array('isConfirmed = 1 AND isRemoved = 0 AND user_friends.user_id IS NULL'),
			'from' => 'users',
			'join' => array(
				array(
					'table' => 'user_friends',
					'type' => 'left',
					'where' => array('user_friends.user_id = users.id')
				)
			)
		));

		foreach($users['data'] as $user) {
			self::createEmptyFriends($user->id);
		}
	}

	public static function checkAndFixFriendsForAllUser()
	{
		$results = self::getList(array(
			'select' => '
						users.id,
						GROUP_CONCAT(connections.friend_id) AS friends,
						user_friends.friends AS userFriends
						',
			'where' => array('isConfirmed = 1 AND isRemoved = 0'),
			'from' => 'users',
			'join' => array(
				array(
					'table' => 'connections',
					'type' => 'left',
					'where' => array('connections.user_id = users.id AND connections.typeApproved = ?', ADDCONNECTION_APPROVED)
				),
				array(
					'table' => 'user_friends',
					'type' => 'left',
					'where' => array('user_friends.user_id = users.id')
				)
			),
			'group' => 'users.id'
		));

		foreach($results['data'] as $item){
			$friends1 = explode(',', $item->friends);
			$friends2 = explode(',', $item->userFriends);

			$isEqual = TRUE;
			if(count($friends1) != count($friends2)) {
				$isEqual = FALSE;
			}
			if($isEqual) {
				foreach($friends1 as $friend) {
					if(!in_array($friend, $friends2)) {
						$isEqual = FALSE;
						break;
					}
				}
			}

			$friends = array();
			if(!$isEqual) {
				$friends = (string)implode(',', $friends1);
				Model_User_Friends::update(array(
					'friends' => $friends
				), array('user_id = ?', $item->id));
			}
		}
	}

	public static function addFriends($user_id, $friend_id)
	{
		$users_friend = self::getList(array(
			'select' => '
						user_id AS id,
						friends
						',
			'where' => array('user_id IN (?)', array($user_id, $friend_id)),
		));

		$user = $users_friend['data'][$user_id];
		$friend = $users_friend['data'][$friend_id];

		$friends = explode(',', $user->friends);
		$friends[] = $friend_id;
		$friends = array_fill_keys($friends, TRUE);
		$friends = array_keys($friends);
		self::update(array(
			'friends' => ((string) implode(',', $friends))
		), array('user_id = ?', $user_id));

		$friends = explode(',', $friend->friends);
		$friends[] = $user_id;
		$friends = array_fill_keys($friends, TRUE);
		$friends = array_keys($friends);
		self::update(array(
			'friends' => ((string) implode(',', $friends))
		), array('user_id = ?', $friend_id));
	}


	public static function removeFriends($user_id, $friend_id)
	{
		$users_friend = self::getList(array(
			'select' => '
						user_id AS id,
						friends
						',
			'where' => array('user_id IN (?)', array($user_id, $friend_id)),
		));

		$user = $users_friend['data'][$user_id];
		$friend = $users_friend['data'][$friend_id];


		$friends = explode(',', $user->friends);
		if(($key = array_search($friend_id, $friends)) !== false) {
			unset($friends[$key]);
		}
		$friends = array_fill_keys($friends, TRUE);
		$friends = array_keys($friends);
		self::update(array(
			'friends' => ((string) implode(',', $friends))
		), array('user_id = ?', $user_id));


		$friends = explode(',', $friend->friends);
		if(($key = array_search($user_id, $friends)) !== false) {
			unset($friends[$key]);
		}
		$friends = array_fill_keys($friends, TRUE);
		$friends = array_keys($friends);
		self::update(array(
			'friends' => ((string) implode(',', $friends))
		), array('user_id = ?', $friend_id));
	}

}