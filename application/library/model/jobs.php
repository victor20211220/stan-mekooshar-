<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Jobs extends Model{

	protected static $table = 'jobs';

	public static function getListByUserid($user_id)
	{
		$results = self::getList(array(
			'select' => '
							jobs.*,
							companies.name as companyName
						',
			'where' => array('jobs.user_id = ? AND isRemoved = 0', $user_id),
			'join' => array(
				array(
					'table' => 'companies',
					'where' => array('companies.id = jobs.company_id AND companies.isAgree = 1 AND companies.user_id = ?', $user_id)
				)
			),
			'order' => 'expiredDate DESC, id DESC'
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
		}
		return $results;
	}

	public static function getListSearch($user_id, $query, $isPaginator = FALSE)
	{
		$industries = array();
		if(!empty($query['industries'])) {
			$industries = explode(',', $query['industries']);
		}
		if(!empty($query['industryjob'])) {
			$tmp = explode(',', $query['industryjob']);
			$industries = array_merge($industries, $tmp);
		}


		$skills = array();
		if(!empty($query['skills'])) {
			$skills = explode(',', $query['skills']);
		}
		if(!empty($query['skilljob'])) {
			$skills = explode(',', $query['skilljob']);
		}


		$where = array('jobs.user_id <> ? AND isRemoved = 0 AND jobs.expiredDate > ?', $user_id, CURRENT_DATETIME);
		if(!empty($industries)) {
			$where[0] .= ' AND jobs.industry in (?)';
			$where[] = $industries;
		}
		if(!empty($skills)) {
			$where[0] .= ' AND skills.id in (?)';
			$where[] = $skills;
		} else {
			$skills = array(0);
		}
		if(!empty($query['country']) && $query['country'] != 'all') {
			$where[0] .= ' AND jobs.country = ?';
			$where[] = $query['country'];
		} elseif(!empty($query['regionjob'])){
			$where[0] .= ' AND jobs.country in (?)';
			$where[] = explode(',', $query['regionjob']);
		}
		if(!empty($query['state']) && $query['state'] != 'all') {
			$where[0] .= ' AND jobs.state LIKE  ?';
			$where[] = '%' . $query['state'] . '%';
		} elseif (!empty($query['state1']) && $query['state1'] != 'all') {
			$where[0] .= ' AND jobs.state LIKE  ?';
			$where[] = '%' . $query['state1'] . '%';
		}
		if(!empty($query['city'])) {
			$where[0] .= ' AND jobs.city LIKE  ?';
			$where[] = '%' . $query['city'] . '%';
		}
		if(!empty($query['jobname'])) {
			$where[0] .= ' AND jobs.title LIKE  ?';
			$where[] = '%' . $query['jobname'] . '%';
		}
//		dump($where, 1);

		$results = self::getList(array(
			'select' => '
						jobs.*,
						companies.name as name,
						companies.avaToken as avaToken,
						job_apply.user_id as jobapplyUserId,
						job_apply.isInvited as jobapplyIsInvited,
						job_apply.isRemovedJobApplicant as jobapplyIsRemovedJobApplicant
						',
			'where' => $where,
			'join' => array(
				array(
					'table' => 'companies',
					'where' => array('companies.id = jobs.company_id AND companies.isAgree = 1')
				),
				array(
					'type' => 'left',
					'table' => 'job_skills',
					'where' => array('job_skills.job_id = jobs.id AND job_skills.skill_id in (?)', $skills)
				),
				array(
					'type' => 'left',
					'table' => 'skills',
					'where' => array('skills.id = job_skills.skill_id')
				),
				array(
					'type' => 'left',
					'table' => 'job_apply',
					'where' => array('job_apply.job_id = jobs.id AND job_apply.user_id = ?', $user_id)
				)

			),
			'order' => 'jobs.activateDate DESC, jobs.id DESC'
		), $isPaginator, false, 20, true);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'jobapplyUserId');
		}
		return $results;
	}



	public static function getItemByIdUserid($job_id, $user_id = false)
	{
		if($user_id) {
			$where = array('jobs.id = ? AND jobs.isRemoved = 0 AND jobs.user_id = ?', $job_id, $user_id);
		} else {
			$where = array('jobs.id = ? AND jobs.isRemoved = 0', $job_id);
		}

		$user = Auth::getInstance()->getIdentity();
		$result = new self(array(
			'select' => '
							jobs.*,
							companies.id AS companyId,
							companies.name AS name,
							companies.name as companyName,
							companies.industry as companyIndustry,
							companies.avaToken AS avaToken,
							users.firstName AS ownerCompanyFirstName,
							users.lastName AS ownerCompanyLastName,
							GROUP_CONCAT(skills.name SEPARATOR ", ") AS skillsName,
							GROUP_CONCAT(skills.id SEPARATOR ", ") AS skillsId,
							job_apply.user_id as jobapplyUserId,
							job_apply.isInvited as jobapplyIsInvited,
							job_apply.isRemovedJobApplicant as jobapplyIsRemovedJobApplicant,
							job_apply.isRemovedJobOwner as jobapplyIsRemovedJobOwner
						',
			'where' => $where,
			'join' => array(
				array(
					'table' => 'companies',
					'where' => array('companies.id = jobs.company_id AND companies.isAgree = 1')
				),
				array(
					'type' => 'left',
					'table' => 'job_skills',
					'where' => array('job_skills.job_id = jobs.id')
				),
				array(
					'type' => 'left',
					'table' => 'skills',
					'where' => array('skills.id = job_skills.skill_id')
				),
				array(
					'type' => 'left',
					'table' => 'job_apply',
					'where' => array('job_apply.job_id = jobs.id AND job_apply.user_id = ?', $user->id)
				),
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = companies.user_id')
				)
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'jobapplyUserId');
		return $result;
	}


	public static function setActivate($job_id, $countDays)
	{
		$result = self::update(array(
			'activateDate' => date('Y-m-d H:m:i', time()),
			'expiredDate' => date('Y-m-d H:m:i', time() + (60*60*24*$countDays))
		), $job_id);

		Model_User::addUserIdByKey($result, 'user_id');
		return $result;
	}

	public static function getCountActiveJobs()
	{
		$result = new self(array(
			'select' => 'COUNT(id) as countJobs',
			'where' => array('expiredDate >= ? AND isRemoved = 0', date('Y-m-d H:m:i'))
		));
		return $result->countJobs;
	}

	public static function getCountJobs()
	{
		$result =  new self(array(
			'select' => 'COUNT(id) as countJobs',
			'where' => array('isRemoved = 0')
		));
		return $result->countJobs;
	}

	public static function getJobsYouMayLike($user_id)
	{
		$result = self::getList(array(
			'select' => '
						jobs.*,
						user_city.city AS userCity,
						companies.name as name,
						companies.avaToken as avaToken,
						job_apply.user_id as jobapplyUserId,
						job_apply.isInvited as jobapplyIsInvited,
						job_apply.isRemovedJobApplicant as jobapplyIsRemovedJobApplicant
						',
			'where' => array('
								jobs.expiredDate >= ? AND jobs.isRemoved = 0 AND
								jobs.user_id <> ?
								', date('Y-m-d H:m:i'), $user_id),
			'join' => array(
				array(
					'table' => 'companies',
					'where' => array('companies.id = jobs.company_id AND companies.isAgree = 1')
				),
//				array(
//					'noQuotes' => TRUE,
//					'table' => 'job_skills as job_skills_count',
//					'type' => 'left',
//					'where' => array('job_skills_count.job_id = jobs.id')
//				),
				array(
					'table' => 'job_skills',
					'type' => 'left',
					'where' => array('job_skills.job_id = jobs.id')
				),
				array(
					'table' => 'profile_skills',
					'type' => 'left',
					'where' => array('profile_skills.skill_id = job_skills.skill_id AND profile_skills.user_id = ?', $user_id)
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'users as user',
					'type' => 'left',
					'where' => array('user.id = ? AND (user.country = jobs.country OR user.country = "")', $user_id)
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'users as user_city',
					'type' => 'left',
					'where' => array('user_city.id = ? AND user_city.city = jobs.city', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'job_apply',
					'where' => array('job_apply.job_id = jobs.id AND job_apply.user_id = ?', $user_id)
				)
			),
			'group' => 'user.country DESC, user_city.city DESC, jobs.id, profile_skills.skill_id DESC,  job_skills.job_id',
			'limit' => 5
		));
		return $result;
	}

    public function getData(){
           return $this->data;
	}

}