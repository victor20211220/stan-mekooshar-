<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_Blocked extends Model{

	protected static $table = 'profile_blocked';

	/**
	 * Check is profile in my block list
	 *
	 * @param  int  $profile_id - User id
	 * @return bool
	 */
	public static function checkIsBlockedUser($profile_id)
	{
		$user = Auth::getInstance()->getIdentity();

		$result = self::query(array(
			'where' => array('user_id = ? AND profile_id = ?', $user->id, $profile_id)
		))->fetch();

		if(!is_null($result)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Check isset my id in block list user
	 *
	 * @param  int  $profile_id - User id
	 * @return bool
	 */
	public static function checkIsIInBlockListUser($profile_id)
	{
		$user = Auth::getInstance()->getIdentity();

		$result = self::query(array(
			'where' => array('user_id = ? AND profile_id = ?', $profile_id, $user->id)
		))->fetch();

		if(!is_null($result)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function getListBlockMeUser($users_id)
	{
		$user = Auth::getInstance()->getIdentity();

		$results = self::getList(array(
			'select' => '
						CONCAT(user_id, "_", profile_id) as id,
						profile_blocked.*
						',
			'where' => array('user_id in (?) AND profile_id = ?', $users_id, $user->id)
		), FALSE);

		// Without Model_User::addUserIdByKey
		return $results;
	}


	public static function getListBlockedUser()
	{
		$user = Auth::getInstance()->getIdentity();

		$results = self::getList(array(
			'select' => '
						profile_blocked.*,
						users.id AS id,
						users.firstName AS userFirstName,
						users.lastName AS userLastName,
						users.professionalHeadline AS userHeadline,
						users.avaToken AS avaToken,
						users.alias AS userAlias,
						users.setInvisibleProfile AS setInvisibleProfile,
						companies.name AS companyName,
						universities.name AS universityName
						',
			'where' => array('profile_blocked.user_id = ?', $user->id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = profile_blocked.profile_id AND users.isConfirmed = 1 AND users.isRemoved = 0')
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
			'order' => 'createDate DESC'
		), FALSE);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}

		return $results;
	}


}