<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Groups extends Model{

	protected static $table = 'groups';

	public static function checkIsRegistredByName($name)
	{
		$group = self::query(array(
			'where' => array('`name` = ? AND user_id IS NOT NULL', $name)
		))->fetch();

		if(!is_null($group)) {
			$group = self::instance($group);
			Model_User::addUserIdByKey($group, 'user_id');
		} else {
			$group = false;
		}
		return $group;
	}

	public static function getListByuserId($user_id)
	{
		$results = self::getList(array(
			'where' => array('user_id = ?', $user_id),
		), false, false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
		}
		return $results;
	}

	public static function getItemById($group_id, $user_id = false)
	{
		$result = new self(array(
			'select' => '
							groups.*,
							group_members.user_id as memberUserId,
							group_members.isApproved as memberIsApproved,
							group_members.memberType as memberType
						',
			'where' => array('id = ? AND (isAgree = 1 OR groups.user_id = ?)', $group_id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ? AND group_members.isApproved = 1', $user_id)
				)
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'memberUserId');
		return $result;
	}

	public static function getItemById_WithoutApproved($group_id, $user_id = false)
	{
		$result = new self(array(
			'select' => '
							groups.*,
							group_members.user_id as memberUserId,
							group_members.isApproved as memberIsApproved,
							group_members.memberType as memberType
						',
			'where' => array('id = ? AND (isAgree = 1 OR groups.user_id = ?)', $group_id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ?', $user_id)
				)
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'memberUserId');
		return $result;
	}

	public static function getUserIdGroupid($user_id, $group_id)
	{
		$result = new self(array(
			'select' => '
							groups.*,
							group_members.user_id as memberUserId,
							group_members.isApproved as memberIsApproved,
							group_members.memberType as memberType
						',
			'where' => array('groups.id = ? AND groups.user_id = ? ', $group_id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ?', $user_id)
				)
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'memberUserId');
		return $result;
	}

	public static function getGroupidAdminid($group_id, $user_id)
	{
		$result = new self(array(
			'select' => '
							groups.*,
							group_members.user_id as memberUserId,
							group_members.isApproved as memberIsApproved,
							group_members.memberType as memberType
						',
			'where' => array('groups.id = ? AND group_members.user_id = ? ', $group_id, $user_id),
			'join' => array(
				array(
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ? AND group_members.isApproved = 1 AND group_members.memberType = ?', $user_id, GROUP_MEMBER_TYPE_ADMIN)
				)
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'memberUserId');
		return $result;
	}

	public static function checkIsRegistredByNameWithoutId($name, $group_id)
	{
		$group = self::query(array(
			'where' => array('`name` = ? AND id <> ?', $name, $group_id)
		))->fetch();

		if(!is_null($group)) {
			$group = self::instance($group);
			Model_User::addUserIdByKey($group, 'user_id');
		} else {
			$group = false;
		}
		return $group;
	}


	public static function getListJoinedGroupByUserid($user_id)
	{
		$results = self::getList(array(
			'select' => '
							groups.*,
							group_members.user_id as memberUserId,
							group_members.isApproved as memberIsApproved,
							group_members.memberType as memberType
						',
			'where' => array('isAgree = 1'),
			'join' => array(
				array(
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ? AND group_members.isApproved = 1', $user_id)
				)
			)
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'memberUserId');
		}
		return $results;
	}

	public static function getListInterestedByUserid($user_id)
	{
		$results = self::getList(array(
			'select' => '
							groups.*,
							group_members2.user_id as memberUserId,
							group_members2.isApproved as memberIsApproved,
							group_members2.memberType as memberType
						',
			'where' => array('groups.isAgree = 1 AND (group_members2.isApproved IS NULL OR group_members2.isApproved <> 1)'),
			'join' => array(
				array(
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.isApproved = 1')
				),
				array(
					'table' => 'connections',
					'where' => array('connections.friend_id = group_members.user_id AND connections.user_id = ? AND connections.typeApproved = 1', $user_id)
				),
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isConfirmed = 1 AND isRemoved = 0')
				),
				array(
					'type' => 'left',
					'noQuotes' => TRUE,
					'table' => 'group_members AS group_members2',
					'where' => array('group_members2.group_id = groups.id AND group_members2.user_id = ?', $user_id)
				),
			),
			'limit' => 5
		), false);

// Don't delete
//		foreach($results['data'] as $item) {
//			Model_User::addUserIdByKey($item, 'user_id');
//			Model_User::addUserIdByKey($item, 'memberUserId');
//		}
		if(count($results['data']) < 5) {
			$group_keys = array_keys($results['data']);
			if(empty($group_keys)) {
				$group_keys = array(0);
			}

			$results2 = self::getList(array(
				'select' => '
							groups.*,
							group_members2.user_id as memberUserId,
							group_members2.isApproved as memberIsApproved,
							group_members2.memberType as memberType
						',
				'where' => array('groups.isAgree = 1 AND (group_members2.isApproved IS NULL OR group_members2.isApproved = 0 OR group_members2.isApproved <> 1) AND groups.id NOT IN (?)', $group_keys),
				'join' => array(
					array(
						'type' => 'left',
						'noQuotes' => TRUE,
						'table' => 'group_members AS group_members2',
						'where' => array('group_members2.group_id = groups.id AND group_members2.user_id = ?', $user_id)
					),
				),
				'order' => 'groups.id DESC',
				'limit' => (5 - count($results['data']))
			), false);

			$results = array_merge_recursive($results, $results2);
		}

		return $results;
	}

	public static function getListSearchGroups($user_id, $query = array(), $isPageDown = TRUE)
	{
		$where = array(
			'0' => 'groups.user_id <> ? AND groups.isAgree = 1',
			'1' => $user_id
		);

		if(isset($query['groupname']) && $query['groupname']){
			$where[0] .= ' AND groups.name like ?';
			$where[] = '%' . $query['groupname'] . '%';
		}
		if(isset($query['access']) && $query['access']){
			$where[0] .= ' AND groups.accessType in (?)';
			$where[] = explode(',', $query['access']);
		}

		$results = self::getList(array(
			'select' => '
							groups.*,
							group_members.user_id AS memberUserId,
							group_members.isApproved AS memberIsApproved
							',
			'where' => $where,
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ?', $user_id)
				)
			),
			'group' => 'groups.id',
			'order' => 'groups.id DESC'
		), true, false, 10, $isPageDown);


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'memberUserId');
		}
		return $results;
	}

	public static function getGroupsYouMayLike($user_id)
	{
		$results = self::getList(array(
			'where' => array(),
			'limit' => 5
		), FALSE);
	}

}