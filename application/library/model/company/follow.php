<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Company_Follow extends Model{

	protected static $table = 'company_follow';

	public static function getListByCompanyId($company_id)
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
			'where' => array('company_follow.company_id = ? ', $company_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = company_follow.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = company_follow.user_id AND profile_expirience.isCurrent = 1')
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

	public static function getListByUserId($user_id)
	{
		$results = self::getList(array(
			'select' => '	companies.*,
							company_follow.user_id as followUserId
						',
			'where' => array('company_follow.user_id = ? ', $user_id),
			'join' => array(
				array(
					'table' => 'companies',
					'where' => array('companies.id = company_follow.company_id AND companies.user_id IS NOT NULL')
				)
			)
		), TRUE, 'companies.id', 6, FALSE);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'followUserId');
		}
		return $results;
	}

	public static function getListCompanyIdByUserId($user_id)
	{
		return self::getList(array(
			'select' => '	companies.id as id
						',
			'where' => array('company_follow.user_id = ? ', $user_id),
			'join' => array(
				array(
					'table' => 'companies',
					'where' => array('companies.id = company_follow.company_id AND companies.user_id IS NOT NULL')
				)
			)
		), false);
	}

	public static function checkFollowOtherCompanies($user_id)
	{
		$follow = self::query(array(
			'select' => '	companies.id
						',
			'where' => array('companies.user_id <> ? ', $user_id),
			'join' => array(
				array(
					'table' => 'companies',
					'where' => array('companies.id = company_follow.company_id AND company_follow.user_id = ?', $user_id)
				)
			)
		))->fetch();

		if(!is_null($follow)) {
			$follow = true;
		} else {
			$follow = false;
		}
		return $follow;
	}
}