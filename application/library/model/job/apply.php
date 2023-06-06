<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Job_Apply extends Model{

	protected static $table = 'job_apply';
	protected static $countNewApplicant = FALSE;

	public static function getItemByUseridJobid($user_id, $job_id)
	{
		$apply = self::query(array(
			'where' => array('user_id = ? AND job_id = ?', $user_id, $job_id)
		))->fetch();

		if(!is_null($apply)) {
			$apply = self::instance($apply);
			Model_User::addUserIdByKey($apply, 'user_id');
		} else {
			$apply = false;
		}
		return $apply;
	}

	public static function getItemApplicantByUseridJobid($user_id, $job_id)
	{
		$results = new self(array(
			'select' => '
						users.*,
						users.firstName as userFirstName,
						users.lastName as userLastName,
						users.professionalHeadline as userHeadline,
						users.avaToken as avaToken,
						users.alias as userAlias,
						users.setInvisibleProfile as setInvisibleProfile,
						companies.name as companyName,
						universities.name as universityName,

						job_apply.user_id as jobapplyUserId,
						job_apply.isInvited as jobapplyIsInvited,
						job_apply.isViewed as jobapplyIsViewed,
						job_apply.coverLetter as jobapplyCoverLetter,

						GROUP_CONCAT(skills.name, " (", profile_skills.countEndorse, ")" SEPARATOR ",") as profileSkills
						',
			'where' => array('job_apply.user_id = ? AND job_apply.job_id = ?', $user_id, $job_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = job_apply.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
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
				),
				array(
					'type' => 'left',
					'table' => 'profile_skills',
					'where' => array('profile_skills.user_id = users.id')
				),
				array(
					'type' => 'left',
					'table' => 'skills',
					'where' => array('skills.id = profile_skills.skill_id')
				)
			)
		));

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
			Model_User::addUserIdByKey($item, 'jobapplyUserId');
		}
		return $results;
	}

	public static function getListByUserid($user_id)
	{
		$results = self::getList(array(
			'select' => '
						jobs.*,
						companies.name as name,
						companies.avaToken as avaToken,
						job_apply.user_id as jobapplyUserId,
						job_apply.isInvited as jobapplyIsInvited,
						job_apply.isRemovedJobApplicant as jobapplyIsRemovedJobApplicant,
						job_apply.isRemovedJobOwner as jobapplyIsRemovedJobOwner
						',
			'where' => array('job_apply.user_id = ? AND job_apply.isRemovedJobApplicant = 0', $user_id),
			'join' => array(
				array(
					'table' => 'jobs',
					'where' => array('jobs.id = job_apply.job_id')
				),
				array(
					'table' => 'companies',
					'where' => array('companies.id = jobs.company_id AND companies.isAgree = 1')
				),
			),
			'order' => 'createDate DESC'
		));

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'jobapplyUserId');
		}
		return $results;
	}

	public static function getListApplicantByJobid($job_id)
	{
		$results = self::getList(array(
			'select' => '
						users.*,
						users.firstName as userFirstName,
						users.lastName as userLastName,
						users.professionalHeadline as userHeadline,
						users.avaToken as avaToken,
						users.alias as userAlias,
						users.setInvisibleProfile as setInvisibleProfile,
						companies.name as companyName,
						universities.name as universityName,

						job_apply.user_id as jobapplyUserId,
						job_apply.isInvited as jobapplyIsInvited,
						job_apply.isViewed as jobapplyIsViewed
						',
			'where' => array('job_apply.job_id = ? AND job_apply.isRemovedJobOwner = 0', $job_id),
			'join' => array(
				array(
					'table' => 'jobs',
					'where' => array('jobs.id = job_apply.job_id')
				),
				array(
					'table' => 'users',
					'where' => array('users.id = job_apply.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = job_apply.user_id AND profile_expirience.isCurrent = 1')
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
			'order' => 'job_apply.isViewed ASC, createDate DESC'
		));

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
			Model_User::addUserIdByKey($item, 'jobapplyUserId');
		}
		return $results;
	}

	public static function getItemApplicantByJobidProfileid($job_id, $profile_id)
	{
		$results = new self(array(
			'select' => '
						users.*,
						users.firstName as userFirstName,
						users.lastName as userLastName,
						users.professionalHeadline as userHeadline,
						users.avaToken as avaToken,
						users.alias as userAlias,
						users.setInvisibleProfile as setInvisibleProfile,
						companies.name as companyName,
						universities.name as universityName,

						job_apply.user_id as jobapplyUserId,
						job_apply.isInvited as jobapplyIsInvited,
						job_apply.isViewed as jobapplyIsViewed
						',
			'where' => array('job_apply.job_id = ? AND job_apply.user_id = ? AND job_apply.isRemovedJobOwner = 0', $job_id, $profile_id),
			'join' => array(
				array(
					'table' => 'jobs',
					'where' => array('jobs.id = job_apply.job_id')
				),
				array(
					'table' => 'users',
					'where' => array('users.id = job_apply.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = job_apply.user_id AND profile_expirience.isCurrent = 1')
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
		));

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
			Model_User::addUserIdByKey($item, 'jobapplyUserId');
		}
		return $results;
	}

	public static function getCountAllNewApplicant ($user_id)
	{
		if(static::$countNewApplicant === FALSE) {
			$result = self::query(array(
				'select' => '
						COUNT(job_apply.job_id) as countItems
						',
				'where' => array('job_apply.isViewed = 0'),
				'join' => array(
					array(
						'table' => 'jobs',
						'where' => array('jobs.id = job_apply.job_id AND jobs.user_id = ?', $user_id)
					)
				)
			))->fetch();

			static::$countNewApplicant = self::instance($result)->countItems;
		}

		return static::$countNewApplicant;
	}
}