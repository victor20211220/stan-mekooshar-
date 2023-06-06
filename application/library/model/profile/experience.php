<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_Experience extends Model{

	protected static $table = 'profile_expirience';

	public static function getItemById($id, $user_id)
	{
		$item = self::query(array(
			'select' => '	profile_expirience.*,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('profile_expirience.id = ? AND profile_expirience.user_id = ?', $id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('company_id = companies.id'),
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('university_id = universities.id'),
				)
			)
		))->fetch();
		$item = self::instance($item);
		return $item;
	}

	public static function getItemMemeberByIdSchoolid($profile_experiance_id, $school_id)
	{
//		dump($profile_experiance_id);
//		dump($school_id, 1);
		$item = self::query(array(
			'select' => '	profile_expirience.id,
							profile_expirience.id as profileExperianceId,
							profile_expirience.isSchoolMember as profileSchoolMember,
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
			'where' => array('profile_expirience.id = ? AND profile_expirience.university_id = ?', $profile_experiance_id, $school_id),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('profile_expirience.university_id = universities.id AND universities.user_id IS NOT NULL AND universities.isAgree = 1'),
				),
				array(
					'table' => 'users',
					'where' => array('users.id = profile_expirience.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'noQuotes' => TRUE,
					'table' => 'profile_expirience as profile_expirience2',
					'where' => array('profile_expirience2.user_id = users.id AND profile_expirience2.isCurrent = 1')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = profile_expirience2.company_id')
				)
			)
		))->fetch();

		$item = self::instance($item);
		return $item;
	}

	public static function getTwoLastByUser($user_id)
	{
		return self::getListByUser($user_id, true);
	}

	public static function getListByUser($user_id, $getTwoLast = false)
	{
		return self::getList(array(
			'select' => '	profile_expirience.*,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('profile_expirience.user_id = ?', $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('company_id = companies.id'),
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('university_id = universities.id'),
				)
			),
			'order' => 'dateFrom DESC, profile_expirience.id DESC',
			'limit' => ($getTwoLast) ? '2' : '1000'
		), false);
	}

	public static function isInExperienceCompany($company_id1, $company_id2)
	{
		return self::getList(array(
			'select' => '	company_id as id',
			'where' => array('company_id = ? OR company_id = ?', $company_id1, $company_id2),
			'group' => 'company_id',
			'limit' => 2
		), false, false);
	}

	public static function changePlacesCompanyId($company_id1, $company_id2)
	{
		$db = static::getDatabase();
		return $db->query('UPDATE `profile_expirience` AS t1 JOIN `profile_expirience` t2 ON (t1.company_id = ' . $company_id1 . ' AND t2.company_id = ' . $company_id2 . ') SET t1.company_id = t2.company_id, t2.company_id = t1.company_id')->count();
	}



	public static function checkUniversityBySchoolidUserid($school_id, $user_id)
	{
		$school = self::query(array(
			'where' => array('profile_expirience.user_id = ? AND universities.id = ?', $user_id, $school_id),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('universities.id = profile_expirience.university_id')
				)
			)
		))->fetch();

		if(!is_null($school)) {
			$school = self::instance($school);
		} else {
			$school = false;
		}
		return $school;
	}


	public static function getListStaffMemberBySchoolid($school_id, $isSchoolMember = array(SCHOOL_TYPEMEMBER_SENT_REQUEST, SCHOOL_TYPEMEMBER_STAFF))
	{
		$user = Auth::getInstance()->getIdentity();

		return self::getList(array(
			'select' => '
							profile_expirience.id,
							profile_expirience.id as profileExperianceId,
							profile_expirience.isSchoolMember as profileSchoolMember,
							users.id as userId,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName,
							CONCAT(users.firstName, " ", users.lastName) as userFullName
						',
			'where' => array('profile_expirience.university_id in (?) AND profile_expirience.isSchoolMember in (?)', $school_id, $isSchoolMember),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('profile_expirience.university_id = universities.id'),
				),
				array(
					'table' => 'users',
					'where' => array('users.id = profile_expirience.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'noQuotes' => TRUE,
					'table' => 'profile_expirience as profile_expirience2',
					'where' => array('profile_expirience2.user_id = profile_expirience2.user_id AND profile_expirience.isCurrent = 1')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = profile_expirience2.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'connections',
					'where' => array('connections.friend_id = users.id AND connections.user_id = ? AND connections.typeApproved <> ?', $user->id, ADDCONNECTION_DENY)
				)
			),
			'order' => 'userFullName ASC'
		), FALSE);
	}


}

