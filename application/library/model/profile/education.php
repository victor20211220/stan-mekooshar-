<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_Education extends Model{

	protected static $table = 'profile_education';

	public static function getItemById($id, $user_id)
	{
		$item = self::query(array(
			'select' => '	profile_education.*,
							universities.name as universityName',
			'where' => array('profile_education.id = ? AND profile_education.user_id = ?', $id, $user_id),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('university_id = universities.id'),
				)
			)
		))->fetch();
		$item = self::instance($item);
		return $item;
	}

	public static function getItemStudentByIdSchoolid($id, $school_id)
	{
		$item = self::query(array(
			'select' => '	users.id,
							profile_education.id as profileEducationId,
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
			'where' => array('profile_education.id = ? AND profile_education.university_id = ?', $id, $school_id),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('profile_education.university_id = universities.id AND universities.user_id IS NOT NULL AND universities.isAgree = 1'),
				),
				array(
					'table' => 'users',
					'where' => array('users.id = profile_education.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = profile_education.user_id AND profile_expirience.isCurrent = 1')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = profile_expirience.company_id')
				)
			)
		))->fetch();
		$item = self::instance($item);
		return $item;
	}

	public static function getOneLastByUser($user_id)
	{
		return self::getListByUser($user_id, true);
	}

	public static function getListByUser($user_id, $getOneLast = false)
	{
		return self::getList(array(
			'select' => '	profile_education.*,
							universities.name as universityName',
			'where' => array('profile_education.user_id = ?', $user_id),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('university_id = universities.id'),
				)
			),
			'order' => 'yearFrom DESC, profile_education.id DESC',
			'limit' => ($getOneLast) ? '1' : '1000'
		), false);
	}

	public static function checkUniversityByNameUserid_WithoutName($name, $user_id, $without_school_name)
	{
		$school = self::query(array(
			'where' => array('profile_education.user_id = ? AND universities.name = ? AND name <> ?', $user_id, $name, $without_school_name),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('universities.id = profile_education.university_id')
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


	public static function checkUniversityByIdUserid_WithoutName($school_id, $user_id, $without_school_name)
	{
		if(!$without_school_name) {
			$without_school_name = '';
		}
		$school = self::query(array(
			'where' => array('profile_education.user_id = ? AND profile_education.university_id = ? AND universities.name <> ?', $user_id, $school_id, $without_school_name),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('universities.id = profile_education.university_id')
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

	public static function checkUniversityBySchoolidUserid($school_id, $user_id)
	{
		$school = self::query(array(
			'where' => array('profile_education.user_id = ? AND universities.id = ?', $user_id, $school_id),
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('universities.id = profile_education.university_id')
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



	public static function getListBySchoolId($school_id, $query = FALSE, $order = FALSE, $isLimit = TRUE)
	{
		$where = array('profile_education.university_id in (?)', $school_id);

		if($query) {
			if(isset($query['year']) && !empty($query['year'])) {
				$where[0] .= ' AND profile_education.yearTo like ?';
				$where[] = '%' . $query['year'] . '%';
			}
			if(isset($query['find']) && !empty($query['find'])) {
				$where[0] .= ' AND CONCAT(users.firstName, " ", users.lastName, " ", users.firstName) like ?';
				$where[] = '%' . $query['find'] . '%';
			}
			if(isset($query['isNotableAlumni'])) {
				$where[0] .= ' AND isNotableAlumni = 1';
			}
		}

		if($order) {
			$order = 'userFullName ASC';
		} else {
			$order = 'profile_education.id DESC';
		}

		$user = Auth::getInstance()->getIdentity();

		return self::getList(array(
			'select' => '
							profile_education.id,
							profile_education.id as profileEducationId,
							profile_education.yearTo,
							users.id as userId,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName,
							CONCAT(users.firstName, " ", users.lastName) as userFullName,
							connections.typeApproved as connectionsTypeApproved
						',
			'where' => $where,
			'join' => array(
				array(
					'table' => 'universities',
					'where' => array('university_id = universities.id'),
				),
				array(
					'table' => 'users',
					'where' => array('users.id = profile_education.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = profile_education.user_id AND profile_expirience.isCurrent = 1')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = profile_expirience.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'connections',
					'where' => array('connections.friend_id = users.id AND connections.user_id = ? AND connections.typeApproved <> ?', $user->id, ADDCONNECTION_DENY)
				),
			),
			'order' => $order,
		), $isLimit, false, 100, TRUE);
	}






}

