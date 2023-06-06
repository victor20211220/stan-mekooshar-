<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_University_Follow extends Model{

	protected static $table = 'university_follow';


	public static function getListBySchoolId($school_id)
	{
		$results = self::getList(array(
			'select' => '	users.id,
							users.id as userId,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName
						',
			'where' => array('university_follow.univercity_id = ? ', $school_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = university_follow.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = university_follow.user_id AND profile_expirience.isCurrent = 1')
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
			)
		), false, false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'userId');
		}
		return $results;
	}



	public static function getListSchoolsIdByUserId($user_id)
	{
		$results = self::getList(array(
			'select' => '	universities.*
						',
			'where' => array('university_follow.user_id = ? ', $user_id),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('universities.id = university_follow.univercity_id AND universities.user_id IS NOT NULL')
				)
			)
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
		}
		return $results;
	}
}