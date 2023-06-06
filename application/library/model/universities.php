<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Universities extends Model{

	protected static $table = 'universities';
	protected static $countNewStaffMember = FALSE;

	public static function getItemById($school_id, $user_id = false)
	{
		$result = new self(array(
			'select' => '
							universities.*,
							university_follow.user_id as followUserId,
							university_follow.user_id as memberUserId
						',
			'where' => array('id = ? AND universities.user_id IS NOT NULL AND (isAgree = 1 OR universities.user_id = ?)', $school_id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'university_follow',
					'where' => array('university_follow.univercity_id = universities.id AND university_follow.user_id = ?', $user_id)
				)
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'followUserId');
		Model_User::addUserIdByKey($result, 'memberUserId');

		return $result;
	}



	/**
	 * Check university by id.
	 *
	 * @param  string $id - University id
	 * @return bool|this - Object or false
	 */
	public static function checkItemById($id)
	{
		$university = self::query(array(
			'where' => array('`id` = ?', $id)
		))->fetch();

		if (!is_null($university)) {
			$university = self::instance($university);
		} else {
			$university = false;
		}
		return $university;
	}

	/**
	 * Check university by name. Name is trimed.
	 *
	 * @param  string $name - University name
	 * @return bool|this - Object or false
	 */
	public static function checkItemByName($name)
	{
		$name = trim($name);
		return self::getByName($name);
	}

	public static function getUserIdSchoolId($user_id, $school_id)
	{
		$result = new self(array(
			'where' => array('id = ? AND user_id = ? ', $school_id, $user_id),
		));

		Model_User::addUserIdByKey($result, 'user_id');
		return $result;
	}

	public static function getByName($name)
	{
		$school = self::query(array(
			'where' => array('`name` = ?', $name)
		))->fetch();

		if(!is_null($school)) {
			$school = self::instance($school);
			Model_User::addUserIdByKey($school, 'user_id');
		} else {
			$school = false;
		}
		return $school;
	}

	public static function checkIsRegistredByName($name)
	{
		$school = self::query(array(
			'where' => array('`name` = ? AND user_id IS NOT NULL', $name)
		))->fetch();

		if(!is_null($school)) {
			$school = self::instance($school);
			Model_User::addUserIdByKey($school, 'user_id');
		} else {
			$school = false;
		}
		return $school;
	}

	public static function checkIsEmailCorporate($email)
	{
		$email = strtolower($email);
		$tmp = explode('@', $email);
		$domain = (isset($tmp[1])) ? $tmp[1] : false;

		$settings = System::$global->settings;

		if($settings['blockMailServers'] == 1) {
			$emailagents = explode(',', $settings['mailServers']);

			foreach($emailagents as $key => $agent) {
				$emailagents[$key] = strtolower(trim($agent));
			}

			if(in_array($domain, $emailagents)) {
				return false;
			} else {
				return true;
			}

		} else {
			return true;
		}
	}

	public static function checkRegisteredByEmail($email)
	{
		$email = strtolower($email);
		return self::exists(array('email = ?', $email));
	}



	public static function checkIsRegistredByNameWithoutId($name, $school_id)
	{
		$school = self::query(array(
			'where' => array('`name` = ? AND id <> ? AND user_id IS NOT NULL', $name, $school_id)
		))->fetch();

		if(!is_null($school)) {
			$school = self::instance($school);
			Model_User::addUserIdByKey($school, 'user_id');
		} else {
			$school = false;
		}
		return $school;
	}


	public static function getListMySchools($user_id)
	{
		return self::getList(array(
			'where' => array('user_id = ? AND isRegistered = 1', $user_id)
		), false);
	}


	public static function getListSearchSchool($user_id, $query = array(), $isPageDown = TRUE)
	{
		$where = array(
			'0' => 'universities.user_id <> ? AND isAgree = 1',
			'1' => $user_id
		);

		if(isset($query['schoolname']) && $query['schoolname']){
			$where[0] .= ' AND universities.name like ?';
			$where[] = '%' . $query['schoolname'] . '%';
		}
		if(isset($query['type']) && $query['type']){
			$where[0] .= ' AND universities.type in (?)';
			$where[] = explode(',', $query['type']);
		}
		if(isset($query['typeschool']) && $query['typeschool']){
			$where[0] .= ' AND universities.type in (?)';
			$where[] = explode(',', $query['typeschool']);
		}

		$results = self::getList(array(
			'select' => '
							universities.*,
							university_follow.user_id AS followUserId
							',
			'where' => $where,
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'university_follow',
					'where' => array('university_follow.univercity_id = universities.id AND university_follow.user_id = ?', $user_id)
				)
			),
			'group' => 'universities.id',
			'order' => 'universities.id DESC'
		), true, false, 10, $isPageDown);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'followUserId');
		}
		return $results;
	}


	public static function getListInterestedSchoolByUserid($user_id)
	{
		$results = self::getList(array(
			'select' => '
							universities.*,
							COUNT(universities.id) as countItem,
							university_follow_me.user_id as memberUserId
			',
			'where' => array('connections.user_id = ? AND connections.typeApproved = ? AND university_follow_me.user_id IS NULL', $user_id, ADDCONNECTION_APPROVED),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'table' => 'university_follow',
					'where' => array('university_follow.user_id = users.id ')
				),
				array(
					'table' => 'universities',
					'where' => array('universities.id = university_follow.univercity_id AND universities.isAgree = 1 AND universities.user_id <> ?', $user_id)
				),
				array(
					'type' => 'left',
					'noQuotes' => TRUE,
					'table' => 'university_follow AS university_follow_me',
					'where' => array('university_follow_me.user_id = ? AND university_follow_me.univercity_id = universities.id', $user_id)
				),
			),
			'group' => 'universities.id',
			'order' => 'countItem DESC',
			'limit' => 6
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'memberUserId');
		}
		return $results;
	}

	public static function getListInterestedSchoolByUserid_WithoutNyFriendsFollow($user_id)
	{
		$results = self::getList(array(
			'select' => '
							universities.*,
							university_follow.user_id as memberUserId
			',
			'where' => array('universities.isAgree = 1 AND universities.user_id <> ? AND university_follow.user_id IS NULL', $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'university_follow',
					'where' => array('university_follow.user_id = ? AND university_follow.univercity_id = universities.id', $user_id)
				),
			),
			'order' => 'countFollowers DESC',
			'limit' => 6
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'memberUserId');
		}
		return $results;
	}


	public static function getCountNewStaffMember($user_id, $school_id = FALSE)
	{
		if (static::$countNewStaffMember === FALSE) {
			$result = self::getList(array(

				'select' => '
						COUNT(profile_expirience.id) as countItems,
						universities.id AS id
						',
				'where'  => array('universities.user_id = ? AND isAgree = 1 AND isRegistered = 1', $user_id),
				'join'   => array(
					array(
						'table' => 'profile_expirience',
						'where' => array('profile_expirience.university_id = universities.id AND isSchoolMember = 0')
					)
				),
				'group'  => 'universities.id'
			), FALSE);

			static::$countNewStaffMember = array();
			foreach ($result['data'] as $id => $item) {
				static::$countNewStaffMember[$id] = $item->countItems;
			}
		}

		if ($school_id) {
			if (isset(static::$countNewStaffMember[$school_id])) {
				return static::$countNewStaffMember[$school_id];
			} else {
				return FALSE;
			}
		} else {
			$count = 0;
			foreach (static::$countNewStaffMember as $id => $sum) {
				$count = $count + $sum;
			}
			return $count;
		}
	}

	/**
	 * Get sorted list universities for autocomplete by text, if it is.
	 *
	 * @param  bool|string $text - Search text or FALSE
	 * @return Array Objects - List result
	 */
	public static function getList_OrderCountUsed($text = false)
	{
		$where = array('id <> 0');
		if ($text) {
			$where[0] .= ' AND universities.name like ?';
			$where[] = '%' . strtolower($text) . '%';
		}


		return self::getList(array(
			'where' => $where,
			'order' => 'countUsed DESC, id DESC'
		), TRUE, FALSE, 100);
	}

	public static function isOwnerSchool($user_id, $school_id){
        $school = self::query(array(
            'where' => array('`id` = ? AND `user_id` = ?', $school_id, $user_id)
        ))->fetch();
        if($school->id){
            return true;
        }
        return false;
	}
}