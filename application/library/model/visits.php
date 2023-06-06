<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Visits extends Model{

	protected static $table = 'visits';

//	public static function insertShowResult($results)
//	{
//		$update = '';
//		foreach($results['data'] as $result){
//		$update .= ',( NULL,' . $result->id . ',CURRENT_TIMESTAMP)';
//		}
//		$update = 'INSERT INTO `connection_search_result`(`id`,`profile_id`,`createDate`) VALUES ' . substr($update, 1);
//
//		$db = static::getDatabase();
//		return $db->query($update)->count();
//	}

	public static function countVisits($user_id)
	{
		$result = new self (array(
			'select' => 'COUNT(DISTINCT(visits.user_id)) as countItem',
			'where' => array('visits.profile_id = ? AND visits.createDate >= ?', $user_id, date('Y-m-d 00:00:00', (time() - 60*60*24*15))),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = visits.profile_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				)
			)
		), false);

		return $result->countItem;
	}

	public static function getListWhoVisitMyProfileByUserId($user_id)
	{
		$results = self::getList(array(
			'select' => '	DISTINCT(visits.user_id) as id,
							visits.createDate as createDate,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('visits.profile_id = ? AND visits.createDate >= ?', $user_id, date('Y-m-d 00:00:00', (time() - 60*60*24*15))),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = visits.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
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
//			'group' => 'visits.user_id',
			'order' => 'visits.createDate'
		), true, false, 10);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}

	public static function getListAlsoViewedConnectionsByUser($user_id)
	{
		$results = self::getList(array(
			'select' => '	users.id as id,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('users.id <> ? AND visits.profile_id IS NOT NULL', $user_id),
			'join' => array(
				array(
					'noQuotes' => true,
					'table' => 'connections',
					'where' => array('visits.user_id = connections.friend_id AND connections.typeApproved = 1 AND connections.user_id = ?', $user_id)
				),
				array(
					'table' => 'users',
					'where' => array('users.id = visits.profile_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
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
			'group' => 'id',
			'order' => 'visits.createDate DESC, users.id DESC',
			'limit' => 6
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}

	public static function getListMonthStatisticByUser($user_id)
	{
		return self::getList(array(
			'select' => '	DATE_FORMAT(createDate, "%Y-%m-01") as id,
							COUNT(DISTINCT(user_id)) as countItems',
			'where' => array('profile_id = ? AND createDate >= ?', $user_id, date('Y-m-01', (time() - 60*60*24*30*5))),
			'group' => 'MONTH(createDate)',
			'order' => 'createDate DESC'
		));
	}

	public static function getListDaysStatisticByUser($user_id)
	{
		return self::getList(array(
			'select' => '	DATE_FORMAT(createDate, "%Y-%m-%d") as id,
							COUNT(DISTINCT(user_id)) as countItems',
			'where' => array('profile_id = ? AND createDate >= ?', $user_id, date('Y-m-01', (time() - 60*60*24*28))),
			'group' => 'DAY(createDate)',
			'order' => 'createDate DESC'
		));
	}

	public static function getListMyVisits($user_id)
	{
		$results = self::getList(array(
			'select' => '	users.id as id,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('visits.user_id = ? AND visits.profile_id IS NOT NULL', $user_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = visits.profile_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
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
			'group' => 'id',
			'order' => 'visits.createDate DESC, users.id DESC',
			'limit' => 4
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}


// COMPANY
// ---------------------------------------------------------------------------------------------------------------------
	public static function getListCompanyAlsoViewedConnectionsByUser($user_id)
	{
		$results = self::getList(array(
			'select' => '	companies.*,
							company_follow.user_id as followUserId
							',
			'where' => array('users.id <> ? AND visits.company_id IS NOT NULL', $user_id),
			'join' => array(
				array(
					'noQuotes' => true,
					'table' => 'connections',
					'where' => array('visits.user_id = connections.friend_id AND connections.typeApproved = 1 AND connections.user_id = ?', $user_id)
				),
				array(
					'table' => 'users',
					'where' => array('users.id = visits.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'table' => 'companies',
					'where' => array('companies.id = visits.company_id AND companies.user_id <> ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				)
			),
			'group' => 'id',
			'order' => 'visits.createDate DESC, users.id DESC',
			'limit' => 6
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'followUserId');
		}
		return $results;
	}


	public static function getListMyVisitsCompanies($user_id)
	{
		$results = self::getList(array(
			'select' => '	companies.*,
							company_follow.user_id as followUserId
							',
			'where' => array('visits.user_id = ? AND visits.company_id IS NOT NULL', $user_id),
			'join' => array(
				array(
					'table' => 'companies',
					'where' => array('companies.id = visits.company_id AND companies.user_id <> ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				)
			),
			'group' => 'id',
			'order' => 'visits.createDate DESC, visits.id DESC',
			'limit' => 5
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'followUserId');
		}
		return $results;
	}



// GROUP
// ---------------------------------------------------------------------------------------------------------------------
	public static function getListGroupAlsoViewedConnectionsByUser($user_id)
	{
		$results = self::getList(array(
			'select' => '	groups.*,
							group_members.user_id as memberUserId,
							group_members.isApproved as memberIsApproved
							',
			'where' => array('users.id <> ? AND groups.isAgree = 1 AND visits.group_id IS NOT NULL', $user_id),
			'join' => array(
				array(
					'noQuotes' => true,
					'table' => 'connections',
					'where' => array('visits.user_id = connections.friend_id AND connections.typeApproved = 1 AND connections.user_id = ?', $user_id)
				),
				array(
					'table' => 'users',
					'where' => array('users.id = visits.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'table' => 'groups',
					'where' => array('groups.id = visits.group_id AND groups.user_id <> ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ?', $user_id)
				)
			),
			'group' => 'id',
			'order' => 'visits.createDate DESC, users.id DESC',
			'limit' => 6
		), false);


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'memberUserId');
		}
		return $results;
	}
}