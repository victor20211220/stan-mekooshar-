<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Group_Members extends Model{

	protected static $table = 'group_members';
	protected static $countNewMember = FALSE;
	protected static $countNewMemberByGroupid = FALSE;

	public static function getListMembersAdminsByGroupid_WithoutMe($group_id, $user_id){
		return self::getListMembersByGroupid($group_id, GROUP_MEMBER_TYPE_ADMIN, true, $user_id);
	}

	public static function getListMembersByGroupid($group_id, $typeMembers = false, $isApproved = TRUE, $without_user_id = false)
	{
		$where = array('group_id = ? AND isApproved = ?', $group_id, $isApproved);

		switch($typeMembers){
			case GROUP_MEMBER_TYPE_ADMIN:
			case GROUP_MEMBER_TYPE_USER:
				$where[0] .= ' AND memberType = ?';
				$where[] = $typeMembers;
				break;
		}

		if($without_user_id) {
			$where[0] .= ' AND group_members.user_id <> ?';
			$where[] = $without_user_id;
		}

		$results = self::getList(array(
			'select' => '
							group_members.memberType,
							users.id,
							users.id as userId,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => $where,
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = group_members.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = users.id AND profile_expirience.isCurrent = 1')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = profile_expirience.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('universities.id = profile_expirience.university_id')
				)
			),
			'order' => 'group_members.createDate DESC'
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'userId');
		}
		return $results;
	}

	public static function getListMembersByGroupidProfilesids($group_id, array $profile_ids, $isApproved = TRUE)
	{
		$where = array('group_id = ? AND isApproved = ? AND user_id in (?)', $group_id, $isApproved, $profile_ids);

		$results = self::getList(array(
			'select' => '
							group_members.*,
							users.id',
			'where' => $where,
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = group_members.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
			),
			'order' => 'group_members.createDate DESC'
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'userId');
		}
		return $results;
	}


	public static function getCountMemberRequestsByGroupid($group_id)
	{
		$counter = new self(array(
			'select' => '	COUNT(user_id) as countRequest
							',
			'where' => array('group_id = ? AND isApproved = 0 AND memberType = ?', $group_id, GROUP_MEMBER_TYPE_USER)
		), false);

		return $counter->countRequest;
	}

	public static function getCountMemberByGroupid($group_id)
	{
		$counter = new self(array(
			'select' => '	COUNT(user_id) as countRequest
							',
			'where' => array('group_id = ? AND isApproved = 1 AND memberType = ?', $group_id, GROUP_MEMBER_TYPE_USER)
		), false);

		return $counter->countRequest;
	}

	public static function getListByUserId($user_id)
	{
		$results = self::getList(array(
			'select' => '	groups.*,
							group_members.user_id as memberUserId,
							group_members.isApproved as memberIsApproved
						',
			'where' => array('group_members.user_id = ? AND group_members.isApproved = 1', $user_id),
			'join' => array(
				array(
					'table' => 'groups',
					'where' => array('groups.id = group_members.group_id AND groups.isAgree = 1')
				)
			)
		), TRUE, 'groups.id', 6, FALSE);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'memberUserId');
		}
		return $results;
	}

	public static function checkIsGroupAdmin($user_id, $group_id)
	{
		$groupMember = self::query(array(
			'where' => array('group_id = ? AND user_id = ? AND memberType = ? AND isApproved = 1', $group_id, $user_id, GROUP_MEMBER_TYPE_ADMIN)
		))->fetch();

		if(!is_null($groupMember)) {
			$groupMember = true;
		} else {
			$groupMember = false;
		}

		return $groupMember;
	}

	public static function getCountAllNewMember($user_id)
	{
		if(static::$countNewMember === FALSE) {
			$result = self::query(array(

				'select' => '
						COUNT(group_members.user_id) as countItems
						',
				'where' => array('group_members.isApproved = 0'),
				'join' => array(
					array(
						'noQuotes' => TRUE,
						'table' => 'group_members as adminGroupMember',
						'where' => array('adminGroupMember.group_id = group_members.group_id AND adminGroupMember.memberType = ? AND adminGroupMember.isApproved = 1 AND adminGroupMember.user_id = ?', GROUP_MEMBER_TYPE_ADMIN, $user_id)
					)
				)
			))->fetch();

			static::$countNewMember = self::instance($result)->countItems;
		}

		return static::$countNewMember;
	}

	public static function getCountAllNewMemberByGroupid($user_id, $group_id)
	{
		if(static::$countNewMemberByGroupid === FALSE) {
			$result = self::getList(array(

				'select' => '
						COUNT(group_members.user_id) AS countItems,
						group_members.group_id AS id
						',
				'where' => array('group_members.isApproved = 0'),
				'join' => array(
					array(
						'noQuotes' => TRUE,
						'table' => 'group_members as adminGroupMember',
						'where' => array('adminGroupMember.group_id = group_members.group_id AND adminGroupMember.memberType = ? AND adminGroupMember.isApproved = 1 AND adminGroupMember.user_id = ?', GROUP_MEMBER_TYPE_ADMIN, $user_id)
					)
				),
				'group' => 'group_members.group_id'
			), FALSE);

			static::$countNewMemberByGroupid = array();
			foreach($result['data'] as $id => $item) {
				static::$countNewMemberByGroupid[$id] = $item->countItems;
			}
		}
		if(isset(static::$countNewMemberByGroupid[$group_id])) {
			return static::$countNewMemberByGroupid[$group_id];
		} else {
			return FALSE;
		}
	}
}